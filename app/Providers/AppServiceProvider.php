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
        $this->forcePublicRootUrl();

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

    /**
     * Keep generated redirects/assets on the public domain, never an internal IP.
     */
    private function forcePublicRootUrl(): void
    {
        $appUrl = rtrim((string) config('app.url'), '/');
        $appHost = $this->publicHostname(parse_url($appUrl, PHP_URL_HOST));
        $requestHost = null;
        $requestScheme = null;

        if (! $this->app->runningInConsole()) {
            $request = request();
            $forwardedHost = $request->header('X-Forwarded-Host');
            if (is_string($forwardedHost) && $forwardedHost !== '') {
                $forwardedHost = trim(explode(',', $forwardedHost)[0]);
            }

            $requestHost = $this->publicHostname(
                is_string($forwardedHost) && $forwardedHost !== ''
                    ? $forwardedHost
                    : $request->getHost()
            );

            $forwardedProto = $request->header('X-Forwarded-Proto');
            if (is_string($forwardedProto) && $forwardedProto !== '') {
                $requestScheme = strtolower(trim(explode(',', $forwardedProto)[0]));
            } else {
                $requestScheme = $request->getScheme();
            }
        }

        $host = $requestHost ?? $appHost;
        if (! $host) {
            return;
        }

        $scheme = $requestScheme
            ?: (str_starts_with($appUrl, 'https://') ? 'https' : 'http');

        URL::forceRootUrl($scheme.'://'.$host);

        if ($scheme === 'https' || str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }
    }

    private function publicHostname(mixed $host): ?string
    {
        $host = strtolower(trim((string) $host));
        $host = explode(':', $host)[0];

        if ($host === '' || $host === 'localhost' || str_ends_with($host, '.local')) {
            return null;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return null;
        }

        return $host;
    }
}
