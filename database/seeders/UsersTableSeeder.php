<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timezones = ["CET", "CST", "GMT+1"];

        User::factory(20)->create()->each(function ($user) use ($timezones) {
            $user->update([
                'time_zone' => fake()->randomElement($timezones),
            ]);
        });
    }
}
