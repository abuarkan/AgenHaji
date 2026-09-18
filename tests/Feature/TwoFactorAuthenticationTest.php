<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Agent;
use App\Models\AgentLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorOtpMail;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create initial silver level for agent
        AgentLevel::create([
            'name' => 'Silver',
            'target_prospects' => 0,
            'commission_per_prospect' => 100000
        ]);

        // Create a user
        $this->user = User::create([
            'name' => 'Test Agent',
            'email' => 'test.agent@example.com',
            'password' => bcrypt('password123'),
            'role' => 'agent'
        ]);

        // Create their agent profile
        Agent::create([
            'user_id' => $this->user->id,
            'agent_level_id' => 1,
            'referral_code' => 'TEST-REF',
            'nik' => '1234567890123456',
            'whatsapp_number' => '6281234567890',
            'type' => 'freelance',
            'status' => 'active'
        ]);
    }

    /**
     * Test middleware redirects unverified 2FA users.
     */
    public function test_middleware_redirects_unverified_2fa_users()
    {
        // Enforce 2FA check by putting 'test_enforce_2fa' key
        $response = $this->actingAs($this->user)
            ->withSession(['test_enforce_2fa' => true])
            ->get('/dashboard');

        $response->assertRedirect('/two-factor');
    }

    /**
     * Test 2FA page is accessible.
     */
    public function test_two_factor_page_is_accessible()
    {
        $response = $this->actingAs($this->user)
            ->get('/two-factor');

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Keamanan (2FA)');
    }

    /**
     * Test successful OTP verification.
     */
    public function test_successful_otp_verification()
    {
        $this->user->two_factor_code = '123456';
        $this->user->two_factor_expires_at = now()->addMinutes(10);
        $this->user->save();

        $response = $this->actingAs($this->user)
            ->withSession(['test_enforce_2fa' => true])
            ->post('/two-factor', [
                'code' => '123456'
            ]);

        // Should redirect to dashboard redirect route
        $response->assertRedirect('/agent/freelance');

        // Session should be verified
        $this->assertTrue(session('2fa_verified'));

        // DB columns should be cleared
        $this->user->refresh();
        $this->assertNull($this->user->two_factor_code);
        $this->assertNull($this->user->two_factor_expires_at);
    }

    /**
     * Test invalid OTP verification.
     */
    public function test_invalid_otp_verification()
    {
        $this->user->two_factor_code = '123456';
        $this->user->two_factor_expires_at = now()->addMinutes(10);
        $this->user->save();

        $response = $this->actingAs($this->user)
            ->post('/two-factor', [
                'code' => '654321'
            ]);

        $response->assertSessionHasErrors('code');
        $this->assertNotEquals(true, session('2fa_verified'));
    }

    /**
     * Test expired OTP verification.
     */
    public function test_expired_otp_verification()
    {
        $this->user->two_factor_code = '123456';
        $this->user->two_factor_expires_at = now()->subMinutes(1);
        $this->user->save();

        $response = $this->actingAs($this->user)
            ->post('/two-factor', [
                'code' => '123456'
            ]);

        $response->assertSessionHasErrors('code');
        $this->assertNotEquals(true, session('2fa_verified'));
    }

    /**
     * Test OTP resend triggers mail and updates database.
     */
    public function test_otp_resend_sends_mail()
    {
        Mail::fake();

        $response = $this->actingAs($this->user)
            ->post('/two-factor/resend');

        $response->assertSessionHas('success');
        
        $this->user->refresh();
        $this->assertNotNull($this->user->two_factor_code);

        Mail::assertSent(TwoFactorOtpMail::class, function ($mail) {
            return $mail->otpCode === $this->user->two_factor_code && $mail->userName === $this->user->name;
        });
    }

    /**
     * Test that disabling 2FA globally allows bypassing verification.
     */
    public function test_disabling_2fa_globally_bypasses_otp_and_middleware()
    {
        // Set two_factor_enabled to '0'
        \App\Models\SystemSetting::updateOrCreate(
            ['key' => 'two_factor_enabled'],
            ['value' => '0', 'is_encrypted' => false]
        );

        // 1. Try hitting dashboard - should pass directly
        $response = $this->actingAs($this->user)
            ->withSession(['test_enforce_2fa' => true])
            ->get('/dashboard');

        // It shouldn't redirect to /two-factor, instead it goes to the dashboard redirect target /agent/freelance
        $response->assertRedirect('/agent/freelance');

        // 2. Perform a fresh login and verify no 2FA redirect occurs
        \Illuminate\Support\Facades\Auth::logout();
        
        $responseLogin = $this->post('/login', [
            'email' => 'test.agent@example.com',
            'password' => 'password123'
        ]);

        $responseLogin->assertRedirect('/dashboard');
    }
}
