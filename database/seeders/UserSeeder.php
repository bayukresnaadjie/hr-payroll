<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name'=>'Super Admin',    'email'=>'admin@hr.test',    'role'=>'superadmin'],
            ['name'=>'HR Manager',     'email'=>'hr@hr.test',       'role'=>'hr_manager'],
            ['name'=>'Finance Staff',  'email'=>'finance@hr.test',  'role'=>'finance'],
            ['name'=>'Budi Santoso',   'email'=>'budi@hr.test',     'role'=>'karyawan'],
            ['name'=>'Siti Rahayu',    'email'=>'siti@hr.test',     'role'=>'karyawan'],
            ['name'=>'Andi Wijaya',    'email'=>'andi@hr.test',     'role'=>'karyawan'],
            ['name'=>'Dewi Kusuma',    'email'=>'dewi@hr.test',     'role'=>'karyawan'],
            ['name'=>'Rudi Hartono',   'email'=>'rudi@hr.test',     'role'=>'karyawan'],
            ['name'=>'Maya Sari',      'email'=>'maya@hr.test',     'role'=>'karyawan'],
            ['name'=>'Fajar Nugroho',  'email'=>'fajar@hr.test',    'role'=>'karyawan'],
            ['name'=>'Lina Marlina',   'email'=>'lina@hr.test',     'role'=>'karyawan'],
        ];

        foreach ($users as $u) {
            User::create([
                'name'              => $u['name'],
                'email'             => $u['email'],
                'role'              => $u['role'],
                'aktif'             => true,
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
