@extends('layouts.app')
@section('title','Absensi Harian')
@section('subtitle','Input dan pantau kehadiran karyawan')
@section('content')

<form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Tanggal</label>
        <input type="date" name="tanggal" value="{{ $tanggal }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
    </div>
    <select name="departemen_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Semua Departemen</option>
        @foreach($deptList as $d)
        <option value="{{ $d->id }}" {{ request('departemen_id') == $d->id ? 'selected':'' }}>{{ $d->nama }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg">Filter</button>
</form>

{{-- Rekap Hari Ini --}}
<div class="grid grid-cols-6 gap-3 mb-4">
    @foreach(['hadir'=>['Hadir','green'],'alpha'=>['Alpha','red'],'izin'=>['Izin','blue'],'sakit'=>['Sakit','yellow'],'cuti'=>['Cuti','purple'],'belum'=>['Belum','gray']] as $key => [$label, $color])
    <div class="bg-{{ $color }}-50 border border-{{ $color }}-200 rounded-xl p-3 text-center">
        <div class="text-2xl font-bold text-{{ $color }}-600">{{ $rekap[$key] }}</div>
        <div class="text-xs text-{{ $color }}-500">{{ $label }}</div>
    </div>
    @endforeach
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200"><tr>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Karyawan</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Jam Masuk</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Jam Keluar</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Terlambat</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Status</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Update</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($karyawanAktif as $k)
            @php $statusSaat = $absensiHari->get($k->id, null); @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <img src="{{ $k->foto_url }}" class="w-8 h-8 rounded-lg object-cover"/>
                        <div>
                            <div class="font-medium text-gray-900 text-sm">{{ $k->nama }}</div>
                            <div class="text-xs text-gray-400">{{ $k->departemen->nama }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3 font-mono text-sm text-gray-600">
                    {{ optional(\App\Models\Absensi::where('karyawan_id',$k->id)->whereDate('tanggal',$tanggal)->first())->jam_masuk ?? '-' }}
                </td>
                <td class="px-5 py-3 font-mono text-sm text-gray-600">
                    {{ optional(\App\Models\Absensi::where('karyawan_id',$k->id)->whereDate('tanggal',$tanggal)->first())->jam_keluar ?? '-' }}
                </td>
                <td class="px-5 py-3 text-center text-xs">
                    @php $tl = optional(\App\Models\Absensi::where('karyawan_id',$k->id)->whereDate('tanggal',$tanggal)->first())->terlambat_menit ?? 0; @endphp
                    @if($tl > 0)
                    <span class="text-red-600 font-semibold">{{ $tl }} mnt</span>
                    @else
                    <span class="text-gray-300">-</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-center">
                    @if($statusSaat)
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full
                        {{ $statusSaat==='hadir'?'bg-green-100 text-green-700':($statusSaat==='alpha'?'bg-red-100 text-red-700':($statusSaat==='sakit'?'bg-yellow-100 text-yellow-700':'bg-blue-100 text-blue-700')) }}">
                        {{ ucfirst($statusSaat) }}
                    </span>
                    @else
                    <span class="text-xs text-gray-300">Belum</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-right">
                    <form action="{{ route('absensi.store') }}" method="POST" class="flex justify-end items-center gap-1">
                        @csrf
                        <input type="hidden" name="karyawan_id" value="{{ $k->id }}"/>
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}"/>
                        <input type="time" name="jam_masuk" value="{{ optional(\App\Models\Absensi::where('karyawan_id',$k->id)->whereDate('tanggal',$tanggal)->first())->jam_masuk }}"
                               class="border border-gray-200 rounded px-1 py-1 text-xs focus:outline-none w-20"/>
                        <select name="status" class="border border-gray-200 rounded px-1 py-1 text-xs focus:outline-none">
                            @foreach(['hadir','izin','sakit','alpha','cuti','libur'] as $s)
                            <option value="{{ $s }}" {{ $statusSaat === $s ? 'selected':'' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-indigo-600 text-white text-xs px-2 py-1.5 rounded-lg hover:bg-indigo-700 transition">✓</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
