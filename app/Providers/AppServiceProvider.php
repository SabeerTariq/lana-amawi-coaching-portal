<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;

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
        // Dynamically configure mail settings from database
        $this->configureMailFromDatabase();
    }

    /**
     * Configure mail settings from database, fallback to .env
     */
    protected function configureMailFromDatabase(): void
    {
        try {
            // Check if settings table exists (to avoid errors during migrations)
            if (!\Schema::hasTable('settings')) {
                return;
            }

            // Get SMTP settings from database
            $mailMailer = Setting::get('mail_mailer');
            $mailHost = Setting::get('mail_host');
            $mailPort = Setting::get('mail_port');
            $mailUsername = Setting::get('mail_username');
            $mailPassword = Setting::get('mail_password');
            $mailEncryption = Setting::get('mail_encryption');
            $mailFromAddress = Setting::get('mail_from_address');
            $mailFromName = Setting::get('mail_from_name');

            // Only update config if database has values (otherwise use .env defaults)
            if ($mailMailer) {
                Config::set('mail.default', $mailMailer);
            }

            if ($mailHost) {
                Config::set('mail.mailers.smtp.host', $mailHost);
            }

            if ($mailPort) {
                Config::set('mail.mailers.smtp.port', $mailPort);
            }

            if ($mailUsername !== null) {
                Config::set('mail.mailers.smtp.username', $mailUsername);
            }

            if ($mailPassword !== null) {
                Config::set('mail.mailers.smtp.password', $mailPassword);
            }

            if ($mailEncryption !== null) {
                Config::set('mail.mailers.smtp.encryption', $mailEncryption);
            }

            if ($mailFromAddress) {
                Config::set('mail.from.address', $mailFromAddress);
            }

            if ($mailFromName) {
                Config::set('mail.from.name', $mailFromName);
            }
        } catch (\Exception $e) {
            // Silently fail if settings table doesn't exist or query fails
            // This prevents errors during migrations or when database is not ready
            \Log::debug('Could not load mail settings from database: ' . $e->getMessage());
        }
    }
}
