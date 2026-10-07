@extends('layouts.app')
@section('title','Laporan Data Karyawan')
@section('content')

<form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Departemen</label>
        <select name="departemen_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Departemen</option>
            @foreach($deptList as $d)
            <option value="{{ $d->id }}" {{ $deptId == $d->id ? 'selected':'' }}>{{ $d->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status_kerja" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach(['aktif','resign','phk','cuti_panjang'] as $s)
            <option value="{{ $s }}" {{ $status === $s ? 'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-700 transition">Filter</button>
</form>

{{-- Summary Cards --}}
<div class="grid grid-cols-5 gap-3 mb-4">
    @foreach($perStatus as $s => $jml)
    <div class="bg-white border border-gray-200 rounded-xl p-3 text-center">
        <div class="text-xl font-bold text-indigo-600">{{ $jml }}</div>
        <div class="text-xs text-gray-400 capitalize">{{ str_replace('_',' ',$s) }}</div>
    </div>
    @endforeach
</div>

{{-- Per Departemen --}}
@if($perDept->count() > 0)
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
    <h2 class="text-sm font-semibold text-gray-900 mb-3">Distribusi per Departemen</h2>
    @php $maxDept = max($perDept->max(), 1); @endphp
    <div class="grid grid-cols-5 gap-3">
        @foreach($perDept as $nama => $jml)
        <div class="text-center">
            <div class="h-16 flex items-end justify-center mb-1">
                <div class="w-8 bg-indigo-400 rounded-t" style="height:{{ max(4, ($jml/$maxDept)*60) }}px"></div>
            </div>
            <div class="text-sm font-bold text-indigo-600">{{ $jml }}</div>
            <div class="text-xs text-gray-400 truncate">{{ $nama }}</div>
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200"><tr>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Karyawan</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Departemen / Jabatan</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Tipe Kontrak</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Tanggal Masuk</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Masa Kerja</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Gaji Pokok</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Status</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($karyawan as $k)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <img src="{{ $k->foto_url }}" class="w-8 h-8 rounded-lg object-cover flex-shrink-0"/>
                        <div>
                            <div class="font-medium text-gray-900">{{ $k->nama }}</div>
                            <div class="text-xs font-mono text-gray-400">{{ $k->nik }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3">
                    <div class="text-gray-700 text-xs">{{ $k->departemen->nama }}</div>
                    <div class="text-xs text-gray-400">{{ $k->jabatan->nama }}</div>
                </td>
                <td class="px-5 py-3 capitalize text-xs text-gray-500">{{ $k->tipe_kontrak }}</td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $k->tanggal_masuk->format('d M Y') }}</td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $k->masa_kerja }}</td>
                <td class="px-5 py-3 text-right font-medium text-gray-900">{{ $k->gaji_pokok_format }}</td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $k->status_kerja_badge['class'] }}">{{ $k->status_kerja_badge['label'] }}</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">Tidak ada data karyawan</td></tr>
            @endforelse
        </tbody>
        @if($karyawan->count() > 0)
        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
            <tr>
                <td colspan="5" class="px-5 py-3 text-xs font-semibold text-gray-500">TOTAL {{ $karyawan->count() }} KARYAWAN</td>
                <td class="px-5 py-3 text-right font-bold text-gray-900">Rp {{ number_format($karyawan->sum('gaji_pokok'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
