<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Chirp;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()
            ->has(Chirp::factory()->count(200))
            ->has(
                User::factory()
                    ->count(30)
                    ->has(Chirp::factory()->count(200)),
                'following'
            )
            ->has(
                User::factory()
                    ->count(30)
                    ->has(Chirp::factory()->count(200)),
                'followers'
            )
            ->createQuietly(['email' => 'test@test.com']);
    }
}
