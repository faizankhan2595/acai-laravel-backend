<?php

use App\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'key'   => 'project_title',
            'value' => 'Project Acai'
        ]);

        Setting::create([
            'key'   => 'contact_email',
            'value' => 'hello@project-acai.com'
        ]);

        Setting::create([
            'key'   => 'contact_site',
            'value' => 'https://project-acai.com'
        ]);

        Setting::create([
            'key'   => 'birthday_reward',
            'value' => '30'
        ]);

        Setting::create([
            'key'   => 'default_exipry',
            'value' => 30
        ]);

        Setting::create([
            'key'   => 'gold_membership_points',
            'value' => '1000'
        ]);

        Setting::create([
            'key'   => 'gold_membership_charge',
            'value' => '100'
        ]);

        Setting::create([
            'key'   => 'acai_reward_guide_pdf',
            'value' => null
        ]);

        Setting::create([
            'key'   => 'gold_member_conversion',
            'value' => null
        ]);

        Setting::create([
            'key'   => 'purple_member_conversion',
            'value' => null
        ]);

        Setting::create([
            'key'   => 'sales_home_banner',
            'value' => null
        ]);

        Setting::create([
            'key'   => 'sales_home_heading',
            'value' => null
        ]);

        Setting::create([
            'key'   => 'sales_home_subheading',
            'value' => null
        ]);
    }
}
