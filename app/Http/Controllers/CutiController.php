<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Karyawan;
use App\Models\JatahCuti;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    public function index(Request $request)
    {
        $query = Cuti::with(['karyawan.departemen','approvedBy']);
        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('karyawan_id'))$query->where('karyawan_id', $request->karyawan_id);

        $cuti      = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $menunggu  = Cuti::where('status','menunggu')->count();
        return view('cuti.index', compact('cuti','menunggu'));
    }

    public function create()
    {
        $karyawan = Karyawan::where('status_kerja','aktif')->orderBy('nama')->get();
        return view('cuti.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id'     => 'required|exists:karyawan,id',
            'jenis'           => 'required|in:tahunan,sakit,melahirkan,penting,lainnya',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string',
            'lampiran'        => 'nullable|file|max:2048',
        ]);

        $jumlahHari = \Carbon\Carbon::parse($validated['tanggal_mulai'])
            ->diffInWeekdays(\Carbon\Carbon::parse($validated['tanggal_selesai'])) + 1;

        // Cek sisa cuti tahunan
        if ($validated['jenis'] === 'tahunan') {
            $jatah = JatahCuti::where('karyawan_id', $validated['karyawan_id'])
                ->where('tahun', now()->year)->first();
            if (!$jatah || $jatah->sisa < $jumlahHari) {
                return back()->withErrors(['tanggal_selesai' => 'Sisa cuti tidak mencukupi. Sisa: ' . ($jatah->sisa ?? 0) . ' hari.'])->withInput();
            }
        }

        if ($request->hasFile('lampiran')) {
            $validated['lampiran'] = $request->file('lampiran')->store('cuti','public');
        }

        Cuti::create(array_merge($validated, ['jumlah_hari' => $jumlahHari]));
        return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function approval(Request $request, Cuti $cuti)
    {
        $request->validate([
            'status'           => 'required|in:disetujui,ditolak',
            'catatan_approval' => 'nullable|string',
        ]);

        $cuti->update([
            'status'           => $request->status,
            'catatan_approval' => $request->catatan_approval,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
        ]);

        // Update jatah cuti jika disetujui
        if ($request->status === 'disetujui' && $cuti->jenis === 'tahunan') {
            JatahCuti::where('karyawan_id', $cuti->karyawan_id)
                ->where('tahun', now()->year)
                ->increment('terpakai', $cuti->jumlah_hari);
        }

        $label = $request->status === 'disetujui' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Cuti berhasil {$label}.");
    }
}
