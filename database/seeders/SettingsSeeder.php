<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Site Settings
            'site_name' => 'SMM Admin Panel',
            'site_description' => 'Professional Social Media Marketing Panel',
            'site_logo' => null,
            'site_favicon' => null,
            'currency' => 'USD',
            'timezone' => 'UTC',

            // Payment Settings
            'paypal_enabled' => false,
            'paypal_mode' => 'sandbox',
            'paypal_client_id' => '',
            'paypal_secret' => '',

            'stripe_enabled' => false,
            'stripe_key' => '',
            'stripe_secret' => '',
            'stripe_webhook_secret' => '',

            'manual_payment_enabled' => true,
            'manual_payment_instructions' => 'Please contact admin for payment instructions.',

            // Email Settings
            'mail_from_address' => 'admin@admin.com',
            'mail_from_name' => 'SMM Admin',

            // Social Media Links
            'facebook_url' => '',
            'twitter_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',

            // Account Limits
            'facebook_account_daily_use_limit' => 4,
            'facebook_reset_last_run' => null,
            
            // Terms and Privacy
            'terms_content' => 'Your terms and conditions content here.',
            'privacy_content' => 'Your privacy policy content here.',
            
            // System Notification
            'system_notification_active' => false,
            'system_notification_message' => 'We are currently experiencing high volume of orders. New orders will be accepted tomorrow. Thank you for your patience!',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
} 