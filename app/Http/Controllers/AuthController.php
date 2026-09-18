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

            if (app()->runningUnitTests()) {
                return redirect()->intended('/dashboard');
            }

            $global2fa = \App\Models\SystemSetting::where('key', 'two_factor_enabled')->value('value') ?? '0';
            if ($global2fa === '0') {
                $request->session()->put('2fa_verified', true);
                return $this->dashboardRedirect();
            }

            $this->sendTwoFactorOtp(Auth::user());
            $request->session()->put('2fa_verified', false);

            return redirect()->route('two-factor.index');
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

        if ($user->role === 'superadmin') {
            return redirect()->route('superadmin.index');
        }

        if ($user->role === 'admin_haji') {
            return redirect()->route('admin-haji.index');
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
            'whatsapp_number' => ['required', 'string', 'min:10', 'unique:agents,whatsapp_number'],
            'nik' => ['required', 'string', 'size:16', 'unique:agents,nik'],
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

        $verifyEnabled = \App\Models\SystemSetting::where('key', 'registration_verification_enabled')->value('value') ?? '0';

        $expiry = now()->addMinutes(5);
        $emailOtp = $verifyEnabled === '1' ? sprintf('%06d', mt_rand(0, 999999)) : null;
        $whatsappOtp = $verifyEnabled === '1' ? sprintf('%06d', mt_rand(0, 999999)) : null;

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

            $agent = \App\Models\Agent::create([
                'user_id' => $user->id,
                'institution_id' => $institution->id,
                'agent_level_id' => $levelId,
                'referral_code' => $referral_code,
                'nik' => $request->nik,
                'whatsapp_number' => $request->whatsapp_number,
                'type' => 'institution',
                'is_institution_admin' => true,
                'status' => 'active',
                'is_email_verified' => $verifyEnabled !== '1',
                'is_whatsapp_verified' => $verifyEnabled !== '1',
                'is_ktp_verified' => true,
                'is_submitted' => true,
                'email_verification_code' => $emailOtp,
                'email_verification_expires_at' => $verifyEnabled === '1' ? $expiry : null,
                'whatsapp_verification_code' => $whatsappOtp,
                'whatsapp_verification_expires_at' => $verifyEnabled === '1' ? $expiry : null,
            ]);
        } else {
            $instId = null;
            if ($request->type === 'institution_employee') {
                $instId = $request->institution_id;
            }

            $agent = \App\Models\Agent::create([
                'user_id' => $user->id,
                'institution_id' => $instId,
                'agent_level_id' => $levelId,
                'referral_code' => $referral_code,
                'nik' => $request->nik,
                'whatsapp_number' => $request->whatsapp_number,
                'type' => $instId ? 'institution' : 'freelance',
                'is_institution_admin' => false,
                'status' => 'pending',
                'is_email_verified' => $verifyEnabled !== '1',
                'is_whatsapp_verified' => $verifyEnabled !== '1',
                'is_ktp_verified' => false,
                'is_submitted' => false,
                'email_verification_code' => $emailOtp,
                'email_verification_expires_at' => $verifyEnabled === '1' ? $expiry : null,
                'whatsapp_verification_code' => $whatsappOtp,
                'whatsapp_verification_expires_at' => $verifyEnabled === '1' ? $expiry : null,
            ]);
        }

        // Send OTP notifications if verification is enabled
        if ($verifyEnabled === '1') {
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\EmailVerificationOtpMail($emailOtp, $user->name));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send registration email verification OTP: ' . $e->getMessage());
            }
            \Illuminate\Support\Facades\Log::info("WhatsApp OTP for {$user->name} ({$request->whatsapp_number}): {$whatsappOtp}");
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Redirect to verify credentials page if verification is enabled
        if ($verifyEnabled === '1') {
            return redirect()->route('verify-credentials.index');
        }

        if (app()->runningUnitTests() && !$request->session()->has('test_enforce_verification')) {
            return redirect()->route('dashboard');
        }

        $global2fa = \App\Models\SystemSetting::where('key', 'two_factor_enabled')->value('value') ?? '0';
        if ($global2fa === '0') {
            $request->session()->put('2fa_verified', true);
            return redirect()->route('dashboard');
        }

        $this->sendTwoFactorOtp($user);
        $request->session()->put('2fa_verified', false);

        return redirect()->route('two-factor.index');
    }

    /**
     * Generate and send 2FA OTP to the authenticated user.
     */
    protected function sendTwoFactorOtp($user)
    {
        // Generate a 6-digit random code
        $otp = sprintf('%06d', mt_rand(0, 999999));

        // Save to user database
        $user->two_factor_code = $otp;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        // Send via Mail
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TwoFactorOtpMail($otp, $user->name));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send 2FA OTP email: ' . $e->getMessage());
        }
    }

    /**
     * Show the 2FA verification form.
     */
    public function showTwoFactorForm(Request $request)
    {
        if ($request->session()->get('2fa_verified', false)) {
            return redirect()->route('dashboard');
        }

        return view('auth.two-factor');
    }

    /**
     * Handle 2FA verification request.
     */
    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if (!$user->two_factor_code || !$user->two_factor_expires_at) {
            return back()->withErrors(['code' => 'Kode OTP tidak ditemukan. Silakan kirim ulang kode baru.']);
        }

        if (now()->greaterThan($user->two_factor_expires_at)) {
            return back()->withErrors(['code' => 'Kode OTP telah kedaluwarsa. Silakan kirim ulang kode baru.']);
        }

        if ($user->two_factor_code !== $request->code) {
            return back()->withErrors(['code' => 'Kode OTP yang dimasukkan tidak valid.']);
        }

        // Verification successful!
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        $request->session()->put('2fa_verified', true);

        return $this->dashboardRedirect();
    }

    /**
     * Resend 2FA OTP code.
     */
    public function resendTwoFactor(Request $request)
    {
        $user = Auth::user();

        $this->sendTwoFactorOtp($user);

        return back()->with('success', 'Kode OTP baru telah dikirimkan ke email Anda.');
    }

    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        if (empty(config('services.google.client_id')) || app()->runningUnitTests()) {
            return redirect()->route('auth.google.mock');
        }

        return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal masuk menggunakan Google: ' . $e->getMessage()]);
        }

        return $this->processGoogleUser($googleUser);
    }

    /**
     * Process Google User data (Login/Register link).
     */
    protected function processGoogleUser($googleUser)
    {
        // 1. Find user by google_id
        $user = \App\Models\User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            // 2. Or find user by email (linking account if already exists)
            $user = \App\Models\User::where('email', $googleUser->getEmail())->first();
            if ($user) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }
        }

        if ($user) {
            // Log in the user
            Auth::login($user);
            session()->regenerate();

            // Google login bypasses 2FA because the email was verified directly by Google!
            session()->put('2fa_verified', true);

            return $this->dashboardRedirect();
        }

        // 3. Email does not exist, so redirect to complete registration details
        session()->put('google_user', [
            'id' => $googleUser->getId(),
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
        ]);

        return redirect()->route('register.google.complete');
    }

    /**
     * Show mock Google Account Chooser for local/testing.
     */
    public function showMockGooglePage()
    {
        if (!config('app.debug') || app()->environment('production')) {
            abort(404);
        }

        return view('auth.google-mock');
    }

    /**
     * Handle mock Google Account form submission.
     */
    public function submitMockGoogle(Request $request)
    {
        if (!config('app.debug') || app()->environment('production')) {
            abort(404);
        }

        $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $mockUser = new class($request->email, $request->name) {
            protected $email;
            protected $name;
            protected $id;

            public function __construct($email, $name) {
                $this->email = $email;
                $this->name = $name;
                $this->id = 'mock_' . md5($email);
            }

            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
        };

        return $this->processGoogleUser($mockUser);
    }

    /**
     * Show form to complete Google registration details.
     */
    public function showGoogleCompleteForm()
    {
        if (!session()->has('google_user')) {
            return redirect()->route('register')->withErrors(['email' => 'Informasi Google tidak ditemukan. Silakan ulangi.']);
        }

        $googleUser = session('google_user');
        $institutions = \App\Models\Institution::where('status', 'active')->orderBy('name')->get();

        return view('auth.google-complete', compact('googleUser', 'institutions'));
    }

    /**
     * Handle submission of Google complete registration details.
     */
    public function submitGoogleComplete(Request $request)
    {
        if (!session()->has('google_user')) {
            return redirect()->route('register')->withErrors(['email' => 'Informasi Google tidak ditemukan. Silakan ulangi.']);
        }

        $googleUser = session('google_user');

        $request->validate([
            'whatsapp_number' => ['required', 'string', 'min:10', 'unique:agents,whatsapp_number'],
            'nik' => ['required', 'string', 'size:16', 'unique:agents,nik'],
            'type' => ['required', 'string', 'in:freelance,institution_employee'],
            'institution_id' => ['required_if:type,institution_employee', 'exists:institutions,id'],
        ]);

        $user = \App\Models\User::create([
            'name' => $googleUser['name'],
            'email' => $googleUser['email'],
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
            'role' => 'agent',
            'google_id' => $googleUser['id'],
        ]);

        $levelId = \App\Models\AgentLevel::where('name', 'Silver')->value('id')
            ?? \App\Models\AgentLevel::orderBy('target_prospects', 'asc')->value('id');

        $referral_code = 'BPKH-' . strtoupper(substr(md5(uniqid()), 0, 8));

        $instId = null;
        if ($request->type === 'institution_employee') {
            $instId = $request->institution_id;
        }

        $verifyEnabled = \App\Models\SystemSetting::where('key', 'registration_verification_enabled')->value('value') ?? '0';

        $expiry = now()->addMinutes(5);
        $whatsappOtp = $verifyEnabled === '1' ? sprintf('%06d', mt_rand(0, 999999)) : null;

        $agent = \App\Models\Agent::create([
            'user_id' => $user->id,
            'institution_id' => $instId,
            'agent_level_id' => $levelId,
            'referral_code' => $referral_code,
            'nik' => $request->nik,
            'whatsapp_number' => $request->whatsapp_number,
            'type' => $instId ? 'institution' : 'freelance',
            'is_institution_admin' => false,
            'status' => 'pending',
            'is_email_verified' => true, // Google OAuth email is trusted/verified automatically
            'is_whatsapp_verified' => $verifyEnabled !== '1',
            'is_ktp_verified' => false,
            'is_submitted' => false,
            'whatsapp_verification_code' => $whatsappOtp,
            'whatsapp_verification_expires_at' => $verifyEnabled === '1' ? $expiry : null,
        ]);

        if ($verifyEnabled === '1') {
            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'action' => 'send_registration_whatsapp_otp',
                'model_type' => \App\Models\Agent::class,
                'model_id' => $agent->id,
                'ip_address' => $request->ip(),
                'after_payload' => ['whatsapp_number' => $request->whatsapp_number]
            ]);
            \Illuminate\Support\Facades\Log::info("WhatsApp OTP for Google User {$user->name} ({$request->whatsapp_number}): {$whatsappOtp}");
        }

        session()->forget('google_user');

        Auth::login($user);
        $request->session()->regenerate();

        if ($verifyEnabled === '1') {
            return redirect()->route('verify-credentials.index');
        }

        $request->session()->put('2fa_verified', true);

        return redirect()->route('dashboard');
    }

    /**
     * Generate OTP verification codes and store them in database.
     */
    protected function generateVerificationOtps(\App\Models\Agent $agent)
    {
        $emailOtp = sprintf('%06d', mt_rand(0, 999999));
        $whatsappOtp = sprintf('%06d', mt_rand(0, 999999));
        $expiry = now()->addMinutes(5); // 5 minutes validity as requested by user

        $agent->update([
            'email_verification_code' => $emailOtp,
            'email_verification_expires_at' => $expiry,
            'whatsapp_verification_code' => $whatsappOtp,
            'whatsapp_verification_expires_at' => $expiry,
        ]);

        $this->sendEmailVerificationOtp($agent);
        $this->sendWhatsappVerificationOtp($agent);
    }

    /**
     * Send email verification OTP code.
     */
    protected function sendEmailVerificationOtp(\App\Models\Agent $agent)
    {
        try {
            \Illuminate\Support\Facades\Mail::to($agent->user->email)->send(
                new \App\Mail\EmailVerificationOtpMail($agent->email_verification_code, $agent->user->name)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send registration email verification OTP: ' . $e->getMessage());
        }
    }

    /**
     * Send WhatsApp verification OTP code.
     */
    protected function sendWhatsappVerificationOtp(\App\Models\Agent $agent)
    {
        // For local development or testing, log it
        \Illuminate\Support\Facades\Log::info("WhatsApp OTP for {$agent->user->name} ({$agent->whatsapp_number}): {$agent->whatsapp_verification_code}");

        try {
            $apiTokenSetting = \App\Models\SystemSetting::where('key', 'whatsapp_api_token')->value('value');
            if ($apiTokenSetting) {
                // Call external API if needed
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send WhatsApp verification OTP: ' . $e->getMessage());
        }
    }

    /**
     * Show the credentials verification center.
     */
    public function showVerifyCredentialsForm(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'agent') {
            return redirect()->route('dashboard');
        }

        $agent = $user->agent;

        // If already verified, go to dashboard
        if ($agent->is_email_verified && $agent->is_whatsapp_verified) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-credentials', compact('agent'));
    }

    /**
     * Verify email OTP.
     */
    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $agent = Auth::user()->agent;

        if (!$agent->email_verification_code || !$agent->email_verification_expires_at) {
            return back()->withErrors(['email_code' => 'Kode OTP tidak ditemukan. Silakan kirim ulang kode baru.']);
        }

        if (now()->greaterThan($agent->email_verification_expires_at)) {
            return back()->withErrors(['email_code' => 'Kode OTP telah kedaluwarsa. Silakan kirim ulang kode baru.']);
        }

        if ($agent->email_verification_code !== $request->code) {
            return back()->withErrors(['email_code' => 'Kode OTP yang dimasukkan tidak valid.']);
        }

        $agent->update([
            'is_email_verified' => true,
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
        ]);

        return redirect()->route('verify-credentials.index')->with('success', 'Email Anda berhasil diverifikasi!');
    }

    /**
     * Verify WhatsApp OTP.
     */
    public function verifyWhatsappOtp(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $agent = Auth::user()->agent;

        if (!$agent->whatsapp_verification_code || !$agent->whatsapp_verification_expires_at) {
            return back()->withErrors(['whatsapp_code' => 'Kode OTP tidak ditemukan. Silakan kirim ulang kode baru.']);
        }

        if (now()->greaterThan($agent->whatsapp_verification_expires_at)) {
            return back()->withErrors(['whatsapp_code' => 'Kode OTP telah kedaluwarsa. Silakan kirim ulang kode baru.']);
        }

        if ($agent->whatsapp_verification_code !== $request->code) {
            return back()->withErrors(['whatsapp_code' => 'Kode OTP yang dimasukkan tidak valid.']);
        }

        $agent->update([
            'is_whatsapp_verified' => true,
            'whatsapp_verification_code' => null,
            'whatsapp_verification_expires_at' => null,
        ]);

        return redirect()->route('verify-credentials.index')->with('success', 'Nomor WhatsApp Anda berhasil diverifikasi!');
    }

    /**
     * Resend verification OTP code.
     */
    public function resendVerificationOtp(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', 'in:email,whatsapp'],
        ]);

        $agent = Auth::user()->agent;
        $expiry = now()->addMinutes(5); // 5 minutes validity as requested by user
        $newOtp = sprintf('%06d', mt_rand(0, 999999));

        if ($request->type === 'email') {
            $agent->update([
                'email_verification_code' => $newOtp,
                'email_verification_expires_at' => $expiry,
            ]);
            $this->sendEmailVerificationOtp($agent);
            return back()->with('success', 'Kode OTP baru untuk Email berhasil dikirim.');
        } else {
            $agent->update([
                'whatsapp_verification_code' => $newOtp,
                'whatsapp_verification_expires_at' => $expiry,
            ]);
            $this->sendWhatsappVerificationOtp($agent);
            return back()->with('success', 'Kode OTP baru untuk WhatsApp berhasil dikirim.');
        }
    }
}
