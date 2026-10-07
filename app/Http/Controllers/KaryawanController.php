<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\JatahCuti;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with(['departemen','jabatan']);

        if ($request->filled('departemen_id')) $query->where('departemen_id', $request->departemen_id);
        if ($request->filled('status_kerja'))  $query->where('status_kerja', $request->status_kerja);
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($q2) => $q2->where('nama','like',"%$q%")->orWhere('nik','like',"%$q%")->orWhere('email','like',"%$q%"));
        }

        $karyawan   = $query->orderBy('nama')->paginate(15)->withQueryString();
        $departemen = Departemen::where('aktif',true)->orderBy('nama')->get();

        return view('karyawan.index', compact('karyawan','departemen'));
    }

    public function create()
    {
        $departemen = Departemen::where('aktif',true)->orderBy('nama')->get();
        $jabatan    = Jabatan::where('aktif',true)->with('departemen')->orderBy('nama')->get();
        return view('karyawan.create', compact('departemen','jabatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:255',
            'departemen_id'    => 'required|exists:departemen,id',
            'jabatan_id'       => 'required|exists:jabatan,id',
            'jenis_kelamin'    => 'required|in:L,P',
            'tanggal_lahir'    => 'required|date',
            'tanggal_masuk'    => 'required|date',
            'status_kawin'     => 'required',
            'tipe_kontrak'     => 'required',
            'gaji_pokok'       => 'required|numeric|min:0',
            'tunjangan_jabatan'  => 'nullable|numeric|min:0',
            'tunjangan_transport'=> 'nullable|numeric|min:0',
            'tunjangan_makan'    => 'nullable|numeric|min:0',
            'status_pajak'     => 'required',
            'foto'             => 'nullable|image|max:2048',
            'email'            => 'nullable|email',
            'telepon'          => 'nullable|string|max:20',
            'alamat'           => 'nullable|string',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('karyawan', 'public');
        }

        // Auto NIK
        $lastNik = Karyawan::max('id') + 1;
        $validated['nik'] = 'KRY-' . str_pad($lastNik, 4, '0', STR_PAD_LEFT);

        // Buat user account jika email diisi
        if (!empty($validated['email'])) {
            $user = User::create([
                'name'     => $validated['nama'],
                'email'    => $validated['email'],
                'password' => Hash::make('password'),
                'role'     => 'karyawan',
            ]);
            $validated['user_id'] = $user->id;
        }

        $karyawan = Karyawan::create($validated);

        // Buat jatah cuti tahun ini
        JatahCuti::create([
            'karyawan_id' => $karyawan->id,
            'tahun'       => now()->year,
            'jatah'       => 12,
            'terpakai'    => 0,
        ]);

        return redirect()->route('karyawan.show', $karyawan)->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Karyawan $karyawan)
    {
        $karyawan->load(['departemen','jabatan','absensi' => fn($q) => $q->whereMonth('tanggal', now()->month)->orderBy('tanggal')]);
        $jatahCuti   = $karyawan->jatahCutiTahunIni();
        $slipTerakhir= $karyawan->slipGaji()->with('periode')->latest()->first();
        $lemburAktif = $karyawan->lembur()->where('status','menunggu')->count();
        $cutiAktif   = $karyawan->cuti()->where('status','menunggu')->count();

        // Rekap absensi bulan ini
        $rekapAbsensi = $karyawan->absensi()
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total','status');

        return view('karyawan.show', compact('karyawan','jatahCuti','slipTerakhir','lemburAktif','cutiAktif','rekapAbsensi'));
    }

    public function edit(Karyawan $karyawan)
    {
        $departemen = Departemen::where('aktif',true)->orderBy('nama')->get();
        $jabatan    = Jabatan::where('aktif',true)->with('departemen')->orderBy('nama')->get();
        return view('karyawan.edit', compact('karyawan','departemen','jabatan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'nama'               => 'required|string|max:255',
            'departemen_id'      => 'required|exists:departemen,id',
            'jabatan_id'         => 'required|exists:jabatan,id',
            'jenis_kelamin'      => 'required|in:L,P',
            'tanggal_lahir'      => 'required|date',
            'tanggal_masuk'      => 'required|date',
            'status_kawin'       => 'required',
            'tipe_kontrak'       => 'required',
            'status_kerja'       => 'required',
            'gaji_pokok'         => 'required|numeric|min:0',
            'tunjangan_jabatan'  => 'nullable|numeric|min:0',
            'tunjangan_transport'=> 'nullable|numeric|min:0',
            'tunjangan_makan'    => 'nullable|numeric|min:0',
            'status_pajak'       => 'required',
            'foto'               => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($karyawan->foto) Storage::disk('public')->delete($karyawan->foto);
            $validated['foto'] = $request->file('foto')->store('karyawan','public');
        }

        $karyawan->update($validated);
        return redirect()->route('karyawan.show', $karyawan)->with('success', 'Data karyawan berhasil diperbarui.');
    }
}
