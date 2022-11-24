<?php

namespace Database\Seeders;

use App\Models\SettingGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingGroupSeeder extends Seeder
{
    protected array $groups = [
        "General" => 'general',
        "Broken Routes" => 'broken_routes',
        "Lighthouse" => 'lighthouse',
        "Monitors" => 'monitors'
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->groups as $groupDisplayName => $groupName) {
            SettingGroup::create([
                'name' => $groupName,
                'display_name' => $groupDisplayName
            ]);
        }
    }
}
