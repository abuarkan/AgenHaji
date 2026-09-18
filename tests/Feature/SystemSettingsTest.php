<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class SystemSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;
    protected $agent;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Superadmin and Agent
        $this->superadmin = User::create([
            'name' => 'Superadmin BPKH',
            'email' => 'superadmin@bpkh.go.id',
            'password' => bcrypt('password123'),
            'role' => 'superadmin',
        ]);

        $this->agent = User::create([
            'name' => 'Agent Ahmad',
            'email' => 'ahmad@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'agent',
        ]);

        // Ensure SMTP and Google settings are present (update or create if missing)
        SystemSetting::updateOrCreate(
            ['key' => 'mail_host'],
            ['value' => null, 'is_encrypted' => false, 'description' => 'SMTP Host']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'mail_password'],
            ['value' => null, 'is_encrypted' => true, 'description' => 'SMTP Password']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'google_client_id'],
            ['value' => null, 'is_encrypted' => false, 'description' => 'Google Client ID']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'google_client_secret'],
            ['value' => null, 'is_encrypted' => true, 'description' => 'Google Client Secret']
        );
    }

    /**
     * Test dynamic configurations and encryption.
     */
    public function test_settings_are_stored_encrypted_and_decrypt_automatically()
    {
        $password = 'super_secret_smtp_password_123';
        $googleSecret = 'google_secret_key_abc';

        $settingPass = SystemSetting::where('key', 'mail_password')->firstOrFail();
        $settingPass->value = $password;
        $settingPass->save();

        $settingGoogle = SystemSetting::where('key', 'google_client_secret')->firstOrFail();
        $settingGoogle->value = $googleSecret;
        $settingGoogle->save();

        // Assert they are encrypted in the raw database record
        $rawDbRecordPass = \Illuminate\Support\Facades\DB::table('system_settings')
            ->where('key', 'mail_password')
            ->first();
        $this->assertNotEquals($password, $rawDbRecordPass->value);
        $this->assertEquals($password, Crypt::decryptString($rawDbRecordPass->value));

        // Assert they decrypt automatically when retrieved via Eloquent Model attribute
        $retrievedPass = SystemSetting::where('key', 'mail_password')->firstOrFail();
        $this->assertEquals($password, $retrievedPass->value);

        $retrievedGoogle = SystemSetting::where('key', 'google_client_secret')->firstOrFail();
        $this->assertEquals($googleSecret, $retrievedGoogle->value);
    }

    /**
     * Test Superadmin can update settings.
     */
    public function test_superadmin_can_update_settings()
    {
        $response = $this->actingAs($this->superadmin)
            ->post('/superadmin/settings', [
                'settings' => [
                    'mail_host' => 'smtp.mailgun.org',
                    'mail_password' => 'secret123',
                    'google_client_id' => 'google_oauth_client_id',
                    'google_client_secret' => 'google_oauth_secret'
                ]
            ]);

        $response->assertRedirect('/superadmin');

        // Check values in database
        $this->assertEquals('smtp.mailgun.org', SystemSetting::where('key', 'mail_host')->first()->value);
        $this->assertEquals('secret123', SystemSetting::where('key', 'mail_password')->first()->value);
    }

    /**
     * Test Agent is blocked from updating settings.
     */
    public function test_agent_is_blocked_from_updating_settings()
    {
        $response = $this->actingAs($this->agent)
            ->post('/superadmin/settings', [
                'settings' => [
                    'mail_host' => 'smtp.hacker.com'
                ]
            ]);

        $response->assertStatus(403);
        $this->assertNotEquals('smtp.hacker.com', SystemSetting::where('key', 'mail_host')->value('value'));
    }
}
