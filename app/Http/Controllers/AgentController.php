<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\AgentLevel;
use App\Models\AuditLog;
use App\Models\CommissionLedger;
use App\Models\ProspectJemaah;
use App\Services\AgentLevelingService;
use App\Services\DukcapilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    protected $dukcapilService;
    protected $levelingService;

    public function __construct(DukcapilService $dukcapilService, AgentLevelingService $levelingService)
    {
        $this->dukcapilService = $dukcapilService;
        $this->levelingService = $levelingService;
    }

    /**
     * AJAX endpoint to verify NIK with Dukcapil KYC.
     */
    public function verifyNik(Request $request)
    {
        $request->validate(['nik' => 'required|digits:16']);
        
        $result = $this->dukcapilService->verifyNIK($request->nik);

        if ($result['success'] ?? false) {
            // Generate mock verified data to auto-fill
            return response()->json([
                'success' => true,
                'message' => 'NIK Terverifikasi dengan Dukcapil.',
                'data' => [
                    'name' => 'Jemaah Hasil Verifikasi NIK ' . substr($request->nik, -4),
                    'address' => 'Jl. Haji Raya No. ' . rand(1, 100) . ', DKI Jakarta'
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'NIK tidak ditemukan di database Dukcapil.'
        ], 422);
    }

    /**
     * Submit a new prospect pilgrim.
     */
    public function storeProspect(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|digits:16|unique:prospect_jemaahs,nik',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric',
            'registration_type' => 'required|string',
            'status_pendaftaran' => 'required|string',
            'bps_bpih' => 'nullable|string',
            'claim_status' => 'nullable|string',
            'porsi_number' => 'nullable|string',
            'ktp_photo' => 'nullable|file|image|max:2048',
            'saving_book_photo' => 'nullable|file|image|max:2048',
            'npwp_photo' => 'nullable|file|image|max:2048',
        ]);

        $agent = Agent::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', Auth::id())
            ->first();

        if (!$agent || $agent->status !== 'active') {
            return back()->with('error', 'Status Agen tidak aktif. Pendaftaran ditolak.');
        }

        $ktpPath = null;
        if ($request->hasFile('ktp_photo')) {
            $ktpPath = $request->file('ktp_photo')->store('prospects/ktp', 'public');
        }
        $savingBookPath = null;
        if ($request->hasFile('saving_book_photo')) {
            $savingBookPath = $request->file('saving_book_photo')->store('prospects/saving_books', 'public');
        }
        $npwpPath = null;
        if ($request->hasFile('npwp_photo')) {
            $npwpPath = $request->file('npwp_photo')->store('prospects/npwp', 'public');
        }

        DB::transaction(function () use ($data, $agent, $ktpPath, $savingBookPath, $npwpPath) {
            $prospect = ProspectJemaah::create([
                'agent_id' => $agent->id,
                'name' => $data['name'],
                'nik' => $data['nik'],
                'address' => $data['address'],
                'location_lat' => $data['location_lat'] ?? null,
                'location_lng' => $data['location_lng'] ?? null,
                'ktp_photo_path' => $ktpPath,
                'saving_book_photo_path' => $savingBookPath,
                'npwp_photo_path' => $npwpPath,
                'phone_number' => $data['phone_number'] ?? '',
                'email' => $data['email'],
                'registration_type' => $data['registration_type'],
                'status_pendaftaran' => $data['status_pendaftaran'],
                'bps_bpih' => $data['bps_bpih'] ?? '-',
                'claim_status' => $data['claim_status'] ?? '-',
                'porsi_number' => $data['porsi_number'] ?? '-',
                'verified_at' => $data['status_pendaftaran'] === 'Pendaftar Haji' ? now() : null,
            ]);

            if ($data['status_pendaftaran'] === 'Pendaftar Haji') {
                $this->levelingService->creditCommissionForProspect($prospect);
                $this->levelingService->evaluateAgentLevel($agent);
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'register_prospect_jemaah',
                'model_type' => ProspectJemaah::class,
                'model_id' => $prospect->id,
                'ip_address' => request()->ip(),
                'before_payload' => [],
                'after_payload' => $prospect->toArray()
            ]);
        });

        return back()->with('success', 'Calon Jemaah Haji berhasil didaftarkan.');
    }

    /**
     * Update an existing pilgrim.
     */
    public function updateProspect(ProspectJemaah $prospect, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric',
            'registration_type' => 'required|string',
            'status_pendaftaran' => 'required|string',
            'bps_bpih' => 'nullable|string',
            'claim_status' => 'nullable|string',
            'porsi_number' => 'nullable|string',
            'ktp_photo' => 'nullable|file|image|max:2048',
            'saving_book_photo' => 'nullable|file|image|max:2048',
            'npwp_photo' => 'nullable|file|image|max:2048',
        ]);

        $oldStatus = $prospect->status_pendaftaran;
        $agent = $prospect->agent;

        DB::transaction(function () use ($prospect, $data, $oldStatus, $agent, $request) {
            $updateData = [
                'name' => $data['name'],
                'address' => $data['address'],
                'location_lat' => $data['location_lat'] ?? null,
                'location_lng' => $data['location_lng'] ?? null,
                'phone_number' => $data['phone_number'] ?? '',
                'email' => $data['email'],
                'registration_type' => $data['registration_type'],
                'status_pendaftaran' => $data['status_pendaftaran'],
                'bps_bpih' => $data['bps_bpih'] ?? '-',
                'claim_status' => $data['claim_status'] ?? '-',
                'porsi_number' => $data['porsi_number'] ?? '-',
            ];

            if ($request->hasFile('ktp_photo')) {
                $updateData['ktp_photo_path'] = $request->file('ktp_photo')->store('prospects/ktp', 'public');
            }
            if ($request->hasFile('saving_book_photo')) {
                $updateData['saving_book_photo_path'] = $request->file('saving_book_photo')->store('prospects/saving_books', 'public');
            }
            if ($request->hasFile('npwp_photo')) {
                $updateData['npwp_photo_path'] = $request->file('npwp_photo')->store('prospects/npwp', 'public');
            }

            $prospect->update($updateData);

            // If status changed to Pendaftar Haji, trigger commission & level promotion
            if ($oldStatus !== 'Pendaftar Haji' && $data['status_pendaftaran'] === 'Pendaftar Haji') {
                $prospect->update(['verified_at' => now()]);
                $this->levelingService->creditCommissionForProspect($prospect);
                
                if ($agent) {
                    $this->levelingService->evaluateAgentLevel($agent);
                }
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'update_prospect_jemaah',
                'model_type' => ProspectJemaah::class,
                'model_id' => $prospect->id,
                'ip_address' => request()->ip(),
                'before_payload' => ['status' => $oldStatus],
                'after_payload' => $prospect->toArray()
            ]);
        });

        return back()->with('success', 'Data Jemaah berhasil diperbarui.');
    }

    /**
     * Delete a pilgrim.
     */
    public function deleteProspect(ProspectJemaah $prospect)
    {
        DB::transaction(function () use ($prospect) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete_prospect_jemaah',
                'model_type' => ProspectJemaah::class,
                'model_id' => $prospect->id,
                'ip_address' => request()->ip(),
                'before_payload' => $prospect->toArray(),
                'after_payload' => []
            ]);

            $prospect->delete();
        });

        return back()->with('success', 'Data Jemaah berhasil dihapus.');
    }

    /**
     * Load B2B Institution Agent dashboard.
     */
    public function institutionDashboard()
    {
        $agent = Agent::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', Auth::id())
            ->first();

        if (!$agent->is_ktp_verified && !$agent->is_submitted) {
            return redirect()->route('agent.verification.wizard');
        }

        // Active Sub-agents registered under the same institution
        $subAgents = Agent::where('institution_id', $agent->institution_id)
            ->where('id', '!=', $agent->id)
            ->where('status', 'active')
            ->with(['user', 'level'])
            ->withCount('prospects')
            ->get();

        // Pending Sub-agents waiting for approval
        $pendingAgents = Agent::where('institution_id', $agent->institution_id)
            ->where('id', '!=', $agent->id)
            ->where('status', 'pending')
            ->where('is_submitted', true)
            ->with(['user', 'level'])
            ->get();

        // Filter parameters
        $filters = [
            'status' => request('status'),
            'search_name' => request('search_name'),
            'search_bank' => request('search_bank'),
        ];

        // Prospects list is automatically filtered by TenantScope (own + sub-agents)
        $prospectsRepo = new \App\Repositories\ProspectRepository();
        $prospects = $prospectsRepo->getFilteredAndPaginated($filters, 10);

        // All prospects for counts & chart stats
        $allProspects = ProspectJemaah::all(); // Scoped by TenantScope

        // Financial ledgers filtered by TenantScope (own + sub-agents)
        $ledgers = CommissionLedger::with('prospectJemaah')->orderBy('created_at', 'desc')->get();

        $credits = CommissionLedger::where('type', 'credit')->sum('amount');
        $debits = CommissionLedger::where('type', 'debit')->whereIn('status', ['approved', 'disbursed'])->sum('amount');
        $balance = $credits - $debits;

        // Count of verified prospects (Pendaftar Haji)
        $verifiedCount = ProspectJemaah::whereIn('status_pendaftaran', ['Verified', 'Pendaftar Haji'])->count();

        // Group by bank for charts (agent own + sub-agent collective)
        $agentBankStats = ProspectJemaah::whereIn('status_pendaftaran', ['Verified', 'Pendaftar Haji'])
            ->groupBy('bps_bpih')
            ->select('bps_bpih', DB::raw('count(*) as count'))
            ->orderBy('count', 'desc')
            ->pluck('count', 'bps_bpih')
            ->toArray();

        // Group by province for charts
        $provinces = ['DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Banten', 'DI Yogyakarta'];
        $agentProvinceStats = [];
        foreach ($provinces as $prov) {
            $agentProvinceStats[$prov] = ProspectJemaah::whereIn('status_pendaftaran', ['Verified', 'Pendaftar Haji'])
                ->where('address', 'like', '%' . $prov . '%')
                ->count();
        }

        // Gamification & Incentive Data
        $gamificationService = app(\App\Services\GamificationService::class);
        $activeReferralProgram = $gamificationService->getActiveReferralProgram();
        $agentPoints = $gamificationService->getAgentTotalPoints($agent->id);
        $pointLedgers = \App\Models\AgentPointLedger::where('agent_id', $agent->id)->orderBy('created_at', 'desc')->take(20)->get();
        $racingLeaderboard = $gamificationService->getRacingLeaderboard(null, $agent);

        $incentiveService = app(\App\Services\IncentiveService::class);
        $selectedMonth = intval(request('month', date('n')));
        $selectedYear = intval(request('year', date('Y')));
        $monthlyIncentive = $incentiveService->calculateMonthlyAgentIncentive($agent->id, $selectedMonth, $selectedYear);

        // Fetch all referral and racing programs with agent performance
        $referralPrograms = \App\Models\ReferralProgram::orderBy('start_date', 'desc')->get();
        $referralProgramsData = $referralPrograms->map(function ($program) use ($agent) {
            $prospectsCount = ProspectJemaah::withoutGlobalScopes()
                ->where('agent_id', $agent->id)
                ->whereDate('created_at', '>=', $program->start_date)
                ->whereDate('created_at', '<=', $program->end_date)
                ->count();
            $program->agent_prospects_count = $prospectsCount;
            return $program;
        });

        $racingPrograms = \App\Models\RacingProgram::orderBy('start_date', 'desc')->get();
        $racingProgramsData = $racingPrograms->map(function ($program) use ($agent, $gamificationService) {
            $leaderboardInfo = $gamificationService->getRacingLeaderboard($program, $agent);
            $program->agent_rank = $leaderboardInfo['current_agent_rank'] ?? null;
            $program->leaderboard_data = $leaderboardInfo['leaderboard'] ?? [];
            return $program;
        });

        $stats = [
            'sub_agents_count' => $subAgents->count(),
            'pending_agents_count' => $pendingAgents->count(),
            'total_prospects' => $allProspects->count(),
            'verified_prospects' => $verifiedCount,
            'total_commission' => $credits,
            'balance' => $balance,
            'bank_stats' => $agentBankStats,
            'province_stats' => $agentProvinceStats,
            'agent_points' => $agentPoints,
        ];

        return view('agent.institution', compact('agent', 'subAgents', 'pendingAgents', 'prospects', 'ledgers', 'stats', 'activeReferralProgram', 'agentPoints', 'pointLedgers', 'racingLeaderboard', 'monthlyIncentive', 'referralProgramsData', 'racingProgramsData'));
    }

    /**
     * Verify/Approve or Reject a B2B employee agent.
     */
    public function verifyInstitutionAgent(Request $request, $agent_id)
    {
        $adminAgent = Agent::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($adminAgent->type !== 'institution' || !$adminAgent->is_institution_admin) {
            abort(403, 'Hanya administrator institusi yang dapat memverifikasi agen.');
        }

        $targetAgent = Agent::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('id', $agent_id)
            ->where('institution_id', $adminAgent->institution_id)
            ->firstOrFail();

        $action = $request->input('action');

        if ($action === 'approve') {
            $targetAgent->update([
                'status' => 'active',
                'is_ktp_verified' => true,
                'is_email_verified' => true,
                'is_whatsapp_verified' => true,
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'b2b_approve_agent',
                'model_type' => Agent::class,
                'model_id' => $targetAgent->id,
                'ip_address' => $request->ip(),
                'before_payload' => ['status' => 'pending'],
                'after_payload' => $targetAgent->toArray(),
            ]);

            return back()->with('success', "Agen {$targetAgent->user->name} berhasil disetujui dan aktif.");
        } elseif ($action === 'reject') {
            $targetAgent->update([
                'status' => 'suspended',
                'is_submitted' => false,
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'b2b_reject_agent',
                'model_type' => Agent::class,
                'model_id' => $targetAgent->id,
                'ip_address' => $request->ip(),
                'before_payload' => ['status' => 'pending'],
                'after_payload' => $targetAgent->toArray(),
            ]);

            return back()->with('success', "Pendaftaran Agen {$targetAgent->user->name} ditolak.");
        }

        return back()->with('error', 'Aksi tidak valid.');
    }

    /**
     * Load Freelance Agent dashboard.
     */
    public function freelanceDashboard()
    {
        $agent = Agent::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', Auth::id())
            ->first();

        if (!$agent->is_ktp_verified && !$agent->is_submitted) {
            return redirect()->route('agent.verification.wizard');
        }

        // Filter parameters
        $filters = [
            'status' => request('status'),
            'search_name' => request('search_name'),
            'search_bank' => request('search_bank'),
        ];

        // Prospects list is automatically filtered by TenantScope (own prospects)
        $prospectsRepo = new \App\Repositories\ProspectRepository();
        $prospects = $prospectsRepo->getFilteredAndPaginated($filters, 10);

        // All prospects for count stats
        $allProspects = ProspectJemaah::all();

        $ledgers = CommissionLedger::with('prospectJemaah')->orderBy('created_at', 'desc')->get();

        $credits = CommissionLedger::where('type', 'credit')->sum('amount');
        $debits = CommissionLedger::where('type', 'debit')->whereIn('status', ['approved', 'disbursed'])->sum('amount');
        $balance = $credits - $debits;

        // Calculate progress to next level
        $currentLevel = $agent->level;
        $nextLevel = AgentLevel::where('target_prospects', '>', $currentLevel->target_prospects)
            ->orderBy('target_prospects', 'asc')
            ->first();

        $verifiedCount = ProspectJemaah::whereIn('status_pendaftaran', ['Verified', 'Pendaftar Haji'])->count(); // Auto-scoped!

        $progressPercent = 100;
        $targetText = "Maksimum (Tingkat Tertinggi)";
        $nextLevelTarget = 0;
        if ($nextLevel) {
            $nextLevelTarget = $nextLevel->target_prospects;
            $targetText = "{$nextLevel->name} ({$nextLevel->target_prospects} Jemaah)";
            $progressPercent = min(100, round(($verifiedCount / $nextLevel->target_prospects) * 100));
        }

        // Group by bank for charts (agent own)
        $agentBankStats = ProspectJemaah::whereIn('status_pendaftaran', ['Verified', 'Pendaftar Haji'])
            ->groupBy('bps_bpih')
            ->select('bps_bpih', DB::raw('count(*) as count'))
            ->orderBy('count', 'desc')
            ->pluck('count', 'bps_bpih')
            ->toArray();

        // Group by province for charts (from address)
        $provinces = ['DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Banten', 'DI Yogyakarta'];
        $agentProvinceStats = [];
        foreach ($provinces as $prov) {
            $agentProvinceStats[$prov] = ProspectJemaah::whereIn('status_pendaftaran', ['Verified', 'Pendaftar Haji'])
                ->where('address', 'like', '%' . $prov . '%')
                ->count();
        }

        // Gamification & Incentive Data
        $gamificationService = app(\App\Services\GamificationService::class);
        $activeReferralProgram = $gamificationService->getActiveReferralProgram();
        $agentPoints = $gamificationService->getAgentTotalPoints($agent->id);
        $pointLedgers = \App\Models\AgentPointLedger::where('agent_id', $agent->id)->orderBy('created_at', 'desc')->take(20)->get();
        $racingLeaderboard = $gamificationService->getRacingLeaderboard(null, $agent);

        $incentiveService = app(\App\Services\IncentiveService::class);
        $selectedMonth = intval(request('month', date('n')));
        $selectedYear = intval(request('year', date('Y')));
        $monthlyIncentive = $incentiveService->calculateMonthlyAgentIncentive($agent->id, $selectedMonth, $selectedYear);

        // Fetch all referral and racing programs with agent performance
        $referralPrograms = \App\Models\ReferralProgram::orderBy('start_date', 'desc')->get();
        $referralProgramsData = $referralPrograms->map(function ($program) use ($agent) {
            $prospectsCount = ProspectJemaah::withoutGlobalScopes()
                ->where('agent_id', $agent->id)
                ->whereDate('created_at', '>=', $program->start_date)
                ->whereDate('created_at', '<=', $program->end_date)
                ->count();
            $program->agent_prospects_count = $prospectsCount;
            return $program;
        });

        $racingPrograms = \App\Models\RacingProgram::orderBy('start_date', 'desc')->get();
        $racingProgramsData = $racingPrograms->map(function ($program) use ($agent, $gamificationService) {
            $leaderboardInfo = $gamificationService->getRacingLeaderboard($program, $agent);
            $program->agent_rank = $leaderboardInfo['current_agent_rank'] ?? null;
            $program->leaderboard_data = $leaderboardInfo['leaderboard'] ?? [];
            return $program;
        });

        $stats = [
            'total_prospects' => $allProspects->count(),
            'verified_prospects' => $verifiedCount,
            'total_commission' => $credits,
            'balance' => $balance,
            'next_level' => $targetText,
            'next_level_target' => $nextLevelTarget,
            'progress_percent' => $progressPercent,
            'bank_stats' => $agentBankStats,
            'province_stats' => $agentProvinceStats,
            'agent_points' => $agentPoints,
        ];

        return view('agent.freelance', compact('agent', 'prospects', 'ledgers', 'stats', 'activeReferralProgram', 'agentPoints', 'pointLedgers', 'racingLeaderboard', 'monthlyIncentive', 'referralProgramsData', 'racingProgramsData'));
    }

    /**
     * Show Agent Profile based on referral code.
     */
    public function showProfileByReferral($referral_code)
    {
        $agent = Agent::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('referral_code', $referral_code)
            ->with(['user', 'level', 'institution'])
            ->firstOrFail();

        $user = Auth::user();

        // Access Control checks
        $canAccess = false;
        if ($user->role === 'superadmin' || $user->role === 'admin_haji') {
            $canAccess = true;
        } elseif ($user->agent && $user->agent->id === $agent->id) {
            $canAccess = true;
        } elseif ($user->agent && $user->agent->type === 'institution' && $agent->institution_id === $user->agent->institution_id) {
            $canAccess = true;
        }

        if (!$canAccess) {
            abort(403, 'Anda tidak memiliki akses untuk melihat profil agen ini.');
        }

        return view('agent.profile', compact('agent'));
    }

    /**
     * Show the agent verification wizard.
     */
    public function showVerificationWizard()
    {
        $agent = Agent::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($agent->is_ktp_verified) {
            return redirect()->route('dashboard');
        }

        return view('agent.wizard', compact('agent'));
    }

    /**
     * Submit verification details from the wizard.
     */
    public function submitVerificationWizard(Request $request)
    {
        $agent = Agent::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($agent->is_ktp_verified) {
            return redirect()->route('dashboard');
        }

        $rules = [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'string'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat_ktp' => ['required', 'string'],
            'provinsi_ktp' => ['required', 'string'],
            'kota_ktp' => ['required', 'string'],
            'kecamatan_ktp' => ['required', 'string'],
            'kelurahan_ktp' => ['required', 'string'],
            'alamat_tinggal' => ['required', 'string'],
            'provinsi_tinggal' => ['required', 'string'],
            'kota_tinggal' => ['required', 'string'],
            'kecamatan_tinggal' => ['required', 'string'],
            'kelurahan_tinggal' => ['required', 'string'],
            'nama_bank' => ['required', 'string'],
            'cabang_bank' => ['required', 'string'],
            'nomor_rekening' => ['required', 'string'],
            'nomor_npwp' => ['required', 'string'],
        ];

        $isEmployee = ($agent->type === 'institution' && !$agent->is_institution_admin);
        if ($isEmployee) {
            $rules['nip'] = ['required', 'string', 'max:50'];
        }

        $uploadFields = ['foto_ktp', 'foto_bangunan', 'foto_diri', 'foto_pakta_integritas', 'foto_buku_tabungan', 'foto_npwp'];
        if ($isEmployee) {
            $uploadFields[] = 'bukti_pekerja';
            $uploadFields[] = 'sk_pengangkatan';
        }

        foreach ($uploadFields as $field) {
            $isImageOnly = in_array($field, ['foto_bangunan', 'foto_diri']);
            $mimes = $isImageOnly ? 'jpeg,jpg,png,webp' : 'jpeg,jpg,png,webp,pdf';
            $requiredRule = $agent->$field ? 'nullable' : 'required';
            $rules[$field] = [$requiredRule, 'file', 'mimes:' . $mimes, 'max:3072'];
        }

        $request->validate($rules);

        $paths = [];
        foreach ($uploadFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $filename);
                $paths[$field] = 'uploads/' . $filename;
            } else {
                $paths[$field] = $agent->$field ?? 'uploads/demo_' . $field . '.png';
            }
        }

        DB::transaction(function () use ($request, $agent, $paths, $isEmployee) {
            $updateData = [
                'full_name' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'birth_date' => $request->tanggal_lahir,
                'alamat_ktp' => $request->alamat_ktp,
                'provinsi_ktp' => $request->provinsi_ktp,
                'kota_ktp' => $request->kota_ktp,
                'kecamatan_ktp' => $request->kecamatan_ktp,
                'kelurahan_ktp' => $request->kelurahan_ktp,
                'alamat_tinggal' => $request->alamat_tinggal,
                'provinsi_tinggal' => $request->provinsi_tinggal,
                'kota_tinggal' => $request->kota_tinggal,
                'kecamatan_tinggal' => $request->kecamatan_tinggal,
                'kelurahan_tinggal' => $request->kelurahan_tinggal,
                'latitude_tinggal' => (function() use ($request) {
                    $lat = -6.200000;
                    $query = "{$request->alamat_tinggal}, {$request->kelurahan_tinggal}, {$request->kecamatan_tinggal}, {$request->kota_tinggal}, {$request->provinsi_tinggal}, Indonesia";
                    $fallback = "{$request->kelurahan_tinggal}, {$request->kecamatan_tinggal}, {$request->kota_tinggal}, {$request->provinsi_tinggal}, Indonesia";
                    $opts = ['http' => ['method' => 'GET', 'header' => "User-Agent: BPKHApp/1.0\r\nAccept: application/json\r\n"]];
                    $context = stream_context_create($opts);
                    $res = @file_get_contents("https://nominatim.openstreetmap.org/search?q=" . urlencode($query) . "&format=json&limit=1", false, $context);
                    $data = $res ? json_decode($res, true) : null;
                    if (empty($data)) {
                        $res = @file_get_contents("https://nominatim.openstreetmap.org/search?q=" . urlencode($fallback) . "&format=json&limit=1", false, $context);
                        $data = $res ? json_decode($res, true) : null;
                    }
                    return (!empty($data) && isset($data[0]['lat'])) ? (float)$data[0]['lat'] : (float)($request->latitude_tinggal ?? -6.200000);
                })(),
                'longitude_tinggal' => (function() use ($request) {
                    $lng = 106.816666;
                    $query = "{$request->alamat_tinggal}, {$request->kelurahan_tinggal}, {$request->kecamatan_tinggal}, {$request->kota_tinggal}, {$request->provinsi_tinggal}, Indonesia";
                    $fallback = "{$request->kelurahan_tinggal}, {$request->kecamatan_tinggal}, {$request->kota_tinggal}, {$request->provinsi_tinggal}, Indonesia";
                    $opts = ['http' => ['method' => 'GET', 'header' => "User-Agent: BPKHApp/1.0\r\nAccept: application/json\r\n"]];
                    $context = stream_context_create($opts);
                    $res = @file_get_contents("https://nominatim.openstreetmap.org/search?q=" . urlencode($query) . "&format=json&limit=1", false, $context);
                    $data = $res ? json_decode($res, true) : null;
                    if (empty($data)) {
                        $res = @file_get_contents("https://nominatim.openstreetmap.org/search?q=" . urlencode($fallback) . "&format=json&limit=1", false, $context);
                        $data = $res ? json_decode($res, true) : null;
                    }
                    return (!empty($data) && isset($data[0]['lon'])) ? (float)$data[0]['lon'] : (float)($request->longitude_tinggal ?? 106.816666);
                })(),
                'foto_ktp' => $paths['foto_ktp'],
                'foto_bangunan' => $paths['foto_bangunan'],
                'foto_diri' => $paths['foto_diri'],
                'foto_pakta_integritas' => $paths['foto_pakta_integritas'],
                'foto_buku_tabungan' => $paths['foto_buku_tabungan'],
                'nama_bank' => $request->nama_bank,
                'cabang_bank' => $request->cabang_bank,
                'nomor_rekening' => $request->nomor_rekening,
                'foto_npwp' => $paths['foto_npwp'],
                'nomor_npwp' => $request->nomor_npwp,
                'is_submitted' => true,
                'rejection_reason' => null,
            ];

            if ($isEmployee) {
                $updateData['nip'] = $request->nip;
                $updateData['bukti_pekerja'] = $paths['bukti_pekerja'];
                $updateData['sk_pengangkatan'] = $paths['sk_pengangkatan'];
            }

            $agent->update($updateData);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'submit_agent_verification_wizard',
                'model_type' => Agent::class,
                'model_id' => $agent->id,
                'ip_address' => $request->ip(),
                'before_payload' => [],
                'after_payload' => $agent->toArray(),
            ]);
        });

        $msg = $isEmployee
            ? 'Dokumen dan informasi berhasil dikirim! Silakan tunggu verifikasi oleh admin institusi Anda.'
            : 'Dokumen dan informasi berhasil dikirim! Silakan tunggu verifikasi oleh admin BPKH.';

        return redirect()->route('dashboard')->with('success', $msg);
    }

    /**
     * Download CSV template.
     */
    public function downloadImportTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_jemaah.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Write BOM for Excel UTF-8 compliance
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'nik',
                'name',
                'address',
                'phone_number',
                'email',
                'registration_type',
                'bps_bpih',
                'claim_status',
                'porsi_number',
                'location_lat',
                'location_lng'
            ], ';'); // Use semicolon as default for Excel-friendly Indonesian locale CSV
            
            // Example row 1
            fputcsv($file, [
                '3171012345670001',
                'Ahmad Subagja',
                'Jl. Kebon Jeruk No. 12, Jakarta',
                '08123456789',
                'ahmad@example.com',
                'Reguler',
                'Bank Syariah Indonesia',
                'Disetujui',
                '1234567890',
                '-6.20880000',
                '106.84560000'
            ], ';');

            // Example row 2
            fputcsv($file, [
                '3171012345670002',
                'Siti Aminah',
                'Jl. Mawar No. 45, Bandung',
                '08139876543',
                'siti@example.com',
                'Khusus',
                'Bank Muamalat Indonesia',
                'Disetujui',
                '1234567891',
                '-6.91750000',
                '107.61910000'
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Validate uploaded CSV file and return JSON preview.
     */
    public function validateImportProspects(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $rows = [];
        $validCount = 0;
        $invalidCount = 0;

        if (($handle = fopen($path, 'r')) !== false) {
            // Read first line to detect delimiter
            $firstLine = fgets($handle);
            rewind($handle);

            $delimiter = ',';
            if (strpos($firstLine, ';') !== false) {
                $delimiter = ';';
            }

            // Skip UTF-8 BOM if present
            $bom = pack('CCC', 0xEF, 0xBB, 0xBF);
            if (fgets($handle, 4) !== $bom) {
                rewind($handle);
            }

            // Headers
            $headers = fgetcsv($handle, 0, $delimiter);
            
            // Clean headers (remove spaces or quotes)
            if ($headers) {
                $headers = array_map(function($h) {
                    return trim(strtolower($h), " \t\n\r\0\x0B\"");
                }, $headers);
            }

            // Map header indexes
            $expectedHeaders = [
                'nik', 'name', 'address', 'phone_number', 'email', 
                'registration_type', 'bps_bpih', 'claim_status', 
                'porsi_number', 'location_lat', 'location_lng'
            ];

            // If header format doesn't match at least nik and name
            if (!$headers || !in_array('nik', $headers) || !in_array('name', $headers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format file template tidak sesuai. Pastikan kolom "nik" dan "name" tersedia.'
                ], 422);
            }

            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                // Skip empty lines
                if (empty($data) || (count($data) === 1 && $data[0] === null)) {
                    continue;
                }

                // Map data to header keys
                $row = [];
                foreach ($headers as $index => $header) {
                    if (isset($data[$index])) {
                        $row[$header] = trim($data[$index]);
                    } else {
                        $row[$header] = '';
                    }
                }

                // Check other expected columns
                foreach ($expectedHeaders as $eh) {
                    if (!isset($row[$eh])) {
                        $row[$eh] = '';
                    }
                }

                // Validate individual row
                $errors = [];

                // 1. NIK
                if (empty($row['nik'])) {
                    $errors[] = 'NIK wajib diisi.';
                } elseif (strlen($row['nik']) !== 16 || !is_numeric($row['nik'])) {
                    $errors[] = 'NIK harus 16 digit angka.';
                }

                // 2. Name
                if (empty($row['name'])) {
                    $errors[] = 'Nama wajib diisi.';
                }

                // 3. Address
                if (empty($row['address'])) {
                    $errors[] = 'Alamat wajib diisi.';
                }

                // 4. Phone number
                if (empty($row['phone_number'])) {
                    $errors[] = 'Nomor telepon wajib diisi.';
                }

                // 5. Email
                if (!empty($row['email']) && !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Format email tidak valid.';
                }

                // 6. Registration type
                if (!empty($row['registration_type']) && !in_array($row['registration_type'], ['Reguler', 'Khusus', '-'])) {
                    $errors[] = 'Tipe pendaftaran harus: Reguler, Khusus, atau -.';
                }

                // 7. Claim Status
                if (!empty($row['claim_status']) && !in_array($row['claim_status'], ['Disetujui', 'Ditolak', '-'])) {
                    $errors[] = 'Status klaim harus: Disetujui, Ditolak, atau -.';
                }

                // 8. Latitude & Longitude
                if (!empty($row['location_lat'])) {
                    if (!is_numeric($row['location_lat']) || $row['location_lat'] < -90 || $row['location_lat'] > 90) {
                        $errors[] = 'Latitude harus di antara -90 dan 90.';
                    }
                }
                if (!empty($row['location_lng'])) {
                    if (!is_numeric($row['location_lng']) || $row['location_lng'] < -180 || $row['location_lng'] > 180) {
                        $errors[] = 'Longitude harus di antara -180 dan 180.';
                    }
                }

                $isValid = empty($errors);
                if ($isValid) {
                    $validCount++;
                } else {
                    $invalidCount++;
                }

                $rows[] = [
                    'data' => $row,
                    'errors' => $errors,
                    'is_valid' => $isValid
                ];
            }
            fclose($handle);
        }

        return response()->json([
            'success' => true,
            'valid_count' => $validCount,
            'invalid_count' => $invalidCount,
            'rows' => $rows
        ]);
    }

    /**
     * Process and save the validated prospects.
     */
    public function processImportProspects(Request $request)
    {
        $request->validate([
            'prospects' => 'required|array',
            'prospects.*.nik' => 'required|digits:16',
            'prospects.*.name' => 'required|string',
            'prospects.*.address' => 'required|string',
            'prospects.*.phone_number' => 'required|string',
        ]);

        $agent = Agent::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', Auth::id())
            ->first();

        if (!$agent) {
            return response()->json(['success' => false, 'message' => 'Agent tidak terotentikasi.'], 403);
        }

        $importedCount = 0;
        
        DB::transaction(function () use ($request, $agent, &$importedCount) {
            foreach ($request->prospects as $p) {
                // Check if NIK already exists
                $prospect = ProspectJemaah::withoutGlobalScope(\App\Scopes\TenantScope::class)
                    ->where('nik', $p['nik'])
                    ->first();

                $oldStatus = null;
                $isNew = false;
                
                if (!$prospect) {
                    $prospect = new ProspectJemaah();
                    $prospect->nik = $p['nik'];
                    $prospect->agent_id = $agent->id;
                    $isNew = true;
                } else {
                    $oldStatus = $prospect->status_pendaftaran;
                    $prospect->agent_id = $agent->id;
                }

                $prospect->name = $p['name'];
                $prospect->address = $p['address'];
                $prospect->phone_number = $p['phone_number'];
                $prospect->email = !empty($p['email']) ? $p['email'] : null;
                $prospect->registration_type = !empty($p['registration_type']) && $p['registration_type'] !== '-' ? $p['registration_type'] : null;
                $prospect->status_pendaftaran = 'Tertarik Daftar Haji';
                $prospect->bps_bpih = !empty($p['bps_bpih']) && $p['bps_bpih'] !== '-' ? $p['bps_bpih'] : null;
                $prospect->claim_status = !empty($p['claim_status']) && $p['claim_status'] !== '-' ? $p['claim_status'] : '-';
                $prospect->porsi_number = !empty($p['porsi_number']) ? $p['porsi_number'] : null;
                
                if (isset($p['location_lat']) && is_numeric($p['location_lat'])) {
                    $prospect->location_lat = $p['location_lat'];
                }
                if (isset($p['location_lng']) && is_numeric($p['location_lng'])) {
                    $prospect->location_lng = $p['location_lng'];
                }

                $prospect->save();
                $importedCount++;

                // Trigger leveling evaluation & log audit trail
                if ($isNew) {
                    AuditLog::create([
                        'user_id' => Auth::id(),
                        'action' => 'import_prospect_jemaah',
                        'model_type' => ProspectJemaah::class,
                        'model_id' => $prospect->id,
                        'ip_address' => request()->ip(),
                        'before_payload' => [],
                        'after_payload' => $prospect->toArray()
                    ]);
                } else {
                    AuditLog::create([
                        'user_id' => Auth::id(),
                        'action' => 'update_prospect_jemaah_via_import',
                        'model_type' => ProspectJemaah::class,
                        'model_id' => $prospect->id,
                        'ip_address' => request()->ip(),
                        'before_payload' => ['status' => $oldStatus],
                        'after_payload' => $prospect->toArray()
                    ]);
                }

                $this->levelingService->evaluateAgentLevel($agent);
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor {$importedCount} data jemaah.",
            'count' => $importedCount
        ]);
    }
}
