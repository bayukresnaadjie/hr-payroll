<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Lembur;
use App\Models\SlipGaji;
use App\Models\PeriodeGaji;
use App\Models\Departemen;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik utama
        $totalKaryawan     = Karyawan::where('status_kerja','aktif')->count();
        $hadirHariIni      = Absensi::whereDate('tanggal', today())->where('status','hadir')->count();
        $alphaHariIni      = Absensi::whereDate('tanggal', today())->where('status','alpha')->count();
        $cutiMenunggu      = Cuti::where('status','menunggu')->count();
        $lemburMenunggu    = Lembur::where('status','menunggu')->count();

        // Total gaji bulan lalu
        $periodeTerakhir   = PeriodeGaji::where('status','dibayar')->latest()->first();
        $totalGajiBulanIni = $periodeTerakhir
            ? SlipGaji::where('periode_id', $periodeTerakhir->id)->sum('gaji_bersih')
            : 0;

        // Karyawan baru bulan ini
        $karyawanBaru = Karyawan::whereMonth('tanggal_masuk', now()->month)
            ->whereYear('tanggal_masuk', now()->year)->count();

        // Absensi per status hari ini
        $absensiHariIni = Absensi::whereDate('tanggal', today())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total','status');

        // Grafik kehadiran 7 hari
        $grafikKehadiran = collect(range(6, 0))->map(function ($day) {
            $tgl = today()->subDays($day);
            return [
                'tanggal' => $tgl->format('d M'),
                'hadir'   => Absensi::whereDate('tanggal', $tgl)->where('status','hadir')->count(),
                'alpha'   => Absensi::whereDate('tanggal', $tgl)->where('status','alpha')->count(),
                'izin'    => Absensi::whereDate('tanggal', $tgl)->whereIn('status',['izin','sakit'])->count(),
            ];
        });

        // Distribusi per departemen
        $perDepartemen = Departemen::withCount(['karyawan' => fn($q) => $q->where('status_kerja','aktif')])
            ->get()->map(fn($d) => ['nama' => $d->nama, 'total' => $d->karyawan_count]);

        // Approval menunggu
        $cutiPending   = Cuti::with('karyawan.departemen')->where('status','menunggu')->latest()->take(5)->get();
        $lemburPending = Lembur::with('karyawan.departemen')->where('status','menunggu')->latest()->take(5)->get();

        // Kontrak akan habis 30 hari ke depan
        $kontrakHabis = Karyawan::where('tipe_kontrak','kontrak')
            ->whereNotNull('kontrak_sampai')
            ->whereBetween('kontrak_sampai', [today(), today()->addDays(30)])
            ->with('jabatan','departemen')->get();

        return view('dashboard', compact(
            'totalKaryawan','hadirHariIni','alphaHariIni',
            'cutiMenunggu','lemburMenunggu','totalGajiBulanIni',
            'karyawanBaru','absensiHariIni','grafikKehadiran',
            'perDepartemen','cutiPending','lemburPending','kontrakHabis',
            'periodeTerakhir'
        ));
    }
}
