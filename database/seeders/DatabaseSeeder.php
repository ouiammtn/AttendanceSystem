<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
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
        if (!User::where('email', 'admin@ensam-casa.com')->exists()) {
            User::create([
                'name' => 'Wardi Ahmed',
                'email' => 'admin@ensam-casa.ma',
                'email_verified_at' => Carbon::now(),
                'password' => bcrypt('password'),
                'role' => 'Admin'
            ]);

        }
        $this->call(SettingSeeder::class);
    }
}
