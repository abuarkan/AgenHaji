<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'key' => 'mail_host',
                'value' => null,
                'is_encrypted' => false,
                'description' => 'SMTP Mail Server Hostname',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_port',
                'value' => '587',
                'is_encrypted' => false,
                'description' => 'SMTP Mail Server Port',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_username',
                'value' => null,
                'is_encrypted' => true,
                'description' => 'SMTP Mail Server Username',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_password',
                'value' => null,
                'is_encrypted' => true,
                'description' => 'SMTP Mail Server Password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'tls',
                'is_encrypted' => false,
                'description' => 'SMTP Mail Server Encryption (tls / ssl / null)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'no-reply@bpkh.go.id',
                'is_encrypted' => false,
                'description' => 'Email Sender Address',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'BPKH Hajj Agent Portal',
                'is_encrypted' => false,
                'description' => 'Email Sender Display Name',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'google_client_id',
                'value' => null,
                'is_encrypted' => false,
                'description' => 'Google Cloud API Client ID',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'google_client_secret',
                'value' => null,
                'is_encrypted' => true,
                'description' => 'Google Cloud API Client Secret',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'google_redirect_uri',
                'value' => 'http://localhost/auth/google/callback',
                'is_encrypted' => false,
                'description' => 'Google OAuth Redirect Callback URI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('system_settings')->insert($settings);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
            'google_client_id',
            'google_client_secret',
            'google_redirect_uri',
        ];

        DB::table('system_settings')->whereIn('key', $keys)->delete();
    }
};
