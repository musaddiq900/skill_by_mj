<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Super Admin System',
                'type' => 'string',
                'group' => 'general',
                'description' => 'The name of the website',
            ],
            [
                'key' => 'site_description',
                'value' => 'A powerful Laravel-based CMS and Admin System',
                'type' => 'text',
                'group' => 'general',
                'description' => 'The description of the website',
            ],
            [
                'key' => 'site_email',
                'value' => 'admin@example.com',
                'type' => 'string',
                'group' => 'general',
                'description' => 'The default email address for the site',
            ],
            [
                'key' => 'site_timezone',
                'value' => 'UTC',
                'type' => 'string',
                'group' => 'general',
                'description' => 'The default timezone for the site',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'general',
                'description' => 'Put the site in maintenance mode',
            ],
            [
                'key' => 'user_registration',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'users',
                'description' => 'Allow new users to register',
            ],
            [
                'key' => 'email_verification',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'users',
                'description' => 'Require email verification for new users',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}