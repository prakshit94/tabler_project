<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Village;
use Illuminate\Support\Facades\DB;

class VillageSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('villages.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found at: {$csvFile}");
            return;
        }

        $handle = fopen($csvFile, 'r');
        $header = fgetcsv($handle); // Read header row

        $batchSize = 1000;
        $data = [];
        $count = 0;

        DB::table('villages')->truncate();

        while (($row = fgetcsv($handle)) !== false) {
            $record = array_combine($header, $row);
            
            // Map CSV columns to table columns
            $data[] = [
                'village_name' => $record['village_name'],
                'pincode'      => $record['pincode'],
                'post_so_name' => $record['post_so_name'],
                'taluka_name'  => $record['taluka name'] === '#N/A' ? null : $record['taluka name'],
                'district_name'=> $record['District_name'] === '#N/A' ? null : $record['District_name'],
                'state_name'   => $record['state name'] === '#N/A' ? null : $record['state name'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ];

            if (count($data) >= $batchSize) {
                DB::table('villages')->insert($data);
                $data = [];
                $count += $batchSize;
                $this->command->info("Imported {$count} villages...");
            }
        }

        if (count($data) > 0) {
            DB::table('villages')->insert($data);
        }

        fclose($handle);
        $this->command->info('Village import completed successfully.');
    }
}
