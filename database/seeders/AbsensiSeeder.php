<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Absensi;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $karyawanList = Karyawan::all();
        $bulanIni     = now()->startOfMonth();
        $hariIni      = now()->day;

        foreach ($karyawanList as $karyawan) {
            for ($hari = 1; $hari <= min($hariIni - 1, 25); $hari++) {
                $tanggal = $bulanIni->copy()->addDays($hari - 1);

                // Skip Sabtu & Minggu
                if ($tanggal->isWeekend()) continue;

                // Random status: 90% hadir, 5% izin, 3% sakit, 2% alpha
                $rand = rand(1, 100);
                if ($rand <= 90) {
                    $status = 'hadir';
                    // Random terlambat: 70% tepat waktu, 30% terlambat 5-60 menit
                    $terlambat = rand(1,10) <= 3 ? rand(5, 60) : 0;
                    $jamMasuk  = Carbon::parse('08:00')->addMinutes($terlambat)->format('H:i:s');
                    $jamKeluar = rand(1,10) <= 2 ? Carbon::parse('16:30')->format('H:i:s') : '17:00:00';
                    $jamKerja  = Carbon::parse($jamMasuk)->diffInMinutes(Carbon::parse($jamKeluar)) / 60;
                } elseif ($rand <= 95) {
                    $status = 'izin'; $jamMasuk = null; $jamKeluar = null; $terlambat = 0; $jamKerja = 0;
                } elseif ($rand <= 98) {
                    $status = 'sakit'; $jamMasuk = null; $jamKeluar = null; $terlambat = 0; $jamKerja = 0;
                } else {
                    $status = 'alpha'; $jamMasuk = null; $jamKeluar = null; $terlambat = 0; $jamKerja = 0;
                }

                Absensi::create([
                    'karyawan_id'       => $karyawan->id,
                    'tanggal'           => $tanggal->format('Y-m-d'),
                    'jam_masuk'         => $jamMasuk,
                    'jam_keluar'        => $jamKeluar,
                    'jam_masuk_normal'  => '08:00:00',
                    'jam_keluar_normal' => '17:00:00',
                    'terlambat_menit'   => $terlambat,
                    'pulang_cepat_menit'=> isset($jamKeluar) && $jamKeluar < '17:00:00' ? 30 : 0,
                    'jam_kerja'         => round($jamKerja, 2),
                    'status'            => $status,
                ]);
            }
        }
    }
}
