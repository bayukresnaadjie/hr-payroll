@extends('layouts.app')
@section('title', $penggajian->nama)
@section('subtitle', 'Periode: ' . $penggajian->kode)
@section('header-action')
<div class="flex gap-2">
    @if($penggajian->status === 'draft')
    <form action="{{ route('penggajian.proses', $penggajian) }}" method="POST">
        @csrf
        <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">⚙️ Proses Gaji</button>
    </form>
    @elseif($penggajian->status === 'proses')
    <form action="{{ route('penggajian.proses', $penggajian) }}" method="POST">
        @csrf
        <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">🔄 Hitung Ulang</button>
    </form>
    <form action="{{ route('penggajian.finalisasi', $penggajian) }}" method="POST">
        @csrf
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition" onclick="return confirm('Finalisasi penggajian ini?')">✓ Finalisasi</button>
    </form>
    @elseif($penggajian->status === 'final')
    <form action="{{ route('penggajian.bayar', $penggajian) }}" method="POST">
        @csrf
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition" onclick="return confirm('Tandai semua gaji sudah dibayar?')">💰 Tandai Dibayar</button>
    </form>
    @endif
</div>
@endsection
@section('content')

{{-- Summary --}}
<div class="grid grid-cols-4 gap-4 mb-5">
    <div class="bg-white border border-gray-200 rounded-xl p-4">
        <div class="text-xs text-gray-400 mb-1">Karyawan Diproses</div>
        <div class="text-2xl font-bold text-gray-900">{{ $penggajian->slipGaji->count() }}</div>
    </div>
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
        <div class="text-xs text-emerald-600 mb-1">Total Gaji Bersih</div>
        <div class="text-xl font-bold text-emerald-700">Rp {{ number_format($totalGajiBersih, 0, ',', '.') }}</div>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="text-xs text-blue-600 mb-1">Total Penghasilan</div>
        <div class="text-xl font-bold text-blue-700">Rp {{ number_format($totalPenghasilan, 0, ',', '.') }}</div>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <div class="text-xs text-red-600 mb-1">Total Potongan</div>
        <div class="text-xl font-bold text-red-700">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</div>
    </div>
</div>

{{-- Workflow Status --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
    <div class="flex items-center gap-2">
        @foreach(['draft'=>'Draft','proses'=>'Diproses','final'=>'Final','dibayar'=>'Dibayar'] as $s => $l)
        @php $done = ['draft'=>0,'proses'=>1,'final'=>2,'dibayar'=>3][$penggajian->status] >= ['draft'=>0,'proses'=>1,'final'=>2,'dibayar'=>3][$s]; @endphp
        <div class="flex items-center gap-2 flex-1">
            <div class="flex flex-col items-center flex-1">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $done ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                    {{ $done ? '✓' : '' }}
                </div>
                <div class="text-xs mt-1 {{ $done ? 'text-indigo-600 font-semibold' : 'text-gray-400' }}">{{ $l }}</div>
            </div>
            @if(!$loop->last)<div class="flex-1 h-0.5 mb-4 {{ $done ? 'bg-indigo-400' : 'bg-gray-200' }}"></div>@endif
        </div>
        @endforeach
    </div>
</div>

{{-- Tabel Slip --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200"><tr>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Karyawan</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Hadir</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Penghasilan</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Potongan</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">PPh 21</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Gaji Bersih</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Slip</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($penggajian->slipGaji as $slip)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-900">{{ $slip->karyawan->nama }}</div>
                    <div class="text-xs text-gray-400">{{ $slip->karyawan->jabatan->nama }}</div>
                </td>
                <td class="px-5 py-3 text-center text-xs">
                    <span class="font-bold text-green-600">{{ $slip->hari_hadir }}</span>
                    <span class="text-gray-300">/{{ $slip->hari_kerja }}</span>
                    @if($slip->hari_alpha > 0)<span class="text-red-500 ml-1">{{ $slip->hari_alpha }}α</span>@endif
                </td>
                <td class="px-5 py-3 text-right text-gray-700">Rp {{ number_format($slip->total_penghasilan, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right text-red-600">Rp {{ number_format($slip->total_potongan, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right text-xs text-gray-500">Rp {{ number_format($slip->potongan_pph21, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right font-bold text-emerald-600">{{ $slip->gaji_bersih_format }}</td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('slip.show', $slip) }}" class="text-xs text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg">Slip</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">Belum ada slip — klik "Proses Gaji" untuk menghitung</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
