@extends('layouts.app')
@section('title','Rekap Absensi Bulanan')
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
    <select name="departemen_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Semua Departemen</option>
        @foreach($deptList as $d)
        <option value="{{ $d->id }}" {{ $deptId == $d->id ? 'selected':'' }}>{{ $d->nama }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg">Tampilkan</button>
</form>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200"><tr>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Karyawan</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-green-500">Hadir</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-blue-500">Izin</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-yellow-500">Sakit</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-red-500">Alpha</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-purple-500">Cuti</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Terlambat</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rekapData as $row)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-900">{{ $row['karyawan']->nama }}</div>
                    <div class="text-xs text-gray-400">{{ $row['karyawan']->departemen->nama }}</div>
                </td>
                <td class="px-5 py-3 text-center font-bold text-green-600">{{ $row['hadir'] }}</td>
                <td class="px-5 py-3 text-center text-blue-600">{{ $row['izin'] }}</td>
                <td class="px-5 py-3 text-center text-yellow-600">{{ $row['sakit'] }}</td>
                <td class="px-5 py-3 text-center {{ $row['alpha'] > 0 ? 'font-bold text-red-600' : 'text-gray-300' }}">{{ $row['alpha'] ?: '-' }}</td>
                <td class="px-5 py-3 text-center text-purple-600">{{ $row['cuti'] ?: '-' }}</td>
                <td class="px-5 py-3 text-center text-xs {{ $row['terlambat'] > 60 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                    {{ $row['terlambat'] > 0 ? $row['terlambat'] . ' mnt' : '-' }}
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">Tidak ada data absensi</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
