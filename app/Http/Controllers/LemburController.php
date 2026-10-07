<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LemburController extends Controller
{
    public function index(Request $request)
    {
        $query = Lembur::with(['karyawan.departemen','approvedBy']);
        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('karyawan_id'))$query->where('karyawan_id', $request->karyawan_id);

        $lembur   = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();
        $menunggu = Lembur::where('status','menunggu')->count();
        return view('lembur.index', compact('lembur','menunggu'));
    }

    public function create()
    {
        $karyawan = Karyawan::where('status_kerja','aktif')->orderBy('nama')->get();
        return view('lembur.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan'  => 'nullable|string',
        ]);

        $karyawan   = Karyawan::findOrFail($validated['karyawan_id']);
        $durasi     = Carbon::parse($validated['jam_mulai'])->diffInMinutes(Carbon::parse($validated['jam_selesai'])) / 60;
        $tarifPerJam= $karyawan->tarif_lembur_per_jam;

        // Hitung upah lembur sesuai UU: jam 1 = 1.5x, jam 2+ = 2x
        $upah = 0;
        if ($durasi >= 1) $upah += $tarifPerJam * 1.5;
        if ($durasi > 1)  $upah += ($durasi - 1) * $tarifPerJam * 2;

        Lembur::create(array_merge($validated, [
            'durasi_jam'    => round($durasi, 2),
            'tarif_per_jam' => $tarifPerJam,
            'total_upah'    => round($upah),
        ]));

        return redirect()->route('lembur.index')->with('success', 'Pengajuan lembur berhasil dikirim.');
    }

    public function approval(Request $request, Lembur $lembur)
    {
        $request->validate([
            'status'           => 'required|in:disetujui,ditolak',
            'catatan_approval' => 'nullable|string',
        ]);

        $lembur->update([
            'status'           => $request->status,
            'catatan_approval' => $request->catatan_approval,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
        ]);

        $label = $request->status === 'disetujui' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Lembur berhasil {$label}.");
    }
}
