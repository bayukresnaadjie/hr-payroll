<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Cuti;
use App\Models\JatahCuti;

class CutiSeeder extends Seeder
{
    public function run(): void
    {
        $karyawanList = Karyawan::all();

        $cutiData = [
            ['jenis'=>'tahunan',  'hari'=>3, 'status'=>'disetujui', 'alasan'=>'Liburan keluarga'],
            ['jenis'=>'sakit',    'hari'=>2, 'status'=>'disetujui', 'alasan'=>'Demam dan flu'],
            ['jenis'=>'penting',  'hari'=>1, 'status'=>'disetujui', 'alasan'=>'Acara pernikahan saudara'],
            ['jenis'=>'tahunan',  'hari'=>2, 'status'=>'menunggu',  'alasan'=>'Keperluan pribadi'],
            ['jenis'=>'tahunan',  'hari'=>1, 'status'=>'ditolak',   'alasan'=>'Urusan keluarga'],
        ];

        $approver = \App\Models\User::where('role','hr_manager')->first();

        foreach ($karyawanList->take(6) as $i => $karyawan) {
            $data   = $cutiData[$i % count($cutiData)];
            $mulai  = now()->subDays(rand(5, 30));
            $selesai= $mulai->copy()->addDays($data['hari'] - 1);

            $cuti = Cuti::create([
                'karyawan_id'      => $karyawan->id,
                'jenis'            => $data['jenis'],
                'tanggal_mulai'    => $mulai->format('Y-m-d'),
                'tanggal_selesai'  => $selesai->format('Y-m-d'),
                'jumlah_hari'      => $data['hari'],
                'alasan'           => $data['alasan'],
                'status'           => $data['status'],
                'approved_by'      => $data['status'] !== 'menunggu' ? $approver?->id : null,
                'approved_at'      => $data['status'] !== 'menunggu' ? now() : null,
            ]);

            // Update jatah cuti jika disetujui & jenis tahunan
            if ($data['status'] === 'disetujui' && $data['jenis'] === 'tahunan') {
                JatahCuti::where('karyawan_id', $karyawan->id)
                    ->where('tahun', now()->year)
                    ->increment('terpakai', $data['hari']);
            }
        }
    }
}
