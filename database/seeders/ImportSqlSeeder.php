<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportSqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
        $files = [
            database_path('sql/domestic_tableConvert.com_liwrrt.sql'),
            database_path('sql/internation_tableConvert.com_pc6l8a.sql'),
        ];
        foreach ($files as $path) {
            if (File::exists($path)) {
                $sql = File::get($path);
                DB::unprepared($sql);
            } else {
                $this->command->error("File not found: " . $path);
            }
        }
    }
}
