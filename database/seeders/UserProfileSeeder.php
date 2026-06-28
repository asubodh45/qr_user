<?php

namespace Database\Seeders;

use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        UserProfile::factory(100)->create();

        $this->command->info('100 user profiles seeded.');
    }
}
