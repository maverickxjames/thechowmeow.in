<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::all()->pluck('value', 'key');
                
                // Override App Name
                if ($settings->has('app_name')) {
                    Config::set('app.name', $settings['app_name']);
                }

                // SMTP Configuration
                if ($settings->has('smtp_host')) Config::set('mail.mailers.smtp.host', $settings['smtp_host']);
                if ($settings->has('smtp_port')) Config::set('mail.mailers.smtp.port', $settings['smtp_port']);
                if ($settings->has('smtp_username')) Config::set('mail.mailers.smtp.username', $settings['smtp_username']);
                if ($settings->has('smtp_password')) Config::set('mail.mailers.smtp.password', $settings['smtp_password']);
                if ($settings->has('smtp_from_address')) {
                    Config::set('mail.from.address', $settings['smtp_from_address']);
                    Config::set('mail.from.name', $settings['app_name'] ?? config('app.name'));
                }

                // Currency Toggles
                if ($settings->has('enable_usd')) Config::set('petwear.enable_usd', $settings['enable_usd'] === '1');
                if ($settings->has('usd_exchange_rate')) Config::set('petwear.usd_exchange_rate', $settings['usd_exchange_rate']);

                // Payment Toggles
                if ($settings->has('enable_cod')) Config::set('petwear.enable_cod', $settings['enable_cod'] === '1');
                if ($settings->has('enable_razorpay')) Config::set('petwear.enable_razorpay', $settings['enable_razorpay'] === '1');
            }
        } catch (\Exception $e) {
            // Fails silently if DB is not set up perfectly yet.
        }
    }
}
