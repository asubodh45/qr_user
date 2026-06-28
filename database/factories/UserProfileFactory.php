<?php

namespace Database\Factories;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'phone'      => fake()->unique()->numerify('98########'),
            'email'      => fake()->unique()->safeEmail(),
            'address'    => fake()->address(),
            // qr_token is auto-generated in UserProfile::booted()
            'created_at' => fake()->dateTimeBetween('-12 months', 'now'),
            'updated_at' => now(),
        ];
    }
}
