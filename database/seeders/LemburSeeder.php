<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Lembur;

class LemburSeeder extends Seeder
{
    public function run(): void
    {
        $karyawanList = Karyawan::all();
        $approver     = \App\Models\User::where('role','hr_manager')->first();

        foreach ($karyawanList->take(7) as $i => $karyawan) {
            $durasi      = rand(1, 4);
            $jamMulai    = '17:00';
            $jamSelesai  = \Carbon\Carbon::parse($jamMulai)->addHours($durasi)->format('H:i');
            $tarifPerJam = round($karyawan->gaji_pokok / 173);
            // Lembur jam ke-1: 1.5x, jam ke-2 dst: 2x
            $upah = $durasi >= 1 ? $tarifPerJam * 1.5 : 0;
            if ($durasi > 1) $upah += ($durasi - 1) * $tarifPerJam * 2;

            $status = $i % 3 === 0 ? 'menunggu' : ($i % 3 === 1 ? 'disetujui' : 'ditolak');

            Lembur::create([
                'karyawan_id'      => $karyawan->id,
                'tanggal'          => now()->subDays(rand(1,15))->format('Y-m-d'),
                'jam_mulai'        => $jamMulai,
                'jam_selesai'      => $jamSelesai,
                'durasi_jam'       => $durasi,
                'tarif_per_jam'    => $tarifPerJam,
                'total_upah'       => $upah,
                'keterangan'       => 'Lembur penyelesaian deadline proyek',
                'status'           => $status,
                'approved_by'      => $status !== 'menunggu' ? $approver?->id : null,
                'approved_at'      => $status !== 'menunggu' ? now() : null,
            ]);
        }
    }
}
