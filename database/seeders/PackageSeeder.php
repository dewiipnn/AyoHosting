<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::create([
            'name' => 'Starter',
            'price' => 15000,
            'storage' => '10 GB SSD',
            'websites' => '1',
            'type' => 'shared'
        ]);

        Package::create([
            'name' => 'Pro',
            'price' => 45000,
            'storage' => '50 GB NVMe',
            'websites' => '5',
            'type' => 'shared'
        ]);

        Package::create([
            'name' => 'Business',
            'price' => 90000,
            'storage' => 'Unlimited NVMe',
            'websites' => 'Unlimited',
            'type' => 'cloud'
        ]);
    }
}
