<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        $backgrounds = \App\Models\LoginBackground::all();
        return view('auth.login', compact('backgrounds'));
    }

    /**
     * Handle authentication request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang dimasukkan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect authenticated users based on their role and agent type.
     */
    public function dashboardRedirect()
    {
        $user = Auth::user();

        if ($user->role === 'superadmin' || $user->role === 'admin_haji') {
            return redirect()->route('superadmin.index');
        }

        // Fetch agent profile (using withoutGlobalScope to prevent recursion on auth checks)
        $agent = \App\Models\Agent::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('user_id', $user->id)
            ->first();

        if ($agent) {
            if ($agent->type === 'institution') {
                return redirect()->route('agent.institution');
            }
            return redirect()->route('agent.freelance');
        }

        // Fallback if role is agent but has no profile
        Auth::logout();
        return redirect()->route('login')->withErrors(['email' => 'Profil agen tidak ditemukan.']);
    }

    /**
     * Show the register form.
     */
    public function showRegister()
    {
        $institutions = \App\Models\Institution::where('status', 'active')->orderBy('name')->get();
        return view('auth.register', compact('institutions'));
    }

    /**
     * Handle agent self-registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'whatsapp_number' => ['required', 'string', 'min:10'],
            'nik' => ['required', 'string', 'size:16', 'unique:agents'],
            'type' => ['required', 'string', 'in:freelance,institution_new,institution_employee'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($request->type === 'institution_new') {
            $request->validate([
                'institution_name' => ['required', 'string', 'max:255'],
                'institution_address' => ['required', 'string'],
                'institution_legal_doc' => ['required', 'file', 'max:3072'],
                'institution_npwp' => ['required', 'string'],
                'institution_bank_account' => ['required', 'string'],
                'institution_latitude' => ['required', 'string'],
                'institution_longitude' => ['required', 'string'],
            ]);
        } elseif ($request->type === 'institution_employee') {
            $request->validate([
                'institution_id' => ['required', 'exists:institutions,id'],
            ]);
        }

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'agent',
        ]);

        $levelId = \App\Models\AgentLevel::where('name', 'Silver')->value('id')
            ?? \App\Models\AgentLevel::orderBy('target_prospects', 'asc')->value('id');

        $referral_code = 'BPKH-' . strtoupper(substr(md5(uniqid()), 0, 8));

        if ($request->type === 'institution_new') {
            $legalDocPath = 'uploads/demo_legal_doc.pdf';
            if ($request->hasFile('institution_legal_doc')) {
                $file = $request->file('institution_legal_doc');
                $filename = time() . '_legal_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $legalDocPath = 'uploads/' . $filename;
            }

            $instRegNo = 'BPKH-INST-' . rand(100000, 999999);

            $institution = \App\Models\Institution::create([
                'name' => $request->institution_name,
                'registration_number' => $instRegNo,
                'address' => $request->institution_address,
                'legal_document' => $legalDocPath,
                'npwp' => $request->institution_npwp,
                'bank_account' => $request->institution_bank_account,
                'latitude' => $request->institution_latitude,
                'longitude' => $request->institution_longitude,
                'status' => 'active',
            ]);

            \App\Models\Agent::create([
                'user_id' => $user->id,
                'institution_id' => $institution->id,
                'agent_level_id' => $levelId,
                'referral_code' => $referral_code,
                'nik' => $request->nik,
                'whatsapp_number' => $request->whatsapp_number,
                'type' => 'institution',
                'is_institution_admin' => true,
                'status' => 'active',
                'is_email_verified' => true,
                'is_whatsapp_verified' => true,
                'is_ktp_verified' => true,
                'is_submitted' => true,
            ]);
        } else {
            $instId = null;
            if ($request->type === 'institution_employee') {
                $instId = $request->institution_id;
            }

            \App\Models\Agent::create([
                'user_id' => $user->id,
                'institution_id' => $instId,
                'agent_level_id' => $levelId,
                'referral_code' => $referral_code,
                'nik' => $request->nik,
                'whatsapp_number' => $request->whatsapp_number,
                'type' => $instId ? 'institution' : 'freelance',
                'is_institution_admin' => false,
                'status' => 'pending',
                'is_email_verified' => false,
                'is_whatsapp_verified' => false,
                'is_ktp_verified' => false,
                'is_submitted' => false,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
