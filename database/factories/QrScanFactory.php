<?php

namespace Database\Factories;

use App\Models\QrScan;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class QrScanFactory extends Factory
{
    protected $model = QrScan::class;

    private array $cities = [
        'Nepal'      => ['Kathmandu', 'Pokhara', 'Lalitpur'],
        'India'      => ['Delhi', 'Mumbai', 'Bangalore'],
        'Bangladesh' => ['Dhaka', 'Chittagong'],
        'USA'        => ['New York', 'Los Angeles', 'Chicago'],
        'UK'         => ['London', 'Manchester', 'Birmingham'],
    ];

    public function definition(): array
    {
        $country   = fake()->randomElement(array_keys($this->cities));
        $city      = fake()->randomElement($this->cities[$country]);
        $scannedAt = fake()->dateTimeBetween('-60 days', 'now');

        return [
            'user_profile_id'  => UserProfile::inRandomOrder()->value('id') ?? UserProfile::factory(),
            'ip_address'       => fake()->ipv4(),
            'country'          => $country,
            'city'             => $city,
            'browser'          => fake()->randomElement(['Chrome', 'Edge', 'Safari', 'Firefox', 'Opera']),
            'operating_system' => fake()->randomElement(['Android', 'iOS', 'Windows', 'macOS', 'Linux']),
            'device'           => fake()->randomElement(['Mobile', 'Desktop', 'Tablet']),
            'scanned_at'       => $scannedAt,
            'created_at'       => $scannedAt,
            'updated_at'       => $scannedAt,
        ];
    }
}
