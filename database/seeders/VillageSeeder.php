<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Village;

class VillageSeeder extends Seeder
{
    public function run(): void
    {
        $villages = [
            [
                'village_name' => 'Bantwa',
                'pincode' => '362620',
                'post_so_name' => 'Bantwa S.O',
                'taluka_name' => 'Manavadar',
                'district_name' => 'Junagadh',
                'state_name' => 'Gujarat'
            ],
            [
                'village_name' => 'Manavadar',
                'pincode' => '362630',
                'post_so_name' => 'Manavadar S.O',
                'taluka_name' => 'Manavadar',
                'district_name' => 'Junagadh',
                'state_name' => 'Gujarat'
            ],
            [
                'village_name' => 'Kutiyana',
                'pincode' => '362650',
                'post_so_name' => 'Kutiyana S.O',
                'taluka_name' => 'Kutiyana',
                'district_name' => 'Porbandar',
                'state_name' => 'Gujarat'
            ],
            [
                'village_name' => 'Ranavav',
                'pincode' => '360550',
                'post_so_name' => 'Ranavav S.O',
                'taluka_name' => 'Ranavav',
                'district_name' => 'Porbandar',
                'state_name' => 'Gujarat'
            ],
            [
                'village_name' => 'Upleta',
                'pincode' => '360490',
                'post_so_name' => 'Upleta S.O',
                'taluka_name' => 'Upleta',
                'district_name' => 'Rajkot',
                'state_name' => 'Gujarat'
            ],
            [
                'village_name' => 'Dhoraji',
                'pincode' => '360410',
                'post_so_name' => 'Dhoraji S.O',
                'taluka_name' => 'Dhoraji',
                'district_name' => 'Rajkot',
                'state_name' => 'Gujarat'
            ],
            [
                'village_name' => 'Gondal',
                'pincode' => '360311',
                'post_so_name' => 'Gondal S.O',
                'taluka_name' => 'Gondal',
                'district_name' => 'Rajkot',
                'state_name' => 'Gujarat'
            ],
            [
                'village_name' => 'Jetpur',
                'pincode' => '360370',
                'post_so_name' => 'Jetpur S.O',
                'taluka_name' => 'Jetpur',
                'district_name' => 'Rajkot',
                'state_name' => 'Gujarat'
            ],
            [
                'village_name' => 'Bhayavadar',
                'pincode' => '360450',
                'post_so_name' => 'Bhayavadar S.O',
                'taluka_name' => 'Upleta',
                'district_name' => 'Rajkot',
                'state_name' => 'Gujarat'
            ],
            [
                'village_name' => 'Paneli Moti',
                'pincode' => '360480',
                'post_so_name' => 'Paneli Moti B.O',
                'taluka_name' => 'Upleta',
                'district_name' => 'Rajkot',
                'state_name' => 'Gujarat'
            ],
        ];

        foreach ($villages as $village) {
            Village::create($village);
        }
    }
}
