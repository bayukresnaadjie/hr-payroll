<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\PeriodeGaji;
use App\Models\SlipGaji;
use App\Models\Absensi;
use App\Models\Lembur;

class PenggajianSeeder extends Seeder
{
    public function run(): void
    {
        $finance = \App\Models\User::where('role','finance')->first();

        // Buat periode bulan lalu (sudah dibayar)
        $bulanLalu = now()->subMonth();
        $periode = PeriodeGaji::create([
            'bulan'        => $bulanLalu->month,
            'tahun'        => $bulanLalu->year,
            'tanggal_bayar'=> $bulanLalu->copy()->endOfMonth()->format('Y-m-d'),
            'status'       => 'dibayar',
            'dibuat_oleh'  => $finance?->id,
            'difinalisasi_oleh' => $finance?->id,
            'finalisasi_at'=> now()->subDays(5),
        ]);

        $karyawanList = Karyawan::with('jabatan')->where('status_kerja','aktif')->get();

        foreach ($karyawanList as $karyawan) {
            $hariKerja  = 22; // asumsi 22 hari kerja
            $hariHadir  = rand(18, 22);
            $hariAlpha  = rand(0, 1);
            $hariSakit  = rand(0, 2);
            $hariIzin   = $hariKerja - $hariHadir - $hariAlpha - $hariSakit;
            $hariIzin   = max(0, $hariIzin);
            $terlambat  = rand(0, 120);

            // Penghasilan
            $gajiPokok         = (float) $karyawan->gaji_pokok;
            $tjJabatan         = (float) $karyawan->tunjangan_jabatan;
            $tjTransport       = (float) $karyawan->tunjangan_transport;
            $tjMakan           = (float) $karyawan->tunjangan_makan;
            $tjLainnya         = (float) $karyawan->tunjangan_lainnya;
            $upahLembur        = Lembur::where('karyawan_id', $karyawan->id)
                                    ->where('status','disetujui')
                                    ->sum('total_upah');
            $totalPenghasilan  = $gajiPokok + $tjJabatan + $tjTransport + $tjMakan + $tjLainnya + $upahLembur;

            // Potongan BPJS karyawan
            $potonganBpjsKes   = $karyawan->ikut_bpjs_kesehatan        ? round($gajiPokok * 0.01) : 0;
            $potonganBpjsJht   = $karyawan->ikut_bpjs_ketenagakerjaan  ? round($gajiPokok * 0.02) : 0;
            $potonganBpjsJp    = $karyawan->ikut_bpjs_ketenagakerjaan  ? round($gajiPokok * 0.01) : 0;

            // Potongan alpha: 1 hari = gaji_pokok / 22
            $potonganAlpha     = $hariAlpha > 0 ? round(($gajiPokok / $hariKerja) * $hariAlpha) : 0;
            // Potongan terlambat: Rp 5.000 per 15 menit
            $potonganTerlambat = round(($terlambat / 15) * 5000);

            // PPh 21
            $potonganPph21 = SlipGaji::hitungPph21($totalPenghasilan, $karyawan->status_pajak);

            $totalPotongan = $potonganBpjsKes + $potonganBpjsJht + $potonganBpjsJp
                           + $potonganAlpha + $potonganTerlambat + $potonganPph21;

            // Iuran perusahaan
            $bpjsKesPerusahaan  = $karyawan->ikut_bpjs_kesehatan       ? round($gajiPokok * 0.04)    : 0;
            $bpjsJhtPerusahaan  = $karyawan->ikut_bpjs_ketenagakerjaan ? round($gajiPokok * 0.037)   : 0;
            $bpjsJpPerusahaan   = $karyawan->ikut_bpjs_ketenagakerjaan ? round($gajiPokok * 0.02)    : 0;
            $bpjsJkkPerusahaan  = $karyawan->ikut_bpjs_ketenagakerjaan ? round($gajiPokok * 0.0024)  : 0;
            $bpjsJkmPerusahaan  = $karyawan->ikut_bpjs_ketenagakerjaan ? round($gajiPokok * 0.003)   : 0;

            $gajiBersih = $totalPenghasilan - $totalPotongan;

            SlipGaji::create([
                'periode_id'              => $periode->id,
                'karyawan_id'             => $karyawan->id,
                'hari_kerja'              => $hariKerja,
                'hari_hadir'              => $hariHadir,
                'hari_izin'               => $hariIzin,
                'hari_sakit'              => $hariSakit,
                'hari_alpha'              => $hariAlpha,
                'hari_cuti'               => 0,
                'total_terlambat_menit'   => $terlambat,
                'gaji_pokok'              => $gajiPokok,
                'tunjangan_jabatan'       => $tjJabatan,
                'tunjangan_transport'     => $tjTransport,
                'tunjangan_makan'         => $tjMakan,
                'tunjangan_lainnya'       => $tjLainnya,
                'upah_lembur'             => $upahLembur,
                'bonus'                   => 0,
                'total_penghasilan'       => $totalPenghasilan,
                'potongan_bpjs_kesehatan' => $potonganBpjsKes,
                'potongan_bpjs_tk_jht'   => $potonganBpjsJht,
                'potongan_bpjs_tk_jp'    => $potonganBpjsJp,
                'potongan_pph21'          => $potonganPph21,
                'potongan_alpha'          => $potonganAlpha,
                'potongan_terlambat'      => $potonganTerlambat,
                'potongan_lainnya'        => 0,
                'total_potongan'          => $totalPotongan,
                'bpjs_kes_perusahaan'     => $bpjsKesPerusahaan,
                'bpjs_jht_perusahaan'     => $bpjsJhtPerusahaan,
                'bpjs_jp_perusahaan'      => $bpjsJpPerusahaan,
                'bpjs_jkk_perusahaan'     => $bpjsJkkPerusahaan,
                'bpjs_jkm_perusahaan'     => $bpjsJkmPerusahaan,
                'gaji_bersih'             => $gajiBersih,
                'status'                  => 'dibayar',
                'dibayar_at'              => now()->subDays(3),
            ]);
        }
    }
}
