<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Agent;
use App\Models\AgentLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationOtpMail;
use Tests\TestCase;

class AgentVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected $level;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        // Create agent level
        $this->level = AgentLevel::create([
            'name' => 'Silver',
            'target_prospects' => 0,
            'commission_per_prospect' => 100000
        ]);

        // Disable 2FA globally for verification tests
        \App\Models\SystemSetting::where('key', 'two_factor_enabled')->update(['value' => '0']);

        // Enable registration verification specifically for these verification tests
        \App\Models\SystemSetting::updateOrCreate(
            ['key' => 'registration_verification_enabled'],
            ['value' => '1']
        );
    }

    /**
     * Test newly registered agent is redirected to verification screen and receives OTPs.
     */
    public function test_newly_registered_agent_is_redirected_to_verification()
    {
        $response = $this->withSession(['test_enforce_verification' => true])
            ->post('/register', [
                'name' => 'Ahmad Pendaftar',
                'email' => 'ahmad.pendaftar@gmail.com',
                'whatsapp_number' => '081234567890',
                'nik' => '3171012345678999',
                'type' => 'freelance',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $user = User::where('email', 'ahmad.pendaftar@gmail.com')->first();
        $this->assertNotNull($user);
        $agent = $user->agent;
        $this->assertNotNull($agent);

        // Assert OTPs are generated
        $this->assertNotNull($agent->email_verification_code);
        $this->assertNotNull($agent->whatsapp_verification_code);
        $this->assertFalse((bool)$agent->is_email_verified);
        $this->assertFalse((bool)$agent->is_whatsapp_verified);

        // Assert redirect to verify-credentials
        $response->assertRedirect(route('verify-credentials.index'));

        // Assert Email verification OTP mail is sent
        Mail::assertSent(EmailVerificationOtpMail::class, function ($mail) use ($user, $agent) {
            return $mail->hasTo($user->email) && $mail->otpCode === $agent->email_verification_code;
        });
    }

    /**
     * Test middleware blocks access to agent routes for unverified users.
     */
    public function test_middleware_blocks_access_to_unverified_agents()
    {
        $user = User::create([
            'name' => 'Unverified Agent',
            'email' => 'unverified@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'agent'
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'agent_level_id' => $this->level->id,
            'referral_code' => 'UNV-REF',
            'nik' => '1234567890123456',
            'whatsapp_number' => '628123456789',
            'type' => 'freelance',
            'status' => 'pending',
            'is_email_verified' => false,
            'is_whatsapp_verified' => false,
            'is_ktp_verified' => false
        ]);

        // Access dashboard freelance
        $response = $this->actingAs($user)
            ->withSession(['test_enforce_verification' => true])
            ->get('/agent/freelance');

        $response->assertRedirect(route('verify-credentials.index'));
    }

    /**
     * Test verifying correct OTP codes updates credentials status.
     */
    public function test_verifying_correct_otps_updates_status()
    {
        $user = User::create([
            'name' => 'Verify Test',
            'email' => 'verifytest@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'agent'
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'agent_level_id' => $this->level->id,
            'referral_code' => 'VT-REF',
            'nik' => '1234567890123457',
            'whatsapp_number' => '628123456780',
            'type' => 'freelance',
            'status' => 'pending',
            'is_email_verified' => false,
            'is_whatsapp_verified' => false,
            'is_ktp_verified' => false,
            'email_verification_code' => '111111',
            'email_verification_expires_at' => now()->addMinutes(5),
            'whatsapp_verification_code' => '222222',
            'whatsapp_verification_expires_at' => now()->addMinutes(5)
        ]);

        $this->actingAs($user);

        // 1. Submit wrong email code
        $this->post('/verify-credentials/email', ['code' => '999999'])
            ->assertSessionHasErrors('email_code');

        // 2. Submit correct email code
        $this->post('/verify-credentials/email', ['code' => '111111'])
            ->assertRedirect(route('verify-credentials.index'))
            ->assertSessionHas('success');

        $agent->refresh();
        $this->assertTrue((bool)$agent->is_email_verified);
        $this->assertNull($agent->email_verification_code);

        // 3. Submit correct WhatsApp code
        $this->post('/verify-credentials/whatsapp', ['code' => '222222'])
            ->assertRedirect(route('verify-credentials.index'))
            ->assertSessionHas('success');

        $agent->refresh();
        $this->assertTrue((bool)$agent->is_whatsapp_verified);
        $this->assertNull($agent->whatsapp_verification_code);
    }

    /**
     * Test Google registration completion sets email verified but requires WhatsApp.
     */
    public function test_google_complete_requires_whatsapp_but_bypasses_email()
    {
        $googleUser = [
            'id' => 'google_12345',
            'email' => 'googleagent@gmail.com',
            'name' => 'Google Agent',
        ];

        // Store Google Info in session
        $this->withSession([
            'google_user' => $googleUser,
            'test_enforce_verification' => true
        ]);

        $response = $this->post('/register/google/complete', [
            'whatsapp_number' => '089876543210',
            'nik' => '3171012345678902',
            'type' => 'freelance',
        ]);

        $user = User::where('email', 'googleagent@gmail.com')->first();
        $this->assertNotNull($user);
        $agent = $user->agent;
        $this->assertNotNull($agent);

        // Assert Email is auto-verified, but WhatsApp is not
        $this->assertTrue((bool)$agent->is_email_verified);
        $this->assertFalse((bool)$agent->is_whatsapp_verified);
        $this->assertNotNull($agent->whatsapp_verification_code);

        $response->assertRedirect(route('verify-credentials.index'));
    }

    /**
     * Test registration rejects duplicate NIK and WhatsApp numbers.
     */
    public function test_registration_rejects_duplicate_nik_or_whatsapp()
    {
        // First register an agent
        $user1 = User::create([
            'name' => 'Agent One',
            'email' => 'one@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'agent'
        ]);
        Agent::create([
            'user_id' => $user1->id,
            'agent_level_id' => $this->level->id,
            'referral_code' => 'REF-ONE',
            'nik' => '1234567890123456',
            'whatsapp_number' => '081234567890',
            'type' => 'freelance',
            'status' => 'active',
            'is_email_verified' => true,
            'is_whatsapp_verified' => true,
            'is_ktp_verified' => true
        ]);

        // Attempt to register with duplicate WhatsApp number
        $response = $this->post('/register', [
            'name' => 'Agent Two',
            'email' => 'two@gmail.com',
            'whatsapp_number' => '081234567890', // Duplicate!
            'nik' => '1234567890123457',
            'type' => 'freelance',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors('whatsapp_number');

        // Attempt to register with duplicate NIK
        $response = $this->post('/register', [
            'name' => 'Agent Three',
            'email' => 'three@gmail.com',
            'whatsapp_number' => '081234567899',
            'nik' => '1234567890123456', // Duplicate!
            'type' => 'freelance',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors('nik');
    }
}
