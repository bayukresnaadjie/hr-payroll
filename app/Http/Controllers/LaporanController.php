<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\SlipGaji;
use App\Models\PeriodeGaji;
use App\Models\Absensi;
use App\Models\Departemen;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function penggajian(Request $request)
    {
        $periodeId = $request->get('periode_id');
        $deptId    = $request->get('departemen_id');

        $periodeList = PeriodeGaji::orderByDesc('tahun')->orderByDesc('bulan')->get();
        $periode     = $periodeId ? PeriodeGaji::find($periodeId) : $periodeList->first();

        $slip = SlipGaji::with(['karyawan.departemen','karyawan.jabatan'])
            ->when($periode, fn($q) => $q->where('periode_id', $periode->id))
            ->when($deptId,  fn($q) => $q->whereHas('karyawan', fn($q2) => $q2->where('departemen_id', $deptId)))
            ->get();

        $totalGaji      = $slip->sum('gaji_bersih');
        $totalPotongan  = $slip->sum('total_potongan');
        $totalPph21     = $slip->sum('potongan_pph21');
        $totalBpjs      = $slip->sum('potongan_bpjs_kesehatan') + $slip->sum('potongan_bpjs_tk_jht') + $slip->sum('potongan_bpjs_tk_jp');
        $perDept        = $slip->groupBy('karyawan.departemen.nama')->map->sum('gaji_bersih');
        $deptList       = Departemen::where('aktif',true)->orderBy('nama')->get();

        return view('laporan.penggajian', compact('slip','periode','periodeList','totalGaji','totalPotongan','totalPph21','totalBpjs','perDept','deptList','deptId'));
    }

    public function absensi(Request $request)
    {
        $bulan  = $request->get('bulan', now()->month);
        $tahun  = $request->get('tahun', now()->year);
        $deptId = $request->get('departemen_id');

        $karyawan = Karyawan::where('status_kerja','aktif')
            ->when($deptId, fn($q) => $q->where('departemen_id', $deptId))
            ->with('departemen','jabatan')->orderBy('nama')->get();

        $data = $karyawan->map(function ($k) use ($bulan, $tahun) {
            $abs = Absensi::where('karyawan_id', $k->id)
                ->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)
                ->selectRaw('status, count(*) as total, sum(terlambat_menit) as terlambat')
                ->groupBy('status')->get()->keyBy('status');
            return [
                'karyawan'  => $k,
                'hadir'     => (int)($abs->get('hadir')?->total ?? 0),
                'izin'      => (int)($abs->get('izin')?->total ?? 0),
                'sakit'     => (int)($abs->get('sakit')?->total ?? 0),
                'alpha'     => (int)($abs->get('alpha')?->total ?? 0),
                'terlambat' => (int)($abs->sum('terlambat') ?? 0),
            ];
        });

        $deptList = Departemen::where('aktif',true)->orderBy('nama')->get();
        return view('laporan.absensi', compact('data','bulan','tahun','deptList','deptId'));
    }

    public function karyawan(Request $request)
    {
        $deptId = $request->get('departemen_id');
        $status = $request->get('status_kerja', 'aktif');

        $karyawan = Karyawan::with(['departemen','jabatan'])
            ->when($deptId, fn($q) => $q->where('departemen_id', $deptId))
            ->when($status, fn($q) => $q->where('status_kerja', $status))
            ->orderBy('departemen_id')->orderBy('nama')->get();

        $perDept   = $karyawan->groupBy('departemen.nama')->map->count();
        $perStatus = Karyawan::selectRaw('status_kerja, count(*) as total')->groupBy('status_kerja')->pluck('total','status_kerja');
        $deptList  = Departemen::where('aktif',true)->orderBy('nama')->get();

        return view('laporan.karyawan', compact('karyawan','perDept','perStatus','deptList','deptId','status'));
    }
}
