<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crops = [
            // Cereals
            ['name' => 'Wheat', 'category' => 'Cereal'],
            ['name' => 'Maize', 'category' => 'Cereal'],
            ['name' => 'Bajra', 'category' => 'Cereal'],
            ['name' => 'Jowar', 'category' => 'Cereal'],
            ['name' => 'Rice', 'category' => 'Cereal'],
            
            // Vegetables
            ['name' => 'Onion', 'category' => 'Vegetable'],
            ['name' => 'Tomato', 'category' => 'Vegetable'],
            ['name' => 'Potato', 'category' => 'Vegetable'],
            ['name' => 'Chili', 'category' => 'Vegetable'],
            ['name' => 'Garlic', 'category' => 'Vegetable'],
            ['name' => 'Ginger', 'category' => 'Vegetable'],
            ['name' => 'Cabbage', 'category' => 'Vegetable'],
            ['name' => 'Cauliflower', 'category' => 'Vegetable'],
            
            // Fruits
            ['name' => 'Pomegranate', 'category' => 'Fruit'],
            ['name' => 'Grapes', 'category' => 'Fruit'],
            ['name' => 'Banana', 'category' => 'Fruit'],
            ['name' => 'Papaya', 'category' => 'Fruit'],
            ['name' => 'Mango', 'category' => 'Fruit'],
            ['name' => 'Lemon', 'category' => 'Fruit'],
            
            // Cash Crops & Commercial
            ['name' => 'Sugarcane', 'category' => 'Cash Crop'],
            ['name' => 'Cotton', 'category' => 'Fiber'],
            ['name' => 'Tobacco', 'category' => 'Commercial'],
            
            // Oilseeds & Spices
            ['name' => 'Soybean', 'category' => 'Oilseed'],
            ['name' => 'Groundnut', 'category' => 'Oilseed'],
            ['name' => 'Sunflower', 'category' => 'Oilseed'],
            ['name' => 'Mustard', 'category' => 'Oilseed'],
            ['name' => 'Turmeric', 'category' => 'Spice'],
            
            // Pulses
            ['name' => 'Tur (Pigeon Pea)', 'category' => 'Pulse'],
            ['name' => 'Moong (Green Gram)', 'category' => 'Pulse'],
            ['name' => 'Chana (Gram)', 'category' => 'Pulse'],
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
    }
}
