<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'name' => "Claudiu",
            'email' => "claudiupopa330@gmail.com",
            'password' => bcrypt("I@mCl@udi0")
        ]);

        $this->call([
            SettingGroupSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
