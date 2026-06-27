<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\AgentLevel;
use App\Models\AuditLog;
use App\Models\Institution;
use App\Models\ProspectJemaah;
use App\Models\SystemSetting;
use App\Models\LoginBackground;
use App\Services\AgentLevelingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuperadminController extends Controller
{
    /**
     * Show the main admin control panel.
     */
    public function index()
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
        $institutions = Institution::withCount('agents')->orderBy('id')->get();
        $prospects = ProspectJemaah::with('agent.user')->orderBy('id', 'desc')->get();
        
        $settings = SystemSetting::all()->pluck('value', 'key')->toArray();
        $settingsRaw = SystemSetting::all();
        $levels = AgentLevel::orderBy('target_prospects', 'asc')->get();
        $auditLogs = AuditLog::with('user')->orderBy('created_at', 'desc')->take(30)->get();
        $loginBackgrounds = LoginBackground::orderBy('id', 'desc')->get();
        $users = \App\Models\User::orderBy('id')->get();

        return view('superadmin.dashboard', compact('stats', 'agents', 'institutions', 'prospects', 'settings', 'settingsRaw', 'levels', 'auditLogs', 'loginBackgrounds', 'users'));
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

        if ($oldStatus !== $data['status']) {
            $updateFields = ['status' => $data['status']];
            if ($data['status'] === 'active') {
                $updateFields['is_ktp_verified'] = true;
                $updateFields['is_email_verified'] = true;
                $updateFields['is_whatsapp_verified'] = true;
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
        }

        return redirect()->route('superadmin.index')->with('success', "Status agen {$agent->user->name} berhasil diubah.");
    }

    /**
     * Run manual evaluation promotion for a single agent.
     */
    public function evaluateAgentLevel(Agent $agent, AgentLevelingService $levelingService)
    {
        $result = $levelingService->evaluateAgentLevel($agent);

        if ($result['level_changed']) {
            $msg = "Evaluasi selesai. Agen {$agent->user->name} dipromosikan dari {$result['old_level']} ke {$result['new_level']}!";
            return redirect()->route('superadmin.index')->with('success', $msg);
        }

        return redirect()->route('superadmin.index')->with('success', "Evaluasi selesai. Tingkat level {$agent->user->name} tetap {$result['new_level']}.");
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

        return redirect()->route('superadmin.index')->with('success', "Status institusi {$institution->name} berhasil diubah.");
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
}
