<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    protected array $settings;


    public function __construct()
    {
        $this->settings = [
            [
                'friendly_name' => [
                    'default' => null,
                    'display_name' => 'Friendly Name',
                    'value_type' => 'text'
                ]
            ], // General
            [
                'crawler_delay' => [
                    'default' => 100,
                    'display_name' => 'Crawler Delay',
                    'value_type' => 'number'
                ],
                'respect_robots' =>[
                    'default' => true,
                    'display_name' => 'Respect Robots',
                    'value_type' => 'checkbox'
                ],
                'execute_js' => [
                    'default' => true,
                    'display_name' => 'Execute Javascript',
                    'value_type' => 'checkbox'
                ],
                'nofollow_links' => [
                    'default' => false,
                    'display_name' => 'Visit nofollow links',
                    'value_type' => 'checkbox'
                ],
                'mime_types' => [
                    'default' => 'text/html,text/plain',
                    'display_name' => 'Allowed MIME Types',
                    'value_type' => 'text'
                ]
            ], // Broken Links
            [
                'something' => [
                    'default' => null,
                    'display_name' => 'Something',
                    'value_type' => 'text'
                ],
            ], // Lighthouse
            [
                'broken_routes' => [
                    'default' => true,
                    'display_name' => 'Broken routes',
                    'value_type' => 'checkbox'
                ],
                'lighthouse' => [
                    'default' => true,
                    'display_name' => 'Lighthouse',
                    'value_type' => 'checkbox'
                ],
            ] // Monitors
        ];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->settings as $key => $value) {
            foreach ($value as $setting => $values) {
                Setting::create([
                    'group_id' => $key,
                    'name' => $setting,
                    'default_value' => $values['default'],
                    'value_type' => $values['value_type'],
                    'display_name' => $values['display_name'],
                ]);
            }
        }
    }
}
