<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\JatahCuti;
use App\Models\User;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $karyawanData = [
            // HRD
            [
                'user_email'  => 'hr@hr.test',
                'jabatan_kode'=> 'HRD-MGR',
                'nik'         => 'KRY-0001',
                'nama'        => 'HR Manager',
                'jk'          => 'P',
                'lahir'       => '1985-03-15',
                'masuk'       => '2018-01-02',
                'gaji'        => 14000000,
                'tj_jab'      => 2000000,
                'tj_tr'       => 500000,
                'tj_mk'       => 300000,
                'kawin'       => 'K1',   // sudah kawin 1 anak
                'pajak'       => 'K1',
            ],
            [
                'user_email'  => null,
                'jabatan_kode'=> 'HRD-STF',
                'nik'         => 'KRY-0002',
                'nama'        => 'Budi Santoso',
                'jk'          => 'L',
                'lahir'       => '1992-07-20',
                'masuk'       => '2020-03-01',
                'gaji'        => 5500000,
                'tj_jab'      => 500000,
                'tj_tr'       => 300000,
                'tj_mk'       => 200000,
                'kawin'       => 'K',    // PERBAIKAN: pakai 'K' bukan 'K0'
                'pajak'       => 'K0',
            ],
            // Finance
            [
                'user_email'  => 'finance@hr.test',
                'jabatan_kode'=> 'FIN-ACC',
                'nik'         => 'KRY-0003',
                'nama'        => 'Finance Staff',
                'jk'          => 'P',
                'lahir'       => '1990-11-05',
                'masuk'       => '2019-06-01',
                'gaji'        => 7500000,
                'tj_jab'      => 800000,
                'tj_tr'       => 400000,
                'tj_mk'       => 250000,
                'kawin'       => 'TK',
                'pajak'       => 'TK0',
            ],
            [
                'user_email'  => 'siti@hr.test',
                'jabatan_kode'=> 'FIN-STF',
                'nik'         => 'KRY-0004',
                'nama'        => 'Siti Rahayu',
                'jk'          => 'P',
                'lahir'       => '1994-02-14',
                'masuk'       => '2021-01-04',
                'gaji'        => 5000000,
                'tj_jab'      => 400000,
                'tj_tr'       => 300000,
                'tj_mk'       => 200000,
                'kawin'       => 'TK',
                'pajak'       => 'TK0',
            ],
            // IT
            [
                'user_email'  => 'andi@hr.test',
                'jabatan_kode'=> 'TI-DEV',
                'nik'         => 'KRY-0005',
                'nama'        => 'Andi Wijaya',
                'jk'          => 'L',
                'lahir'       => '1993-09-25',
                'masuk'       => '2020-07-01',
                'gaji'        => 10000000,
                'tj_jab'      => 1000000,
                'tj_tr'       => 500000,
                'tj_mk'       => 300000,
                'kawin'       => 'K',    // PERBAIKAN
                'pajak'       => 'K0',
            ],
            [
                'user_email'  => 'dewi@hr.test',
                'jabatan_kode'=> 'TI-DEV',
                'nik'         => 'KRY-0006',
                'nama'        => 'Dewi Kusuma',
                'jk'          => 'P',
                'lahir'       => '1996-04-10',
                'masuk'       => '2022-02-14',
                'gaji'        => 8500000,
                'tj_jab'      => 800000,
                'tj_tr'       => 500000,
                'tj_mk'       => 300000,
                'kawin'       => 'TK',
                'pajak'       => 'TK0',
            ],
            // Marketing
            [
                'user_email'  => 'rudi@hr.test',
                'jabatan_kode'=> 'MKT-SPV',
                'nik'         => 'KRY-0007',
                'nama'        => 'Rudi Hartono',
                'jk'          => 'L',
                'lahir'       => '1988-12-03',
                'masuk'       => '2017-05-01',
                'gaji'        => 9000000,
                'tj_jab'      => 1200000,
                'tj_tr'       => 400000,
                'tj_mk'       => 250000,
                'kawin'       => 'K2',
                'pajak'       => 'K2',
            ],
            [
                'user_email'  => 'maya@hr.test',
                'jabatan_kode'=> 'MKT-STF',
                'nik'         => 'KRY-0008',
                'nama'        => 'Maya Sari',
                'jk'          => 'P',
                'lahir'       => '1997-06-22',
                'masuk'       => '2022-08-01',
                'gaji'        => 4500000,
                'tj_jab'      => 300000,
                'tj_tr'       => 300000,
                'tj_mk'       => 200000,
                'kawin'       => 'TK',
                'pajak'       => 'TK0',
            ],
            // Operations
            [
                'user_email'  => 'fajar@hr.test',
                'jabatan_kode'=> 'OPS-SPV',
                'nik'         => 'KRY-0009',
                'nama'        => 'Fajar Nugroho',
                'jk'          => 'L',
                'lahir'       => '1991-08-17',
                'masuk'       => '2019-03-01',
                'gaji'        => 8000000,
                'tj_jab'      => 1000000,
                'tj_tr'       => 400000,
                'tj_mk'       => 250000,
                'kawin'       => 'K1',
                'pajak'       => 'K1',
            ],
            [
                'user_email'  => 'lina@hr.test',
                'jabatan_kode'=> 'OPS-STF',
                'nik'         => 'KRY-0010',
                'nama'        => 'Lina Marlina',
                'jk'          => 'P',
                'lahir'       => '1999-01-30',
                'masuk'       => '2023-04-01',
                'gaji'        => 4000000,
                'tj_jab'      => 200000,
                'tj_tr'       => 300000,
                'tj_mk'       => 200000,
                'kawin'       => 'TK',
                'pajak'       => 'TK0',
            ],
        ];

        foreach ($karyawanData as $data) {
            $jabatan = Jabatan::where('kode', $data['jabatan_kode'])->first();
            $user    = $data['user_email']
                ? User::where('email', $data['user_email'])->first()
                : null;

            $karyawan = Karyawan::create([
                'user_id'                    => $user?->id,
                'departemen_id'              => $jabatan->departemen_id,
                'jabatan_id'                 => $jabatan->id,
                'nik'                        => $data['nik'],
                'nama'                       => $data['nama'],
                'jenis_kelamin'              => $data['jk'],
                'tanggal_lahir'              => $data['lahir'],
                'tempat_lahir'               => 'Jakarta',
                'status_kawin'               => $data['kawin'],
                'agama'                      => 'Islam',
                'alamat'                     => 'Jl. Contoh No. ' . rand(1, 100) . ', Jakarta',
                'telepon'                    => '08' . rand(100000000, 999999999),
                'email'                      => $data['user_email']
                    ?? strtolower(str_replace(' ', '.', $data['nama'])) . '@company.com',
                'tanggal_masuk'              => $data['masuk'],
                'status_kerja'               => 'aktif',
                'tipe_kontrak'               => 'tetap',
                'gaji_pokok'                 => $data['gaji'],
                'tunjangan_jabatan'          => $data['tj_jab'],
                'tunjangan_transport'        => $data['tj_tr'],
                'tunjangan_makan'            => $data['tj_mk'],
                'tunjangan_lainnya'          => 0,
                'ikut_bpjs_kesehatan'        => true,
                'ikut_bpjs_ketenagakerjaan'  => true,
                'no_bpjs_kesehatan'          => '0001-' . rand(1000000, 9999999),
                'no_bpjs_ketenagakerjaan'    => '12' . rand(1000000000, 9999999999),
                'bank'                       => 'BCA',
                'no_rekening'                => '1234' . rand(100000, 999999),
                'nama_rekening'              => $data['nama'],
                'status_pajak'               => $data['pajak'],
            ]);

            // Buat jatah cuti tahun ini
            JatahCuti::create([
                'karyawan_id' => $karyawan->id,
                'tahun'       => now()->year,
                'jatah'       => 12,
                'terpakai'    => 0,
            ]);
        }
    }
}
