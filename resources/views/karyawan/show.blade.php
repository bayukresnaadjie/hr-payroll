@extends('layouts.app')
@section('title','Detail Karyawan')
@section('subtitle', $karyawan->nik . ' · ' . $karyawan->jabatan->nama)
@section('header-action')
<a href="{{ route('karyawan.edit', $karyawan) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">Edit Data</a>
@endsection
@section('content')
<div class="max-w-4xl space-y-4">

{{-- Profile Card --}}
<div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="flex gap-5">
        <img src="{{ $karyawan->foto_url }}" class="w-24 h-24 rounded-2xl object-cover flex-shrink-0" alt="{{ $karyawan->nama }}"/>
        <div class="flex-1">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="font-bold text-gray-900 text-xl">{{ $karyawan->nama }}</h2>
                    <div class="text-sm text-gray-500">{{ $karyawan->jabatan->nama }} · {{ $karyawan->departemen->nama }}</div>
                    <div class="font-mono text-xs text-indigo-600 mt-1">{{ $karyawan->nik }}</div>
                </div>
                <span class="text-xs font-medium px-3 py-1 rounded-full {{ $karyawan->status_kerja_badge['class'] }}">{{ $karyawan->status_kerja_badge['label'] }}</span>
            </div>
            <div class="grid grid-cols-4 gap-3 mt-4 text-sm">
                <div class="bg-indigo-50 rounded-lg p-3 text-center">
                    <div class="text-xs text-indigo-500 mb-1">Masa Kerja</div>
                    <div class="font-bold text-indigo-700 text-xs">{{ $karyawan->masa_kerja }}</div>
                </div>
                <div class="bg-emerald-50 rounded-lg p-3 text-center">
                    <div class="text-xs text-emerald-500 mb-1">Gaji Pokok</div>
                    <div class="font-bold text-emerald-700 text-xs">{{ $karyawan->gaji_pokok_format }}</div>
                </div>
                <div class="bg-amber-50 rounded-lg p-3 text-center">
                    <div class="text-xs text-amber-500 mb-1">Sisa Cuti</div>
                    <div class="font-bold text-amber-700">{{ $jatahCuti?->sisa ?? 0 }} hari</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-3 text-center">
                    <div class="text-xs text-purple-500 mb-1">Pending</div>
                    <div class="font-bold text-purple-700">{{ $cutiAktif + $lemburAktif }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    {{-- Data Pribadi --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Data Pribadi</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-400">Jenis Kelamin</span><span>{{ $karyawan->jenis_kelamin_label }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Tanggal Lahir</span><span>{{ $karyawan->tanggal_lahir?->format('d F Y') }} ({{ $karyawan->umur }} th)</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Status Kawin</span><span>{{ $karyawan->status_kawin }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Telepon</span><span>{{ $karyawan->telepon ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Email</span><span class="text-xs">{{ $karyawan->email ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">NPWP</span><span class="font-mono text-xs">{{ $karyawan->npwp ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Status Pajak</span><span class="font-bold">{{ $karyawan->status_pajak }}</span></div>
        </div>
    </div>

    {{-- Data Kepegawaian --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Kepegawaian & Gaji</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-400">Tanggal Masuk</span><span>{{ $karyawan->tanggal_masuk->format('d F Y') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Tipe Kontrak</span><span class="capitalize">{{ $karyawan->tipe_kontrak }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Gaji Pokok</span><span class="font-semibold">{{ $karyawan->gaji_pokok_format }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Tj. Jabatan</span><span>Rp {{ number_format($karyawan->tunjangan_jabatan, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Tj. Transport</span><span>Rp {{ number_format($karyawan->tunjangan_transport, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Tj. Makan</span><span>Rp {{ number_format($karyawan->tunjangan_makan, 0, ',', '.') }}</span></div>
            <div class="flex justify-between font-semibold border-t pt-2"><span>Total Kotor</span><span class="text-emerald-600">Rp {{ number_format($karyawan->total_penghasilan_kotor, 0, ',', '.') }}</span></div>
        </div>
    </div>
</div>

{{-- Rekap Absensi Bulan Ini --}}
<div class="bg-white rounded-xl border border-gray-200 p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-3">Rekap Absensi Bulan Ini</h3>
    <div class="grid grid-cols-5 gap-3 text-center text-sm">
        @foreach(['hadir'=>['Hadir','green'], 'izin'=>['Izin','blue'], 'sakit'=>['Sakit','yellow'], 'alpha'=>['Alpha','red'], 'cuti'=>['Cuti','purple']] as $key => [$label, $color])
        <div class="bg-{{ $color }}-50 rounded-xl p-3">
            <div class="text-2xl font-bold text-{{ $color }}-600">{{ $rekapAbsensi->get($key, 0) }}</div>
            <div class="text-xs text-{{ $color }}-500">{{ $label }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- Slip Terakhir --}}
@if($slipTerakhir)
<div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-gray-900">Slip Gaji Terakhir — {{ $slipTerakhir->periode->nama }}</h3>
        <a href="{{ route('slip.show', $slipTerakhir) }}" class="text-xs text-indigo-600 hover:underline">Lihat detail →</a>
    </div>
    <div class="grid grid-cols-3 gap-3 text-sm">
        <div class="bg-gray-50 rounded-lg p-3"><div class="text-xs text-gray-400 mb-1">Total Penghasilan</div><div class="font-bold text-gray-900">{{ $slipTerakhir->total_penghasilan_format }}</div></div>
        <div class="bg-red-50 rounded-lg p-3"><div class="text-xs text-red-400 mb-1">Total Potongan</div><div class="font-bold text-red-700">Rp {{ number_format($slipTerakhir->total_potongan, 0, ',', '.') }}</div></div>
        <div class="bg-emerald-50 rounded-lg p-3"><div class="text-xs text-emerald-400 mb-1">Gaji Bersih</div><div class="font-bold text-emerald-700">{{ $slipTerakhir->gaji_bersih_format }}</div></div>
    </div>
</div>
@endif

<div class="flex gap-3">
    <a href="{{ route('karyawan.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
    <a href="{{ route('absensi.index', ['karyawan_id'=>$karyawan->id]) }}" class="text-sm text-indigo-600 hover:underline">Lihat Absensi</a>
</div>
</div>
@endsection
