<?php

namespace Database\Seeders;

use App\Models\QrScan;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class QrScanSeeder extends Seeder
{
    private array $cities = [
        'Nepal'      => ['Kathmandu', 'Pokhara', 'Lalitpur'],
        'India'      => ['Delhi', 'Mumbai', 'Bangalore'],
        'Bangladesh' => ['Dhaka', 'Chittagong'],
        'USA'        => ['New York', 'Los Angeles', 'Chicago'],
        'UK'         => ['London', 'Manchester', 'Birmingham'],
    ];

    public function run(): void
    {
        $profileIds = UserProfile::pluck('id')->toArray();

        if (empty($profileIds)) {
            $this->command->warn('No user profiles found. Run UserProfileSeeder first.');
            return;
        }

        // First 10 users get ~60 % of all scans so "Most Scanned Member" is meaningful
        $popularIds  = array_slice($profileIds, 0, min(10, count($profileIds)));
        $countryKeys = array_keys($this->cities);
        $browsers    = ['Chrome', 'Edge', 'Safari', 'Firefox', 'Opera'];
        $oses        = ['Android', 'iOS', 'Windows', 'macOS', 'Linux'];
        $devices     = ['Mobile', 'Desktop', 'Tablet'];

        $rows = [];
        $now  = now();

        for ($i = 0; $i < 200; $i++) {
            $userId    = (rand(1, 100) <= 60)
                ? $popularIds[array_rand($popularIds)]
                : $profileIds[array_rand($profileIds)];
            $country   = $countryKeys[array_rand($countryKeys)];
            $city      = $this->cities[$country][array_rand($this->cities[$country])];
            $scannedAt = $now->copy()
                ->subDays(rand(0, 59))
                ->subHours(rand(0, 23))
                ->subMinutes(rand(0, 59))
                ->toDateTimeString();

            $rows[] = [
                'user_profile_id'  => $userId,
                'ip_address'       => rand(1, 255).'.'.rand(0, 255).'.'.rand(0, 255).'.'.rand(0, 255),
                'country'          => $country,
                'city'             => $city,
                'browser'          => $browsers[array_rand($browsers)],
                'operating_system' => $oses[array_rand($oses)],
                'device'           => $devices[array_rand($devices)],
                'scanned_at'       => $scannedAt,
                'created_at'       => $scannedAt,
                'updated_at'       => $scannedAt,
            ];
        }

        foreach (array_chunk($rows, 50) as $chunk) {
            QrScan::insert($chunk);
        }

        $this->command->info('200 QR scan records seeded.');
    }
}
