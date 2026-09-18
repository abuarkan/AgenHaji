<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\AgentLevel;
use App\Models\AuditLog;
use App\Models\Institution;
use App\Models\ProspectJemaah;
use App\Models\SystemSetting;
use App\Models\LoginBackground;
use App\Models\ReferralProgram;
use App\Models\RacingProgram;
use App\Services\AgentLevelingService;
use App\Services\GamificationService;
use App\Services\IncentiveService;
use App\Services\SiskehatApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuperadminController extends Controller
{
    /**
     * Show the main admin control panel.
     */
    public function index(Request $request)
    {
        $stats = [
            'total_agents' => Agent::count(),
            'pending_agents' => Agent::where('status', 'pending')->count(),
            'active_agents' => Agent::where('status', 'active')->count(),
            'total_prospects' => ProspectJemaah::count(),
            'total_commission' => \App\Models\CommissionLedger::where('type', 'credit')->sum('amount'),
            'total_disbursed' => \App\Models\CommissionLedger::where('type', 'debit')
                ->whereIn('status', ['approved', 'disbursed'])
                ->sum('amount')
        ];

        $agents = Agent::with(['user', 'level', 'institution'])->orderBy('id')->get();
        $institutions = Institution::with(['agents.user', 'agents.prospects', 'agents.commissionLedgers', 'agents.level'])
            ->withCount('agents')
            ->orderBy('id')
            ->get()
            ->map(function ($inst) {
                $totalJemaah = 0;
                $totalCommission = 0;
                
                $inst->agents->map(function ($agent) use (&$totalJemaah, &$totalCommission) {
                    $agent->prospects_count = $agent->prospects->count();
                    $totalJemaah += $agent->prospects_count;
                    
                    $agent->total_commission = $agent->commissionLedgers
                        ->where('type', 'credit')
                        ->where('status', 'approved')
                        ->sum('amount');
                    $totalCommission += $agent->total_commission;
                    
                    return $agent;
                });
                
                $inst->total_jemaah = $totalJemaah;
                $inst->total_commission = $totalCommission;
                
                return $inst;
            });
        
        $query = ProspectJemaah::with(['agent.user', 'agent.institution'])->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('jenis_haji')) {
            $query->where('registration_type', $request->input('jenis_haji'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        $perPage = intval($request->input('per_page', 10));
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $prospects = $query->paginate($perPage)->withQueryString();
        
        $settings = SystemSetting::all()->pluck('value', 'key')->toArray();
        $settingsRaw = SystemSetting::all();
        $levels = AgentLevel::orderBy('target_prospects', 'asc')->get();
        $auditLogs = AuditLog::with('user')->orderBy('created_at', 'desc')->take(30)->get();
        $loginBackgrounds = LoginBackground::orderBy('id', 'desc')->get();
        $users = \App\Models\User::orderBy('id')->get();

        // Gamification & Incentive Data
        $referralPrograms = ReferralProgram::orderBy('id', 'desc')->get();
        $racingPrograms = RacingProgram::orderBy('id', 'desc')->get();
        
        $gamificationService = app(GamificationService::class);
        $racingLeaderboard = $gamificationService->getRacingLeaderboard();

        $incentiveService = app(IncentiveService::class);
        $incentiveRates = $incentiveService->getIncentiveRates();

        // National Performance Metrics (Kinerja Nasional)
        $topPointsAgents = Agent::with(['user', 'level'])
            ->where('status', 'active')
            ->get()
            ->map(function ($agent) use ($gamificationService) {
                $agent->total_points = $gamificationService->getAgentTotalPoints($agent->id);
                return $agent;
            })
            ->sortByDesc('total_points')
            ->take(10);

        $referralProgramsData = $referralPrograms->map(function ($program) {
            $prospectsCount = ProspectJemaah::withoutGlobalScopes()
                ->whereDate('created_at', '>=', $program->start_date)
                ->whereDate('created_at', '<=', $program->end_date)
                ->count();
            $program->prospects_count = $prospectsCount;
            return $program;
        });

        $racingProgramsData = $racingPrograms->map(function ($program) use ($gamificationService) {
            $leaderboardInfo = $gamificationService->getRacingLeaderboard($program);
            $program->leaderboard_data = $leaderboardInfo['leaderboard'] ?? [];
            return $program;
        });

        return view('superadmin.dashboard', compact(
            'stats', 'agents', 'institutions', 'prospects', 'settings', 
            'settingsRaw', 'levels', 'auditLogs', 'loginBackgrounds', 
            'users', 'referralPrograms', 'racingPrograms', 'racingLeaderboard',
            'incentiveRates', 'topPointsAgents', 'referralProgramsData', 'racingProgramsData'
        ));
    }

    /**
     * Update parameter besaran insentif referral (BPKH Apps & Non-BPKH Apps).
     */
    public function updateIncentiveSettings(Request $request)
    {
        $validated = $request->validate([
            'incentive_bpkh_silver' => 'required|integer|min:0',
            'incentive_bpkh_gold' => 'required|integer|min:0',
            'incentive_bpkh_platinum' => 'required|integer|min:0',
            'incentive_bpkh_diamond' => 'required|integer|min:0',
            'incentive_non_bpkh_silver' => 'required|integer|min:0',
            'incentive_non_bpkh_gold' => 'required|integer|min:0',
            'incentive_non_bpkh_platinum' => 'required|integer|min:0',
            'incentive_non_bpkh_diamond' => 'required|integer|min:0',
        ]);

        foreach ($validated as $key => $val) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => (string)$val, 'type' => 'number', 'group' => 'incentive']
            );
        }

        return redirect()->back()->with('success', 'Parameter besaran insentif referral BPKH Apps & Non-BPKH Apps berhasil diperbarui.');
    }

    /**
     * Trigger SISKEHAT API sync for nominative pilgrim data.
     */
    public function syncSiskehatData(Request $request)
    {
        $siskehatService = app(SiskehatApiService::class);
        $result = $siskehatService->syncNominativePilgrimData();

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Create or update a Referral Program period.
     */
    public function storeReferralProgram(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if (!empty($validated['is_active'])) {
            ReferralProgram::where('is_active', true)->update(['is_active' => false]);
        }

        ReferralProgram::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->back()->with('success', 'Program Referral berhasil disimpan & diperbarui.');
    }

    /**
     * Update point system parameters.
     */
    public function updatePointSettings(Request $request)
    {
        $validated = $request->validate([
            'points_per_reguler' => 'required|integer|min:0',
            'points_per_khusus' => 'required|integer|min:0',
            'points_per_portion' => 'required|integer|min:0',
        ]);

        foreach ($validated as $key => $val) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => (string)$val, 'type' => 'number', 'group' => 'gamification']
            );
        }

        return redirect()->back()->with('success', 'Parameter sistem poin berhasil diperbarui.');
    }

    /**
     * Create or update a Racing Competition Program.
     */
    public function storeRacingProgram(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'announcement_date' => 'nullable|date',
            'min_portion_target' => 'required|integer|min:1',
            'reward_type' => 'nullable|string|max:255',
            'winner_quota' => 'required|integer|min:1',
            'target_bps_bpih' => 'nullable|string|max:255',
            'prize_rank_1' => 'nullable|string|max:255',
            'prize_rank_2' => 'nullable|string|max:255',
            'prize_rank_3' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if (!empty($validated['is_active'])) {
            RacingProgram::where('is_active', true)->update(['is_active' => false]);
        }

        RacingProgram::create([
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'announcement_date' => $validated['announcement_date'] ?? null,
            'min_portion_target' => $validated['min_portion_target'],
            'reward_type' => $validated['reward_type'] ?? 'Paket Umrah',
            'winner_quota' => $validated['winner_quota'] ?? 6,
            'target_bps_bpih' => $validated['target_bps_bpih'] ?? 'Bank Muamalat Indonesia',
            'prize_rank_1' => $validated['prize_rank_1'] ?? null,
            'prize_rank_2' => $validated['prize_rank_2'] ?? null,
            'prize_rank_3' => $validated['prize_rank_3'] ?? null,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->back()->with('success', 'Program Racing Contest Tenaga Pemasaran berhasil dibuat & diaktifkan.');
    }

    /**
     * Update dynamic system settings and private keys.
     */
    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array'
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['settings'] as $key => $value) {
                $setting = SystemSetting::where('key', $key)->first();
                if ($setting) {
                    $oldValue = $setting->value;
                    if ($oldValue !== $value) {
                        $setting->update(['value' => $value]);

                        AuditLog::create([
                            'user_id' => auth()->id(),
                            'action' => 'update_system_setting',
                            'model_type' => SystemSetting::class,
                            'model_id' => $setting->id,
                            'ip_address' => request()->ip(),
                            'before_payload' => ['key' => $key, 'value' => $setting->is_encrypted ? '[ENCRYPTED]' : $oldValue],
                            'after_payload' => ['key' => $key, 'value' => $setting->is_encrypted ? '[ENCRYPTED]' : $value]
                        ]);
                    }
                }
            }
        });

        return redirect()->route('superadmin.index')->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    /**
     * Update agent level rules and commission rates.
     */
    public function updateLevels(Request $request)
    {
        $data = $request->validate([
            'levels' => 'required|array',
            'levels.*.id' => 'required|exists:agent_levels,id',
            'levels.*.target_prospects' => 'required|integer|min:0',
            'levels.*.commission_per_prospect' => 'required|numeric|min:0'
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['levels'] as $levelData) {
                $level = AgentLevel::find($levelData['id']);
                $oldTarget = $level->target_prospects;
                $oldComm = $level->commission_per_prospect;

                if ($oldTarget != $levelData['target_prospects'] || $oldComm != $levelData['commission_per_prospect']) {
                    $level->update([
                        'target_prospects' => $levelData['target_prospects'],
                        'commission_per_prospect' => $levelData['commission_per_prospect']
                    ]);

                    AuditLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'update_level_config',
                        'model_type' => AgentLevel::class,
                        'model_id' => $level->id,
                        'ip_address' => request()->ip(),
                        'before_payload' => ['level' => $level->name, 'target' => $oldTarget, 'commission' => $oldComm],
                        'after_payload' => ['level' => $level->name, 'target' => $levelData['target_prospects'], 'commission' => $levelData['commission_per_prospect']]
                    ]);
                }
            }
        });

        return redirect()->route('superadmin.index')->with('success', 'Konfigurasi level komisi berhasil diperbarui.');
    }

    /**
     * Change agent status (Active / Suspended).
     */
    public function updateAgentStatus(Agent $agent, Request $request)
    {
        $data = $request->validate([
            'status' => 'required|in:active,suspended,pending'
        ]);

        $oldStatus = $agent->status;

        $updateFields = ['status' => $data['status']];
        if ($data['status'] === 'active') {
            $updateFields['is_ktp_verified'] = true;
            $updateFields['is_submitted'] = true;
            $updateFields['is_email_verified'] = true;
            $updateFields['is_whatsapp_verified'] = true;
            $updateFields['rejection_reason'] = null;
        } elseif ($data['status'] === 'pending') {
            $updateFields['is_ktp_verified'] = false;
        }

        $agent->update($updateFields);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_agent_status',
            'model_type' => Agent::class,
            'model_id' => $agent->id,
            'ip_address' => request()->ip(),
            'before_payload' => ['agent_id' => $agent->id, 'status' => $oldStatus],
            'after_payload' => ['agent_id' => $agent->id, 'status' => $data['status']]
        ]);
        
        \Illuminate\Support\Facades\Log::info("NOTIFICATION: Agent {$agent->user->name} status changed to {$data['status']}.");

        return $this->redirectBackWith('success', "Status dan verifikasi agen {$agent->user->name} berhasil diperbarui.");
    }

    /**
     * Reject agent verification and request resubmission.
     */
    public function rejectAgent(Agent $agent, Request $request)
    {
        $data = $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $agent->update([
            'status' => 'pending',
            'is_submitted' => false,
            'is_ktp_verified' => false,
            'rejection_reason' => $data['rejection_reason']
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'reject_agent',
            'model_type' => Agent::class,
            'model_id' => $agent->id,
            'ip_address' => request()->ip(),
            'before_payload' => ['agent_id' => $agent->id, 'status' => $agent->status],
            'after_payload' => ['agent_id' => $agent->id, 'status' => 'pending', 'rejection_reason' => $data['rejection_reason']]
        ]);

        \Illuminate\Support\Facades\Log::info("NOTIFICATION: Agent {$agent->user->name} verification rejected. Reason: {$data['rejection_reason']}");

        return $this->redirectBackWith('success', "Pendaftaran agen {$agent->user->name} berhasil ditolak.");
    }

    /**
     * Run manual evaluation promotion for a single agent.
     */
    public function evaluateAgentLevel(Agent $agent, AgentLevelingService $levelingService)
    {
        $result = $levelingService->evaluateAgentLevel($agent);

        if ($result['level_changed']) {
            $msg = "Evaluasi selesai. Agen {$agent->user->name} dipromosikan dari {$result['old_level']} ke {$result['new_level']}!";
            return $this->redirectBackWith('success', $msg);
        }

        return $this->redirectBackWith('success', "Evaluasi selesai. Tingkat level {$agent->user->name} tetap {$result['new_level']}.");
    }

    /**
     * Change institution status.
     */
    public function updateInstitutionStatus(Institution $institution, Request $request)
    {
        $data = $request->validate([
            'status' => 'required|in:active,suspended'
        ]);

        $oldStatus = $institution->status;

        if ($oldStatus !== $data['status']) {
            $institution->update(['status' => $data['status']]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'update_institution_status',
                'model_type' => Institution::class,
                'model_id' => $institution->id,
                'ip_address' => request()->ip(),
                'before_payload' => ['institution_id' => $institution->id, 'status' => $oldStatus],
                'after_payload' => ['institution_id' => $institution->id, 'status' => $data['status']]
            ]);
        }

        return $this->redirectBackWith('success', "Status institusi {$institution->name} berhasil diubah.");
    }

    /**
     * Helper redirect returning back or fallback to index.
     */
    protected function redirectBackWith($type, $message)
    {
        if (request()->headers->has('referer')) {
            return redirect()->back()->with($type, $message);
        }
        $user = auth()->user();
        if ($user && $user->role === 'admin_haji') {
            return redirect()->route('admin-haji.index')->with($type, $message);
        }
        return redirect()->route('superadmin.index')->with($type, $message);
    }

    /**
     * Upload a new background image for the login screen.
     */
    public function uploadBackground(Request $request)
    {
        $request->validate([
            'background_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/backgrounds'), $filename);
            
            $bg = LoginBackground::create([
                'image_path' => 'uploads/backgrounds/' . $filename,
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'upload_login_background',
                'model_type' => LoginBackground::class,
                'model_id' => $bg->id,
                'ip_address' => request()->ip(),
                'before_payload' => [],
                'after_payload' => ['image_path' => $bg->image_path]
            ]);

            return redirect()->route('superadmin.index')->with('success', 'Gambar latar belakang login baru berhasil diunggah.');
        }

        return redirect()->route('superadmin.index')->with('error', 'Gagal mengunggah gambar latar belakang.');
    }

    /**
     * Delete a background image.
     */
    public function deleteBackground(LoginBackground $background)
    {
        $oldPath = $background->image_path;
        
        // Don't delete the default seeded background file to prevent breaking fallback
        if ($oldPath !== 'uploads/backgrounds/bg_default.png') {
            $fullPath = public_path($oldPath);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        $background->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_login_background',
            'model_type' => LoginBackground::class,
            'model_id' => $background->id,
            'ip_address' => request()->ip(),
            'before_payload' => ['image_path' => $oldPath],
            'after_payload' => []
        ]);

        return redirect()->route('superadmin.index')->with('success', 'Gambar latar belakang login berhasil dihapus.');
    }

    /**
     * Create a new user.
     */
    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:superadmin,admin_haji,agent',
        ]);

        DB::transaction(function () use ($data) {
            $user = \App\Models\User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                'role' => $data['role'],
            ]);

            // If the role is agent, auto-create a default agent profile
            if ($user->role === 'agent') {
                $levelId = AgentLevel::where('name', 'Silver')->value('id')
                    ?? AgentLevel::orderBy('target_prospects', 'asc')->value('id');

                Agent::create([
                    'user_id' => $user->id,
                    'agent_level_id' => $levelId,
                    'referral_code' => 'BPKH-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                    'nik' => '99' . str_pad(rand(0, 99999999999999), 14, '0', STR_PAD_LEFT),
                    'whatsapp_number' => '628' . rand(100000000, 999999999),
                    'type' => 'freelance',
                    'status' => 'active',
                    'is_email_verified' => true,
                    'is_whatsapp_verified' => true,
                    'is_ktp_verified' => true,
                    'is_submitted' => true,
                    'full_name' => $user->name,
                ]);
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create_user',
                'model_type' => \App\Models\User::class,
                'model_id' => $user->id,
                'ip_address' => request()->ip(),
                'before_payload' => [],
                'after_payload' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);
        });

        return redirect()->route('superadmin.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Update an existing user.
     */
    public function updateUser(Request $request, \App\Models\User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:superadmin,admin_haji,agent',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($user, $data, $request) {
            $oldRole = $user->role;
            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
            ];

            if ($request->filled('password')) {
                $payload['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
            }

            $user->update($payload);

            // Handle Agent profile creation/deletion on role transition
            if ($oldRole !== $data['role']) {
                if ($data['role'] === 'agent') {
                    // Create if not exists
                    $exists = Agent::where('user_id', $user->id)->exists();
                    if (!$exists) {
                        $levelId = AgentLevel::where('name', 'Silver')->value('id')
                            ?? AgentLevel::orderBy('target_prospects', 'asc')->value('id');

                        Agent::create([
                            'user_id' => $user->id,
                            'agent_level_id' => $levelId,
                            'referral_code' => 'BPKH-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                            'nik' => '99' . str_pad(rand(0, 99999999999999), 14, '0', STR_PAD_LEFT),
                            'whatsapp_number' => '628' . rand(100000000, 999999999),
                            'type' => 'freelance',
                            'status' => 'active',
                            'is_email_verified' => true,
                            'is_whatsapp_verified' => true,
                            'is_ktp_verified' => true,
                            'is_submitted' => true,
                            'full_name' => $user->name,
                        ]);
                    }
                } else {
                    // Delete agent profile if transitioning away from agent role
                    $agent = Agent::where('user_id', $user->id)->first();
                    if ($agent) {
                        $agent->delete();
                    }
                }
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'update_user',
                'model_type' => \App\Models\User::class,
                'model_id' => $user->id,
                'ip_address' => request()->ip(),
                'before_payload' => [
                    'name' => $user->getOriginal('name'),
                    'email' => $user->getOriginal('email'),
                    'role' => $oldRole,
                ],
                'after_payload' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);
        });

        return redirect()->route('superadmin.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Delete a user.
     */
    public function deleteUser(\App\Models\User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('superadmin.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::transaction(function () use ($user) {
            $agent = Agent::where('user_id', $user->id)->first();
            if ($agent) {
                $agent->delete();
            }

            $user->delete();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete_user',
                'model_type' => \App\Models\User::class,
                'model_id' => $user->id,
                'ip_address' => request()->ip(),
                'before_payload' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'after_payload' => []
            ]);
        });

        return redirect()->route('superadmin.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Delete an agent profile and associated user.
     */
    public function deleteAgent(Agent $agent)
    {
        DB::transaction(function () use ($agent) {
            $user = $agent->user;

            $agent->delete();

            if ($user) {
                $user->delete();
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete_agent',
                'model_type' => Agent::class,
                'model_id' => $agent->id,
                'ip_address' => request()->ip(),
                'before_payload' => [
                    'agent_id' => $agent->id,
                    'user_id' => $user ? $user->id : null,
                    'name' => $user ? $user->name : '',
                    'email' => $user ? $user->email : '',
                ],
                'after_payload' => []
            ]);
        });

        return redirect()->route('superadmin.index')->with('success', 'Agen beserta akun user berhasil dihapus.');
    }

    /**
     * Toggle verification status of email, phone/whatsapp, or KTP for an agent.
     */
    public function verifyAgentCredentials(Agent $agent, Request $request)
    {
        $data = $request->validate([
            'field' => 'required|in:email,whatsapp,ktp',
            'status' => 'required|boolean'
        ]);

        $field = $data['field'];
        $status = (bool) $data['status'];

        $oldStatus = false;
        $dbField = '';

        if ($field === 'email') {
            $dbField = 'is_email_verified';
            $oldStatus = $agent->is_email_verified;
        } elseif ($field === 'whatsapp') {
            $dbField = 'is_whatsapp_verified';
            $oldStatus = $agent->is_whatsapp_verified;
        } elseif ($field === 'ktp') {
            $dbField = 'is_ktp_verified';
            $oldStatus = $agent->is_ktp_verified;
        }

        $agent->update([$dbField => $status]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'verify_agent_' . $field,
            'model_type' => Agent::class,
            'model_id' => $agent->id,
            'ip_address' => request()->ip(),
            'before_payload' => ['agent_id' => $agent->id, $dbField => $oldStatus],
            'after_payload' => ['agent_id' => $agent->id, $dbField => $status]
        ]);

        $statusStr = $status ? 'terverifikasi' : 'batal verifikasi';
        return redirect()->route('superadmin.index')->with('success', "Status verifikasi {$field} agen {$agent->user->name} berhasil diubah menjadi {$statusStr}.");
    }

    /**
     * Download the CSV template for importing agents.
     */
    public function downloadAgentTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_impor_agen.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($file, [
                'name', 'email', 'nik', 'whatsapp_number', 'type', 
                'institution_registration_number', 'is_institution_admin', 'nip'
            ], ';');

            // Write example row 1 (Freelance)
            fputcsv($file, [
                'Ahmad Freelance', 'ahmad.freelance@gmail.com', '3171012345678901', '081234567890', 'freelance', 
                '', 'no', ''
            ], ';');

            // Write example row 2 (Institution Employee/Admin)
            fputcsv($file, [
                'Budi B2B Admin', 'budi.b2b@gmail.com', '3171012345678902', '081298765432', 'institution', 
                'REG-BPKH-001', 'yes', 'NIP12345'
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Validate the uploaded CSV file for agents.
     */
    public function validateImportAgents(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        $file = $request->file('file');
        $path = $file->getPathname();

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

            // If header format doesn't match at least name, email, nik, type
            if (!$headers || !in_array('name', $headers) || !in_array('email', $headers) || !in_array('nik', $headers) || !in_array('type', $headers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format file template tidak sesuai. Pastikan kolom "name", "email", "nik", dan "type" tersedia.'
                ], 422);
            }

            $expectedHeaders = [
                'name', 'email', 'nik', 'whatsapp_number', 'type', 
                'institution_registration_number', 'is_institution_admin', 'nip'
            ];

            // Set to check duplicates within the uploaded file
            $seenEmails = [];
            $seenNiks = [];
            $seenWhatsapps = [];

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

                $errors = [];

                // 1. Name
                if (empty($row['name'])) {
                    $errors[] = 'Nama wajib diisi.';
                }

                // 2. Email
                if (empty($row['email'])) {
                    $errors[] = 'Email wajib diisi.';
                } elseif (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Format email tidak valid.';
                } elseif (in_array($row['email'], $seenEmails)) {
                    $errors[] = 'Email duplikat di dalam file ini.';
                } else {
                    $seenEmails[] = $row['email'];
                    if (\App\Models\User::where('email', $row['email'])->exists()) {
                        $errors[] = 'Email sudah terdaftar di sistem.';
                    }
                }

                // 3. NIK
                if (empty($row['nik'])) {
                    $errors[] = 'NIK wajib diisi.';
                } elseif (strlen($row['nik']) !== 16 || !is_numeric($row['nik'])) {
                    $errors[] = 'NIK harus 16 digit angka.';
                } elseif (in_array($row['nik'], $seenNiks)) {
                    $errors[] = 'NIK duplikat di dalam file ini.';
                } else {
                    $seenNiks[] = $row['nik'];
                    if (Agent::where('nik', $row['nik'])->exists()) {
                        $errors[] = 'NIK sudah terdaftar di sistem.';
                    }
                }

                // 4. WhatsApp Number
                if (empty($row['whatsapp_number'])) {
                    $errors[] = 'Nomor WhatsApp wajib diisi.';
                } elseif (in_array($row['whatsapp_number'], $seenWhatsapps)) {
                    $errors[] = 'Nomor WhatsApp duplikat di dalam file ini.';
                } else {
                    $seenWhatsapps[] = $row['whatsapp_number'];
                    if (Agent::where('whatsapp_number', $row['whatsapp_number'])->exists()) {
                        $errors[] = 'Nomor WhatsApp sudah terdaftar di sistem.';
                    }
                }

                // 5. Type
                $type = strtolower($row['type']);
                if (empty($row['type'])) {
                    $errors[] = 'Tipe agen wajib diisi.';
                } elseif (!in_array($type, ['freelance', 'institution'])) {
                    $errors[] = 'Tipe harus berupa: freelance atau institution.';
                }

                // 6. Institution validation if B2B
                if ($type === 'institution') {
                    $regNum = $row['institution_registration_number'];
                    if (empty($regNum)) {
                        $errors[] = 'Nomor registrasi institusi wajib diisi untuk tipe institusi.';
                    } else {
                        $institution = Institution::where('registration_number', $regNum)->first();
                        if (!$institution) {
                            $errors[] = "Institusi dengan No Registrasi '{$regNum}' tidak ditemukan.";
                        }
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
     * Process and save the validated agents from import.
     */
    public function processImportAgents(Request $request)
    {
        $request->validate([
            'agents' => 'required|array',
            'agents.*.name' => 'required|string',
            'agents.*.email' => 'required|email',
            'agents.*.nik' => 'required|digits:16',
            'agents.*.whatsapp_number' => 'required|string',
            'agents.*.type' => 'required|string|in:freelance,institution',
        ]);

        $importedCount = 0;

        DB::transaction(function () use ($request, &$importedCount) {
            $silverLevelId = AgentLevel::where('name', 'Silver')->value('id')
                ?? AgentLevel::orderBy('target_prospects', 'asc')->value('id');

            foreach ($request->agents as $a) {
                // Check if user email or agent NIK or whatsapp_number already exists to prevent double submit race conditions
                if (\App\Models\User::where('email', $a['email'])->exists() 
                    || Agent::where('nik', $a['nik'])->exists() 
                    || Agent::where('whatsapp_number', $a['whatsapp_number'])->exists()
                ) {
                    continue;
                }

                // Create User
                $user = \App\Models\User::create([
                    'name' => $a['name'],
                    'email' => $a['email'],
                    'password' => \Illuminate\Support\Facades\Hash::make($a['nik']), // Default password is NIK
                    'role' => 'agent'
                ]);

                // Determine institution if type is institution
                $institutionId = null;
                $isInstAdmin = false;
                $nip = !empty($a['nip']) ? $a['nip'] : null;

                if (strtolower($a['type']) === 'institution') {
                    $institutionId = Institution::where('registration_number', $a['institution_registration_number'])->value('id');
                    $isInstAdmin = in_array(strtolower($a['is_institution_admin']), ['yes', '1', 'true', 'ya']);
                }

                // Generate referral code
                $referralCode = 'BPKH-' . strtoupper(substr(md5(uniqid()), 0, 8));

                // Generate OTPs with 5-minute expiry
                $emailOtp = sprintf('%06d', mt_rand(0, 999999));
                $whatsappOtp = sprintf('%06d', mt_rand(0, 999999));
                $expiry = now()->addMinutes(5);

                // Create Agent profile (pending & unverified as requested)
                $agent = Agent::create([
                    'user_id' => $user->id,
                    'institution_id' => $institutionId,
                    'agent_level_id' => $silverLevelId,
                    'referral_code' => $referralCode,
                    'nik' => $a['nik'],
                    'whatsapp_number' => $a['whatsapp_number'],
                    'type' => strtolower($a['type']),
                    'is_institution_admin' => $isInstAdmin,
                    'status' => 'pending',
                    'is_email_verified' => false,
                    'is_whatsapp_verified' => false,
                    'is_ktp_verified' => false,
                    'is_submitted' => false,
                    'nip' => $nip,
                    'email_verification_code' => $emailOtp,
                    'email_verification_expires_at' => $expiry,
                    'whatsapp_verification_code' => $whatsappOtp,
                    'whatsapp_verification_expires_at' => $expiry,
                ]);

                $importedCount++;

                // Log the audit trail
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'import_agent_via_csv',
                    'model_type' => Agent::class,
                    'model_id' => $agent->id,
                    'ip_address' => request()->ip(),
                    'before_payload' => [],
                    'after_payload' => [
                        'user_id' => $user->id,
                        'agent_id' => $agent->id,
                        'name' => $a['name'],
                        'email' => $a['email'],
                        'nik' => $a['nik'],
                        'type' => $a['type'],
                        'status' => 'pending'
                    ]
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor {$importedCount} data agen ke dalam sistem.",
            'count' => $importedCount
        ]);
    }
}
