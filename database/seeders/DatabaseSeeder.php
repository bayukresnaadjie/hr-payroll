<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DepartemenJabatanSeeder::class,
            KaryawanSeeder::class,
            AbsensiSeeder::class,
            CutiSeeder::class,
            LemburSeeder::class,
            PenggajianSeeder::class,
        ]);
    }
}
