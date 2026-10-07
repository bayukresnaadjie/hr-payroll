<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index()
    {
        $departemen = Departemen::withCount(['karyawan' => fn($q) => $q->where('status_kerja','aktif')])
            ->with('jabatan')->orderBy('nama')->get();
        return view('departemen.index', compact('departemen'));
    }

    public function create() { return view('departemen.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode'      => 'required|string|max:10|unique:departemen',
            'nama'      => 'required|string|max:100',
            'kepala'    => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);
        Departemen::create($validated);
        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit(Departemen $departemen)
    {
        $jabatan = Jabatan::where('departemen_id', $departemen->id)->get();
        return view('departemen.edit', compact('departemen','jabatan'));
    }

    public function update(Request $request, Departemen $departemen)
    {
        $validated = $request->validate([
            'kode'      => 'required|string|max:10|unique:departemen,kode,' . $departemen->id,
            'nama'      => 'required|string|max:100',
            'kepala'    => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'aktif'     => 'sometimes|boolean',
        ]);
        $validated['aktif'] = $request->boolean('aktif', true);
        $departemen->update($validated);
        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil diperbarui.');
    }
}
