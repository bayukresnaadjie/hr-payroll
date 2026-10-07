<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departemen;
use App\Models\Jabatan;

class DepartemenJabatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode' => 'HRD', 'nama' => 'Human Resources', 'kepala' => 'HR Manager',
                'jabatan' => [
                    ['kode'=>'HRD-MGR', 'nama'=>'HR Manager',       'level'=>'manager',    'min'=>12_000_000, 'max'=>18_000_000],
                    ['kode'=>'HRD-SPV', 'nama'=>'HR Supervisor',     'level'=>'supervisor', 'min'=>8_000_000,  'max'=>12_000_000],
                    ['kode'=>'HRD-STF', 'nama'=>'HR Staff',          'level'=>'staff',      'min'=>4_000_000,  'max'=>7_000_000],
                ],
            ],
            [
                'kode' => 'FIN', 'nama' => 'Finance & Accounting', 'kepala' => 'Finance Manager',
                'jabatan' => [
                    ['kode'=>'FIN-MGR', 'nama'=>'Finance Manager',   'level'=>'manager',    'min'=>13_000_000, 'max'=>20_000_000],
                    ['kode'=>'FIN-ACC', 'nama'=>'Accountant',        'level'=>'staff',      'min'=>5_000_000,  'max'=>9_000_000],
                    ['kode'=>'FIN-STF', 'nama'=>'Finance Staff',     'level'=>'staff',      'min'=>4_000_000,  'max'=>7_000_000],
                ],
            ],
            [
                'kode' => 'TI', 'nama' => 'Technology & IT', 'kepala' => 'IT Manager',
                'jabatan' => [
                    ['kode'=>'TI-MGR',  'nama'=>'IT Manager',        'level'=>'manager',    'min'=>15_000_000, 'max'=>25_000_000],
                    ['kode'=>'TI-DEV',  'nama'=>'Software Developer', 'level'=>'staff',     'min'=>7_000_000,  'max'=>15_000_000],
                    ['kode'=>'TI-NET',  'nama'=>'Network Engineer',   'level'=>'staff',     'min'=>6_000_000,  'max'=>12_000_000],
                ],
            ],
            [
                'kode' => 'MKT', 'nama' => 'Marketing & Sales', 'kepala' => 'Marketing Manager',
                'jabatan' => [
                    ['kode'=>'MKT-MGR', 'nama'=>'Marketing Manager', 'level'=>'manager',    'min'=>12_000_000, 'max'=>20_000_000],
                    ['kode'=>'MKT-SPV', 'nama'=>'Marketing Supervisor','level'=>'supervisor','min'=>7_000_000, 'max'=>12_000_000],
                    ['kode'=>'MKT-STF', 'nama'=>'Marketing Staff',   'level'=>'staff',      'min'=>4_000_000,  'max'=>8_000_000],
                ],
            ],
            [
                'kode' => 'OPS', 'nama' => 'Operations', 'kepala' => 'Operations Manager',
                'jabatan' => [
                    ['kode'=>'OPS-MGR', 'nama'=>'Operations Manager','level'=>'manager',    'min'=>12_000_000, 'max'=>18_000_000],
                    ['kode'=>'OPS-SPV', 'nama'=>'Operations Supervisor','level'=>'supervisor','min'=>7_000_000,'max'=>11_000_000],
                    ['kode'=>'OPS-STF', 'nama'=>'Operations Staff',  'level'=>'staff',      'min'=>3_500_000,  'max'=>6_000_000],
                ],
            ],
        ];

        foreach ($data as $dep) {
            $departemen = Departemen::create([
                'kode'    => $dep['kode'],
                'nama'    => $dep['nama'],
                'kepala'  => $dep['kepala'],
                'aktif'   => true,
            ]);

            foreach ($dep['jabatan'] as $jab) {
                Jabatan::create([
                    'departemen_id'   => $departemen->id,
                    'kode'            => $jab['kode'],
                    'nama'            => $jab['nama'],
                    'level'           => $jab['level'],
                    'gaji_pokok_min'  => $jab['min'],
                    'gaji_pokok_max'  => $jab['max'],
                    'aktif'           => true,
                ]);
            }
        }
    }
}
