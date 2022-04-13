<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        User::query()->updateOrCreate(
            ['email' => 'admin@admin.com',
            ],
            [
                'type' => User::TYPE_ADMIN,
                'name' => 'Shop Owner',
                'password' => 'secret',
                'email_verified_at' => now(),
            ]);

        User::query()->updateOrCreate(
            [
                'email' => 'user@user.com',

            ],
            [
                'type' => User::TYPE_USER,
                'name' => 'store staff',
                'password' => 'secret',
                'email_verified_at' => now(),
            ]);

    }
}
