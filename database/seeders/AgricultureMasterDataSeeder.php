<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Crop;
use App\Models\IrrigationType;
use App\Models\LandUnit;
use App\Models\AccountType;
use Illuminate\Support\Str;

class AgricultureMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Default Crops
        $crops = [
            ['name' => 'Cotton', 'category' => 'Fiber'],
            ['name' => 'Wheat', 'category' => 'Cereal'],
            ['name' => 'Rice', 'category' => 'Cereal'],
            ['name' => 'Groundnut', 'category' => 'Oilseed'],
            ['name' => 'Soybean', 'category' => 'Oilseed'],
            ['name' => 'Sugar Cane', 'category' => 'Cash Crop'],
            ['name' => 'Maize', 'category' => 'Cereal'],
            ['name' => 'Castor', 'category' => 'Oilseed'],
            ['name' => 'Cumin', 'category' => 'Spice'],
            ['name' => 'Mustard', 'category' => 'Oilseed'],
        ];

        foreach ($crops as $crop) {
            Crop::updateOrCreate(
                ['name' => $crop['name']],
                [
                    'slug' => Str::slug($crop['name']),
                    'category' => $crop['category'],
                    'is_active' => true
                ]
            );
        }

        // 2. Irrigation Types
        $irrigationTypes = [
            'Drip Irrigation',
            'Sprinkler Irrigation',
            'Flood Irrigation',
            'Canal Irrigation',
            'Borewell',
            'Rainfed',
        ];

        foreach ($irrigationTypes as $type) {
            IrrigationType::updateOrCreate(
                ['name' => $type],
                ['is_active' => true]
            );
        }

        // 3. Land Units
        $landUnits = [
            ['name' => 'Vigha', 'code' => 'vigha'],
            ['name' => 'Acre', 'code' => 'acre'],
            ['name' => 'Hectare', 'code' => 'ha'],
            ['name' => 'Guntha', 'code' => 'guntha'],
            ['name' => 'Square Meter', 'code' => 'sqm'],
        ];

        foreach ($landUnits as $unit) {
            LandUnit::updateOrCreate(
                ['name' => $unit['name']],
                [
                    'code' => $unit['code'],
                    'is_active' => true
                ]
            );
        }

        // 4. Account Types (For ERP Registration)
        $accountTypes = [
            ['name' => 'Farmer', 'color' => 'green'],
            ['name' => 'Vendor', 'color' => 'blue'],
            ['name' => 'Agent', 'color' => 'orange'],
            ['name' => 'Staff', 'color' => 'indigo'],
            ['name' => 'Corporate', 'color' => 'purple'],
        ];

        foreach ($accountTypes as $type) {
            AccountType::updateOrCreate(
                ['name' => $type['name']],
                [
                    'slug' => Str::slug($type['name']),
                    'color_class' => $type['color'],
                    'is_active' => true
                ]
            );
        }
    }
}
