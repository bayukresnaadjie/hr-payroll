@extends('layouts.app')
@section('title','Laporan Absensi')
@section('content')

<form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Bulan</label>
        <select name="bulan" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach(range(1,12) as $b)
            <option value="{{ $b }}" {{ $bulan == $b ? 'selected':'' }}>{{ \Carbon\Carbon::create(null,$b)->translatedFormat('F') }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Tahun</label>
        <select name="tahun" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach(range(now()->year, now()->year-3) as $y)
            <option value="{{ $y }}" {{ $tahun == $y ? 'selected':'' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Departemen</label>
        <select name="departemen_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Departemen</option>
            @foreach($deptList as $d)
            <option value="{{ $d->id }}" {{ $deptId == $d->id ? 'selected':'' }}>{{ $d->nama }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-700 transition">Tampilkan</button>
</form>

{{-- Summary --}}
@php
    $totalHadir  = $data->sum('hadir');
    $totalAlpha  = $data->sum('alpha');
    $totalIzin   = $data->sum('izin');
    $totalSakit  = $data->sum('sakit');
@endphp
<div class="grid grid-cols-4 gap-4 mb-4">
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
        <div class="text-2xl font-bold text-green-600">{{ $totalHadir }}</div>
        <div class="text-xs text-green-500">Total Hari Hadir</div>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
        <div class="text-2xl font-bold text-red-600">{{ $totalAlpha }}</div>
        <div class="text-xs text-red-500">Total Alpha</div>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
        <div class="text-2xl font-bold text-blue-600">{{ $totalIzin }}</div>
        <div class="text-xs text-blue-500">Total Izin</div>
    </div>
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
        <div class="text-2xl font-bold text-yellow-600">{{ $totalSakit }}</div>
        <div class="text-xs text-yellow-500">Total Sakit</div>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200"><tr>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Karyawan</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Departemen</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-green-500">Hadir</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-blue-500">Izin</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-yellow-500">Sakit</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-red-500">Alpha</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Terlambat</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Tingkat Hadir</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($data as $row)
            @php
                $totalHari = $row['hadir'] + $row['izin'] + $row['sakit'] + $row['alpha'];
                $persen = $totalHari > 0 ? round(($row['hadir'] / $totalHari) * 100) : 0;
            @endphp
            <tr class="hover:bg-gray-50 {{ $row['alpha'] > 2 ? 'bg-red-50/30':'' }}">
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-900">{{ $row['karyawan']->nama }}</div>
                    <div class="text-xs text-gray-400 font-mono">{{ $row['karyawan']->nik }}</div>
                </td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $row['karyawan']->departemen->nama }}</td>
                <td class="px-5 py-3 text-center font-bold text-green-600">{{ $row['hadir'] }}</td>
                <td class="px-5 py-3 text-center text-blue-600">{{ $row['izin'] ?: '-' }}</td>
                <td class="px-5 py-3 text-center text-yellow-600">{{ $row['sakit'] ?: '-' }}</td>
                <td class="px-5 py-3 text-center {{ $row['alpha'] > 0 ? 'font-bold text-red-600':'text-gray-300' }}">{{ $row['alpha'] ?: '-' }}</td>
                <td class="px-5 py-3 text-center text-xs {{ $row['terlambat'] > 60 ? 'text-red-600 font-semibold':'text-gray-400' }}">
                    {{ $row['terlambat'] > 0 ? $row['terlambat'].' mnt' : '-' }}
                </td>
                <td class="px-5 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <div class="w-16 h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $persen >= 90 ? 'bg-green-400' : ($persen >= 75 ? 'bg-yellow-400' : 'bg-red-400') }}" style="width:{{ $persen }}%"></div>
                        </div>
                        <span class="text-xs {{ $persen >= 90 ? 'text-green-600' : ($persen >= 75 ? 'text-yellow-600' : 'text-red-600') }}">{{ $persen }}%</span>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-5 py-10 text-center text-sm text-gray-400">Tidak ada data absensi</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
