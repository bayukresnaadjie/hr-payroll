<?php

namespace App\Http\Controllers;

use App\Models\PeriodeGaji;
use App\Models\SlipGaji;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PenggajianController extends Controller
{
    public function index()
    {
        $periode = PeriodeGaji::withCount('slipGaji')
            ->orderByDesc('tahun')->orderByDesc('bulan')->paginate(12);
        return view('penggajian.index', compact('periode'));
    }

    public function create()
    {
        return view('penggajian.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan'         => 'required|integer|between:1,12',
            'tahun'         => 'required|integer|min:2020',
            'tanggal_bayar' => 'required|date',
            'catatan'       => 'nullable|string',
        ]);

        // Cek duplikat periode
        if (PeriodeGaji::where('bulan', $validated['bulan'])->where('tahun', $validated['tahun'])->exists()) {
            return back()->withErrors(['bulan' => 'Periode gaji bulan ini sudah ada.'])->withInput();
        }

        $periode = PeriodeGaji::create(array_merge($validated, [
            'status'      => 'draft',
            'dibuat_oleh' => auth()->id(),
        ]));

        return redirect()->route('penggajian.show', $periode)->with('success', 'Periode gaji berhasil dibuat. Silakan proses penggajian.');
    }

    public function show(PeriodeGaji $penggajian)
    {
        $penggajian->load(['slipGaji.karyawan.departemen','slipGaji.karyawan.jabatan']);
        $totalGajiBersih   = $penggajian->slipGaji->sum('gaji_bersih');
        $totalPenghasilan  = $penggajian->slipGaji->sum('total_penghasilan');
        $totalPotongan     = $penggajian->slipGaji->sum('total_potongan');
        return view('penggajian.show', compact('penggajian','totalGajiBersih','totalPenghasilan','totalPotongan'));
    }

    public function proses(PeriodeGaji $penggajian)
    {
        if (!in_array($penggajian->status, ['draft','proses'])) {
            return back()->withErrors(['status' => 'Periode sudah final atau dibayar.']);
        }

        DB::transaction(function () use ($penggajian) {
            $karyawanList = Karyawan::where('status_kerja','aktif')->get();
            $hariKerja    = $this->hitungHariKerja($penggajian->bulan, $penggajian->tahun);

            foreach ($karyawanList as $karyawan) {
                // Rekap absensi
                $absensi = Absensi::where('karyawan_id', $karyawan->id)
                    ->whereMonth('tanggal', $penggajian->bulan)
                    ->whereYear('tanggal', $penggajian->tahun)
                    ->selectRaw('status, count(*) as total, sum(terlambat_menit) as total_terlambat')
                    ->groupBy('status')->get()->keyBy('status');

                $hariHadir  = (int)($absensi->get('hadir')?->total ?? 0);
                $hariIzin   = (int)($absensi->get('izin')?->total ?? 0);
                $hariSakit  = (int)($absensi->get('sakit')?->total ?? 0);
                $hariAlpha  = (int)($absensi->get('alpha')?->total ?? 0);
                $hariCuti   = (int)($absensi->get('cuti')?->total ?? 0);
                $terlambat  = (float)($absensi->sum('total_terlambat') ?? 0);

                // Penghasilan
                $gajiPokok       = (float) $karyawan->gaji_pokok;
                $tjJabatan       = (float) $karyawan->tunjangan_jabatan;
                $tjTransport     = (float) $karyawan->tunjangan_transport;
                $tjMakan         = (float) $karyawan->tunjangan_makan;
                $tjLainnya       = (float) $karyawan->tunjangan_lainnya;
                $upahLembur      = (float) Lembur::where('karyawan_id', $karyawan->id)
                    ->whereMonth('tanggal', $penggajian->bulan)
                    ->whereYear('tanggal', $penggajian->tahun)
                    ->where('status','disetujui')->sum('total_upah');

                $totalPenghasilan = $gajiPokok + $tjJabatan + $tjTransport + $tjMakan + $tjLainnya + $upahLembur;

                // Potongan
                $potonganBpjsKes  = $karyawan->ikut_bpjs_kesehatan       ? round($gajiPokok * 0.01) : 0;
                $potonganBpjsJht  = $karyawan->ikut_bpjs_ketenagakerjaan ? round($gajiPokok * 0.02) : 0;
                $potonganBpjsJp   = $karyawan->ikut_bpjs_ketenagakerjaan ? round($gajiPokok * 0.01) : 0;
                $potonganAlpha    = $hariAlpha > 0 ? round(($gajiPokok / $hariKerja) * $hariAlpha) : 0;
                $potonganTelambat = round(($terlambat / 15) * 5000);
                $potonganPph21    = SlipGaji::hitungPph21($totalPenghasilan, $karyawan->status_pajak);
                $totalPotongan    = $potonganBpjsKes + $potonganBpjsJht + $potonganBpjsJp + $potonganAlpha + $potonganTelambat + $potonganPph21;

                // Iuran perusahaan
                $bpjsKesPerus  = $karyawan->ikut_bpjs_kesehatan       ? round($gajiPokok * 0.04)   : 0;
                $bpjsJhtPerus  = $karyawan->ikut_bpjs_ketenagakerjaan ? round($gajiPokok * 0.037)  : 0;
                $bpjsJpPerus   = $karyawan->ikut_bpjs_ketenagakerjaan ? round($gajiPokok * 0.02)   : 0;
                $bpjsJkkPerus  = $karyawan->ikut_bpjs_ketenagakerjaan ? round($gajiPokok * 0.0024) : 0;
                $bpjsJkmPerus  = $karyawan->ikut_bpjs_ketenagakerjaan ? round($gajiPokok * 0.003)  : 0;

                SlipGaji::updateOrCreate(
                    ['periode_id' => $penggajian->id, 'karyawan_id' => $karyawan->id],
                    [
                        'hari_kerja'              => $hariKerja,
                        'hari_hadir'              => $hariHadir,
                        'hari_izin'               => $hariIzin,
                        'hari_sakit'              => $hariSakit,
                        'hari_alpha'              => $hariAlpha,
                        'hari_cuti'               => $hariCuti,
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
                        'potongan_terlambat'      => $potonganTelambat,
                        'potongan_lainnya'        => 0,
                        'total_potongan'          => $totalPotongan,
                        'bpjs_kes_perusahaan'     => $bpjsKesPerus,
                        'bpjs_jht_perusahaan'     => $bpjsJhtPerus,
                        'bpjs_jp_perusahaan'      => $bpjsJpPerus,
                        'bpjs_jkk_perusahaan'     => $bpjsJkkPerus,
                        'bpjs_jkm_perusahaan'     => $bpjsJkmPerus,
                        'gaji_bersih'             => $totalPenghasilan - $totalPotongan,
                        'status'                  => 'draft',
                    ]
                );
            }

            $penggajian->update(['status' => 'proses']);
        });

        return redirect()->route('penggajian.show', $penggajian)->with('success', 'Penggajian berhasil diproses untuk ' . $penggajian->slipGaji()->count() . ' karyawan.');
    }

    public function finalisasi(PeriodeGaji $penggajian)
    {
        if ($penggajian->status !== 'proses') {
            return back()->withErrors(['status' => 'Status harus "proses" untuk difinalisasi.']);
        }

        $penggajian->slipGaji()->update(['status' => 'final']);
        $penggajian->update([
            'status'             => 'final',
            'difinalisasi_oleh'  => auth()->id(),
            'finalisasi_at'      => now(),
        ]);

        return redirect()->route('penggajian.show', $penggajian)->with('success', 'Penggajian berhasil difinalisasi.');
    }

    public function bayar(PeriodeGaji $penggajian)
    {
        if ($penggajian->status !== 'final') {
            return back()->withErrors(['status' => 'Harus difinalisasi dulu sebelum dibayar.']);
        }

        $penggajian->slipGaji()->update(['status' => 'dibayar', 'dibayar_at' => now()]);
        $penggajian->update(['status' => 'dibayar']);

        return redirect()->route('penggajian.show', $penggajian)->with('success', 'Gaji berhasil ditandai sudah dibayar.');
    }

    public function slip(SlipGaji $slip)
    {
        $slip->load(['karyawan.departemen','karyawan.jabatan','periode']);
        return view('penggajian.slip', compact('slip'));
    }

    public function slipPdf(SlipGaji $slip)
    {
        $slip->load(['karyawan.departemen','karyawan.jabatan','periode']);
        $pdf = Pdf::loadView('penggajian.slip-pdf', compact('slip'))->setPaper('a4');
        return $pdf->stream("slip-{$slip->nomor_slip}.pdf");
    }

    private function hitungHariKerja(int $bulan, int $tahun): int
    {
        $start = \Carbon\Carbon::create($tahun, $bulan, 1);
        $end   = $start->copy()->endOfMonth();
        $count = 0;
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if ($d->isWeekday()) $count++;
        }
        return $count;
    }
}
