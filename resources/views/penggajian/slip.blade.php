@extends('layouts.app')
@section('title','Slip Gaji')
@section('subtitle', $slip->nomor_slip)
@section('header-action')
<a href="{{ route('slip.pdf', $slip) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">📄 Download PDF</a>
@endsection
@section('content')
<div class="max-w-2xl">
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    {{-- Header --}}
    <div class="bg-indigo-900 px-6 py-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-lg font-bold">SLIP GAJI KARYAWAN</div>
                <div class="text-indigo-300 text-sm">{{ $slip->periode->nama }}</div>
            </div>
            <div class="text-right">
                <div class="font-mono text-sm text-indigo-300">{{ $slip->nomor_slip }}</div>
                <div class="text-xs text-indigo-400">{{ now()->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    {{-- Identitas Karyawan --}}
    <div class="px-6 py-4 border-b border-gray-100 grid grid-cols-2 gap-3 text-sm">
        <div><span class="text-xs text-gray-400 block">Nama</span><span class="font-semibold">{{ $slip->karyawan->nama }}</span></div>
        <div><span class="text-xs text-gray-400 block">NIK</span><span class="font-mono">{{ $slip->karyawan->nik }}</span></div>
        <div><span class="text-xs text-gray-400 block">Jabatan</span><span>{{ $slip->karyawan->jabatan->nama }}</span></div>
        <div><span class="text-xs text-gray-400 block">Departemen</span><span>{{ $slip->karyawan->departemen->nama }}</span></div>
        <div><span class="text-xs text-gray-400 block">Status Pajak</span><span class="font-bold">{{ $slip->karyawan->status_pajak }}</span></div>
        <div><span class="text-xs text-gray-400 block">Bank / Rekening</span><span class="font-mono text-xs">{{ $slip->karyawan->bank }} — {{ $slip->karyawan->no_rekening }}</span></div>
    </div>

    {{-- Kehadiran --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kehadiran</div>
        <div class="grid grid-cols-6 gap-2 text-center text-xs">
            @foreach(['Hari Kerja'=>$slip->hari_kerja,'Hadir'=>$slip->hari_hadir,'Izin'=>$slip->hari_izin,'Sakit'=>$slip->hari_sakit,'Alpha'=>$slip->hari_alpha,'Cuti'=>$slip->hari_cuti] as $l => $v)
            <div class="bg-gray-50 rounded-lg p-2"><div class="font-bold text-base {{ $l==='Alpha'&&$v>0?'text-red-600':($l==='Hadir'?'text-green-600':'text-gray-900') }}">{{ $v }}</div><div class="text-gray-400">{{ $l }}</div></div>
            @endforeach
        </div>
    </div>

    <div class="px-6 py-4 grid grid-cols-2 gap-6">
        {{-- Penghasilan --}}
        <div>
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Penghasilan</div>
            <div class="space-y-1.5 text-sm">
                @foreach(['Gaji Pokok'=>$slip->gaji_pokok,'Tj. Jabatan'=>$slip->tunjangan_jabatan,'Tj. Transport'=>$slip->tunjangan_transport,'Tj. Makan'=>$slip->tunjangan_makan,'Upah Lembur'=>$slip->upah_lembur,'Bonus'=>$slip->bonus] as $l => $v)
                @if($v > 0)
                <div class="flex justify-between"><span class="text-gray-500">{{ $l }}</span><span>Rp {{ number_format($v,0,',','.') }}</span></div>
                @endif
                @endforeach
                <div class="flex justify-between font-bold border-t pt-1.5"><span>Total</span><span class="text-blue-600">Rp {{ number_format($slip->total_penghasilan,0,',','.') }}</span></div>
            </div>
        </div>

        {{-- Potongan --}}
        <div>
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Potongan</div>
            <div class="space-y-1.5 text-sm">
                @foreach(['BPJS Kes (1%)'=>$slip->potongan_bpjs_kesehatan,'BPJS JHT (2%)'=>$slip->potongan_bpjs_tk_jht,'BPJS JP (1%)'=>$slip->potongan_bpjs_tk_jp,'PPh 21'=>$slip->potongan_pph21,'Potongan Alpha'=>$slip->potongan_alpha,'Potongan Terlambat'=>$slip->potongan_terlambat,'Lainnya'=>$slip->potongan_lainnya] as $l => $v)
                @if($v > 0)
                <div class="flex justify-between"><span class="text-gray-500">{{ $l }}</span><span class="text-red-600">Rp {{ number_format($v,0,',','.') }}</span></div>
                @endif
                @endforeach
                <div class="flex justify-between font-bold border-t pt-1.5"><span>Total</span><span class="text-red-600">Rp {{ number_format($slip->total_potongan,0,',','.') }}</span></div>
            </div>
        </div>
    </div>

    {{-- Take Home --}}
    <div class="px-6 py-5 bg-emerald-50 border-t border-emerald-200 flex items-center justify-between">
        <div>
            <div class="text-sm text-emerald-700 font-medium">GAJI BERSIH (TAKE HOME PAY)</div>
            <div class="text-xs text-emerald-500">Dibayarkan ke {{ $slip->karyawan->bank }} — {{ $slip->karyawan->no_rekening }}</div>
        </div>
        <div class="text-2xl font-bold text-emerald-700">{{ $slip->gaji_bersih_format }}</div>
    </div>
</div>
<div class="mt-4"><a href="{{ route('penggajian.show', $slip->periode) }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a></div>
</div>
@endsection
