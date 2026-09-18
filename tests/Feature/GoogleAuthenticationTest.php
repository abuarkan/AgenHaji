<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Agent;
use App\Models\AgentLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $existingUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create silver level for agent
        AgentLevel::create([
            'name' => 'Silver',
            'target_prospects' => 0,
            'commission_per_prospect' => 100000
        ]);

        // Create an existing user
        $this->existingUser = User::create([
            'name' => 'Existing User',
            'email' => 'existing.user@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'agent'
        ]);

        // Create their agent profile
        Agent::create([
            'user_id' => $this->existingUser->id,
            'agent_level_id' => 1,
            'referral_code' => 'SA-REF-MOCK',
            'nik' => '1234567890123456',
            'whatsapp_number' => '6281234567890',
            'type' => 'freelance',
            'status' => 'active'
        ]);
    }

    /**
     * Test redirection to Google mock page.
     */
    public function test_redirect_to_google_directs_to_mock_page()
    {
        $response = $this->get('/auth/google');
        $response->assertRedirect('/auth/google/mock');
    }

    /**
     * Test accessing the Google mock page.
     */
    public function test_mock_google_page_is_accessible()
    {
        $response = $this->get('/auth/google/mock');
        $response->assertStatus(200);
        $response->assertSee('Pilih akun');
    }

    /**
     * Test login via Google with an existing user email.
     */
    public function test_login_via_google_with_existing_user()
    {
        $response = $this->post('/auth/google/mock', [
            'email' => 'existing.user@gmail.com',
            'name' => 'Existing User'
        ]);

        // Should log in and redirect to agent dashboard route
        $response->assertRedirect('/agent/freelance');

        // Check user is authenticated
        $this->assertAuthenticatedAs($this->existingUser);

        // Check 2FA is bypassed (verified)
        $this->assertTrue(session('2fa_verified'));

        // Check user has google_id
        $this->existingUser->refresh();
        $this->assertNotNull($this->existingUser->google_id);
    }

    /**
     * Test register via Google with a new email redirects to complete details form.
     */
    public function test_register_via_google_with_new_user_redirects_to_complete_form()
    {
        $response = $this->post('/auth/google/mock', [
            'email' => 'new.user@gmail.com',
            'name' => 'New Google User'
        ]);

        $response->assertRedirect('/register/google/complete');
        $response->assertSessionHas('google_user');

        $googleUser = session('google_user');
        $this->assertEquals('new.user@gmail.com', $googleUser['email']);
        $this->assertEquals('New Google User', $googleUser['name']);
    }

    /**
     * Test complete form is accessible with session.
     */
    public function test_complete_form_is_accessible_with_session()
    {
        $response = $this->withSession([
            'google_user' => [
                'id' => 'mock_123',
                'name' => 'New Google User',
                'email' => 'new.user@gmail.com'
            ]
        ])->get('/register/google/complete');

        $response->assertStatus(200);
        $response->assertSee('Lengkapi Data Pendaftaran');
        $response->assertSee('new.user@gmail.com');
    }

    /**
     * Test complete form is blocked without session.
     */
    public function test_complete_form_is_blocked_without_session()
    {
        $response = $this->get('/register/google/complete');
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test submitting registration completion form.
     */
    public function test_submitting_google_registration_completion()
    {
        $response = $this->withSession([
            'google_user' => [
                'id' => 'mock_123',
                'name' => 'New Google User',
                'email' => 'new.user@gmail.com'
            ]
        ])->post('/register/google/complete', [
            'whatsapp_number' => '628987654321',
            'nik' => '9876543210123456',
            'type' => 'freelance'
        ]);

        // User should be created and redirected
        $response->assertRedirect('/dashboard');

        // Check user is authenticated
        $user = User::where('email', 'new.user@gmail.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);

        // Check 2FA is bypassed
        $this->assertTrue(session('2fa_verified'));

        // Check agent profile was created and has is_email_verified = true
        $agent = Agent::withoutGlobalScope(\App\Scopes\TenantScope::class)->where('user_id', $user->id)->firstOrFail();
        $this->assertEquals('9876543210123456', $agent->nik);
        $this->assertTrue((bool)$agent->is_email_verified);
        $this->assertEquals('pending', $agent->status);

        // Session should be cleared of google_user
        $this->assertFalse(session()->has('google_user'));
    }
}
