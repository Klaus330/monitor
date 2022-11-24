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
                    'value_type' => 'text',
                    'description' => 'If you specify a friendly name we\'ll display this instead of the url.',
                ]
            ], // General
            [
                'crawler_delay' => [
                    'default' => 100,
                    'display_name' => 'Crawler Delay',
                    'value_type' => 'number',
                    'description' => 'Set the delay between requests.',
                ],
                'respect_robots' => [
                    'default' => true,
                    'display_name' => 'Respect Robots',
                    'value_type' => 'checkbox',
                    'description' => 'Tell the crawler if it should respect the robots.txt file.',
                ],
                'execute_js' => [
                    'default' => true,
                    'display_name' => 'Execute Javascript',
                    'value_type' => 'checkbox',
                    'description' => 'Tell us if your site needs to execute JavaScript before crawling.',
                ],
                'nofollow_links' => [
                    'default' => false,
                    'display_name' => 'Visit nofollow links',
                    'value_type' => 'checkbox',
                    'description' => 'May the crawler visit nofollow links?',
                ],
                'mime_types' => [
                    'default' => 'text/html,text/plain',
                    'display_name' => 'Allowed MIME Types',
                    'value_type' => 'text',
                    'description' => 'Specify the MIME types the crawler should visit.',
                ]
            ], // Broken Links
            [
                'something' => [
                    'default' => null,
                    'display_name' => 'Something',
                    'value_type' => 'text',
                    'description' => null,
                ],
            ], // Lighthouse
            [
                'broken_routes' => [
                    'default' => true,
                    'display_name' => 'Broken routes',
                    'value_type' => 'checkbox',
                    'description' => null,
                ],
                'lighthouse' => [
                    'default' => true,
                    'display_name' => 'Lighthouse',
                    'value_type' => 'checkbox',
                    'description' => null,
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
                    'group_id' => $key + 1,
                    'name' => $setting,
                    'default_value' => $values['default'],
                    'value_type' => $values['value_type'],
                    'display_name' => $values['display_name'],
                    'description' => $values['description'],
                ]);
            }
        }
    }
}
