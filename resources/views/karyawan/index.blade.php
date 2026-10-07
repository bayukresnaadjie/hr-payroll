@extends('layouts.app')
@section('title','Data Karyawan')
@section('header-action')
<a href="{{ route('karyawan.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">+ Tambah Karyawan</a>
@endsection
@section('content')

<form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex gap-3 items-end">
    <div class="flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, email..."
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
    </div>
    <select name="departemen_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Semua Departemen</option>
        @foreach($departemen as $d)
        <option value="{{ $d->id }}" {{ request('departemen_id') == $d->id ? 'selected':'' }}>{{ $d->nama }}</option>
        @endforeach
    </select>
    <select name="status_kerja" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Semua Status</option>
        @foreach(['aktif','resign','phk','cuti_panjang'] as $s)
        <option value="{{ $s }}" {{ request('status_kerja')===$s ? 'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-700 transition">Filter</button>
</form>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200"><tr>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Karyawan</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Departemen / Jabatan</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Masa Kerja</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Gaji Pokok</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Status</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Aksi</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($karyawan as $k)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $k->foto_url }}" class="w-9 h-9 rounded-xl object-cover" alt="{{ $k->nama }}"/>
                        <div>
                            <div class="font-medium text-gray-900">{{ $k->nama }}</div>
                            <div class="text-xs text-gray-400 font-mono">{{ $k->nik }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3">
                    <div class="text-gray-700">{{ $k->departemen->nama }}</div>
                    <div class="text-xs text-gray-400">{{ $k->jabatan->nama }}</div>
                </td>
                <td class="px-5 py-3 text-gray-500 text-xs">{{ $k->masa_kerja }}</td>
                <td class="px-5 py-3 text-right font-medium text-gray-900">{{ $k->gaji_pokok_format }}</td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $k->status_kerja_badge['class'] }}">{{ $k->status_kerja_badge['label'] }}</span>
                </td>
                <td class="px-5 py-3 text-right">
                    <div class="flex justify-end gap-1">
                        <a href="{{ route('karyawan.show', $k) }}" class="text-xs text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg">Detail</a>
                        <a href="{{ route('karyawan.edit', $k) }}" class="text-xs text-gray-600 bg-gray-100 hover:bg-gray-200 px-2.5 py-1 rounded-lg">Edit</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">Tidak ada karyawan ditemukan</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($karyawan->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $karyawan->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
