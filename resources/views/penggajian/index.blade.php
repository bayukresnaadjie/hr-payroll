@extends('layouts.app')
@section('title','Periode Penggajian')
@section('header-action')
<a href="{{ route('penggajian.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">+ Buat Periode Gaji</a>
@endsection
@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200"><tr>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Periode</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Tanggal Bayar</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Karyawan</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Total Gaji Bersih</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Status</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Aksi</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($periode as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="font-semibold text-gray-900">{{ $p->nama }}</div>
                    <div class="text-xs font-mono text-gray-400">{{ $p->kode }}</div>
                </td>
                <td class="px-5 py-3 text-gray-500 text-sm">{{ $p->tanggal_bayar->format('d F Y') }}</td>
                <td class="px-5 py-3 text-center font-bold text-gray-900">{{ $p->slip_gaji_count }}</td>
                <td class="px-5 py-3 text-right font-bold text-emerald-600">Rp {{ number_format($p->total_gaji_bersih, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $p->status_badge['class'] }}">{{ $p->status_badge['label'] }}</span>
                </td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('penggajian.show', $p) }}" class="text-xs text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1.5 rounded-lg">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">Belum ada periode gaji</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($periode->hasPages())<div class="px-5 py-3 border-t">{{ $periode->links() }}</div>@endif
</div>
@endsection
