<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal    = $request->get('tanggal', today()->format('Y-m-d'));
        $departemen = $request->get('departemen_id');

        $karyawanAktif = Karyawan::where('status_kerja','aktif')
            ->when($departemen, fn($q) => $q->where('departemen_id', $departemen))
            ->with(['departemen','jabatan'])->orderBy('nama')->get();

        $absensiHari = Absensi::whereDate('tanggal', $tanggal)
            ->pluck('status','karyawan_id');

        $rekap = [
            'hadir' => $absensiHari->filter(fn($s) => $s === 'hadir')->count(),
            'alpha' => $absensiHari->filter(fn($s) => $s === 'alpha')->count(),
            'izin'  => $absensiHari->filter(fn($s) => $s === 'izin')->count(),
            'sakit' => $absensiHari->filter(fn($s) => $s === 'sakit')->count(),
            'cuti'  => $absensiHari->filter(fn($s) => $s === 'cuti')->count(),
            'belum' => $karyawanAktif->count() - $absensiHari->count(),
        ];

        $deptList = \App\Models\Departemen::where('aktif',true)->orderBy('nama')->get();

        return view('absensi.index', compact('karyawanAktif','absensiHari','rekap','tanggal','deptList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal'     => 'required|date',
            'status'      => 'required|in:hadir,izin,sakit,alpha,cuti,libur',
            'jam_masuk'   => 'nullable|date_format:H:i',
            'jam_keluar'  => 'nullable|date_format:H:i',
            'keterangan'  => 'nullable|string',
        ]);

        // Hitung terlambat
        $terlambat = 0;
        $jamKerja  = 0;
        if (!empty($validated['jam_masuk'])) {
            $jamNormal = Carbon::parse('08:00');
            $jamMasuk  = Carbon::parse($validated['jam_masuk']);
            $terlambat = max(0, $jamNormal->diffInMinutes($jamMasuk, false));
        }
        if (!empty($validated['jam_masuk']) && !empty($validated['jam_keluar'])) {
            $jamKerja = Carbon::parse($validated['jam_masuk'])->diffInMinutes(Carbon::parse($validated['jam_keluar'])) / 60;
        }

        Absensi::updateOrCreate(
            ['karyawan_id' => $validated['karyawan_id'], 'tanggal' => $validated['tanggal']],
            array_merge($validated, [
                'terlambat_menit'    => $terlambat,
                'jam_kerja'          => round($jamKerja, 2),
                'jam_masuk_normal'   => '08:00:00',
                'jam_keluar_normal'  => '17:00:00',
                'approved_by'        => auth()->id(),
            ])
        );

        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    public function rekap(Request $request)
    {
        $bulan     = $request->get('bulan', now()->month);
        $tahun     = $request->get('tahun', now()->year);
        $deptId    = $request->get('departemen_id');

        $karyawan  = Karyawan::where('status_kerja','aktif')
            ->when($deptId, fn($q) => $q->where('departemen_id', $deptId))
            ->with(['departemen','jabatan'])
            ->orderBy('nama')->get();

        $rekapData = $karyawan->map(function ($k) use ($bulan, $tahun) {
            $absensi = Absensi::where('karyawan_id', $k->id)
                ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                ->selectRaw('status, count(*) as total')->groupBy('status')
                ->pluck('total','status');
            return [
                'karyawan'  => $k,
                'hadir'     => $absensi->get('hadir', 0),
                'izin'      => $absensi->get('izin', 0),
                'sakit'     => $absensi->get('sakit', 0),
                'alpha'     => $absensi->get('alpha', 0),
                'cuti'      => $absensi->get('cuti', 0),
                'terlambat' => Absensi::where('karyawan_id', $k->id)
                                ->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)
                                ->sum('terlambat_menit'),
            ];
        });

        $deptList = \App\Models\Departemen::where('aktif',true)->orderBy('nama')->get();
        return view('absensi.rekap', compact('rekapData','bulan','tahun','deptList','deptId'));
    }
}
