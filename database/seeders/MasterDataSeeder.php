<?php

namespace Database\Seeders;

use App\Models\AccountType;
use App\Models\IrrigationType;
use App\Models\LandUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Land Units
        $units = [
            ['name' => 'Acre', 'code' => 'acre'],
            ['name' => 'Hectare', 'code' => 'hectare'],
            ['name' => 'Bigha', 'code' => 'bigha'],
            ['name' => 'Guntha', 'code' => 'guntha'],
            ['name' => 'Square Meter', 'code' => 'sqm'],
        ];
        foreach ($units as $unit) {
            LandUnit::updateOrCreate(['code' => $unit['code']], $unit);
        }

        // Irrigation Types
        $irrigation = ['Borewell', 'Canal', 'Rainfed', 'Well', 'Drip', 'Sprinkler', 'River'];
        foreach ($irrigation as $type) {
            IrrigationType::updateOrCreate(['name' => $type]);
        }

        // Account Types
        $accounts = [
            ['name' => 'Farmer (Producer)', 'slug' => 'farmer', 'color' => 'bg-green-lt'],
            ['name' => 'Retail Customer', 'slug' => 'customer', 'color' => 'bg-blue-lt'],
            ['name' => 'Material Vendor', 'slug' => 'vendor', 'color' => 'bg-purple-lt'],
            ['name' => 'Wholesale Dealer', 'slug' => 'dealer', 'color' => 'bg-orange-lt'],
            ['name' => 'Bulk Buyer', 'slug' => 'buyer', 'color' => 'bg-cyan-lt'],
        ];
        foreach ($accounts as $acc) {
            AccountType::updateOrCreate(['slug' => $acc['slug']], [
                'name' => $acc['name'],
                'color_class' => $acc['color']
            ]);
        }
    }
}
