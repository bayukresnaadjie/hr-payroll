@extends('layouts.app')
@section('title','Dashboard')
@section('subtitle','Ringkasan SDM ' . now()->translatedFormat('l, d F Y'))
@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-gray-500">Total Karyawan</span>
            <span class="text-2xl">👥</span>
        </div>
        <div class="text-2xl font-bold text-gray-900">{{ $totalKaryawan }}</div>
        <div class="text-xs text-indigo-500 mt-1">{{ $karyawanBaru }} bergabung bulan ini</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-gray-500">Hadir Hari Ini</span>
            <span class="text-2xl">✅</span>
        </div>
        <div class="text-2xl font-bold text-green-600">{{ $hadirHariIni }}</div>
        <div class="text-xs text-red-500 mt-1">{{ $alphaHariIni }} alpha hari ini</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-gray-500">Total Gaji Dibayar</span>
            <span class="text-2xl">💰</span>
        </div>
        <div class="text-lg font-bold text-emerald-600">Rp {{ number_format($totalGajiBulanIni/1000000, 1) }}jt</div>
        <div class="text-xs text-gray-400 mt-1">{{ $periodeTerakhir?->nama ?? 'Belum ada' }}</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-gray-500">Pending Approval</span>
            <span class="text-2xl">⏳</span>
        </div>
        <div class="text-2xl font-bold {{ ($cutiMenunggu + $lemburMenunggu) > 0 ? 'text-amber-600' : 'text-gray-900' }}">
            {{ $cutiMenunggu + $lemburMenunggu }}
        </div>
        <div class="text-xs text-gray-400 mt-1">{{ $cutiMenunggu }} cuti · {{ $lemburMenunggu }} lembur</div>
    </div>
</div>

<div class="grid grid-cols-3 gap-4 mb-4">
    {{-- Grafik Kehadiran 7 Hari --}}
    <div class="col-span-2 bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">Tren Kehadiran 7 Hari Terakhir</h2>
        @php $maxVal = max($grafikKehadiran->max('hadir'), 1); @endphp
        <div class="flex items-end gap-3 h-28">
            @foreach($grafikKehadiran as $g)
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="w-full flex flex-col gap-0.5 items-center">
                    <div class="w-full bg-green-400 rounded-t"
                         style="height:{{ max(2, ($g['hadir']/$maxVal)*90) }}px"
                         title="Hadir: {{ $g['hadir'] }}"></div>
                </div>
                <span class="text-gray-400" style="font-size:9px">{{ $g['tanggal'] }}</span>
                <span class="text-xs font-bold text-green-600">{{ $g['hadir'] }}</span>
            </div>
            @endforeach
        </div>
        <div class="flex gap-4 mt-3 text-xs">
            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-400 rounded"></span> Hadir</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-300 rounded"></span> Alpha</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-yellow-300 rounded"></span> Izin/Sakit</span>
        </div>
    </div>

    {{-- Distribusi Departemen --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-sm font-semibold text-gray-900 mb-3">Karyawan per Departemen</h2>
        @php $maxDept = max($perDepartemen->max('total'), 1); @endphp
        <div class="space-y-2">
            @foreach($perDepartemen as $dept)
            <div>
                <div class="flex justify-between text-xs mb-0.5">
                    <span class="text-gray-600 truncate">{{ $dept['nama'] }}</span>
                    <span class="font-bold text-indigo-600">{{ $dept['total'] }}</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full">
                    <div class="h-full bg-indigo-400 rounded-full" style="width:{{ ($dept['total']/$maxDept)*100 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-2 gap-4 mb-4">
    {{-- Cuti Pending --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">Cuti Menunggu Approval</h2>
            <a href="{{ route('cuti.index', ['status'=>'menunggu']) }}" class="text-xs text-indigo-500 hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($cutiPending as $c)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium text-gray-900">{{ $c->karyawan->nama }}</div>
                    <div class="text-xs text-gray-400">{{ $c->jenis_label }} · {{ $c->jumlah_hari }} hari</div>
                    <div class="text-xs text-gray-400">{{ $c->tanggal_mulai->format('d M') }} – {{ $c->tanggal_selesai->format('d M Y') }}</div>
                </div>
                <div class="flex gap-1">
                    <form action="{{ route('cuti.approval', $c) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="disetujui"/>
                        <button class="text-xs bg-green-50 text-green-600 hover:bg-green-100 px-2.5 py-1.5 rounded-lg">✓</button>
                    </form>
                    <form action="{{ route('cuti.approval', $c) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="ditolak"/>
                        <button class="text-xs bg-red-50 text-red-600 hover:bg-red-100 px-2.5 py-1.5 rounded-lg">✕</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="px-5 py-6 text-center text-sm text-gray-400">Tidak ada pengajuan cuti</div>
            @endforelse
        </div>
    </div>

    {{-- Lembur Pending --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">Lembur Menunggu Approval</h2>
            <a href="{{ route('lembur.index', ['status'=>'menunggu']) }}" class="text-xs text-indigo-500 hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($lemburPending as $l)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium text-gray-900">{{ $l->karyawan->nama }}</div>
                    <div class="text-xs text-gray-400">{{ $l->tanggal->format('d M Y') }} · {{ $l->durasi_jam }} jam</div>
                    <div class="text-xs text-emerald-600 font-medium">{{ $l->total_upah_format }}</div>
                </div>
                <div class="flex gap-1">
                    <form action="{{ route('lembur.approval', $l) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="disetujui"/>
                        <button class="text-xs bg-green-50 text-green-600 hover:bg-green-100 px-2.5 py-1.5 rounded-lg">✓</button>
                    </form>
                    <form action="{{ route('lembur.approval', $l) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="ditolak"/>
                        <button class="text-xs bg-red-50 text-red-600 hover:bg-red-100 px-2.5 py-1.5 rounded-lg">✕</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="px-5 py-6 text-center text-sm text-gray-400">Tidak ada pengajuan lembur</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Kontrak Hampir Habis --}}
@if($kontrakHabis->count() > 0)
<div class="bg-amber-50 border border-amber-200 rounded-xl">
    <div class="px-5 py-3 border-b border-amber-200">
        <h2 class="text-sm font-semibold text-amber-800">⚠️ Kontrak Hampir Habis (30 hari ke depan)</h2>
    </div>
    <div class="divide-y divide-amber-100">
        @foreach($kontrakHabis as $k)
        <div class="px-5 py-3 flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-gray-900">{{ $k->nama }}</div>
                <div class="text-xs text-gray-500">{{ $k->jabatan->nama }} · {{ $k->departemen->nama }}</div>
            </div>
            <div class="text-right">
                <div class="text-sm font-bold text-amber-700">{{ $k->kontrak_sampai->format('d M Y') }}</div>
                <div class="text-xs text-amber-600">{{ $k->kontrak_sampai->diffInDays(today()) }} hari lagi</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
