<?php

namespace Database\Seeders;

use App\Models\SettingGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingGroupSeeder extends Seeder
{
    protected array $groups = ["General", "Broken Links", "Lighthouse", "Monitors"];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->groups as $group)
        {
            SettingGroup::create([
                'name' => $group
            ]);
        }
    }
}
