<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Tambahkan ini jika belum ada

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $forwardedHttps = ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
        if (str_starts_with((string) config('app.url'), 'https://') || $forwardedHttps) {
            URL::forceScheme('https');
        }

        // Dynamically override mail and google services config using database settings
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                $settings = \App\Models\SystemSetting::all()->pluck('value', 'key')->toArray();

                // Override Mail config if host settings exist
                if (!empty($settings['mail_host'])) {
                    config([
                        'mail.default' => 'smtp',
                        'mail.mailers.smtp.host' => $settings['mail_host'],
                        'mail.mailers.smtp.port' => intval($settings['mail_port'] ?? 587),
                        'mail.mailers.smtp.username' => $settings['mail_username'] ?? null,
                        'mail.mailers.smtp.password' => $settings['mail_password'] ?? null,
                        'mail.mailers.smtp.encryption' => $settings['mail_encryption'] ?? 'tls',
                        'mail.from.address' => $settings['mail_from_address'] ?? 'no-reply@bpkh.go.id',
                        'mail.from.name' => $settings['mail_from_name'] ?? config('app.name'),
                    ]);
                }

                // Override Google Client API config if client ID exists
                if (!empty($settings['google_client_id'])) {
                    config([
                        'services.google.client_id' => $settings['google_client_id'],
                        'services.google.client_secret' => $settings['google_client_secret'] ?? null,
                        'services.google.redirect' => $settings['google_redirect_uri'] ?? 'http://localhost/auth/google/callback',
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silently ignore database errors during setup / early migrations
        }
    }
}