@extends('layouts.app')
@section('title','Manajemen Lembur')
@section('header-action')
<a href="{{ route('lembur.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">+ Ajukan Lembur</a>
@endsection
@section('content')

@if($menunggu > 0)
<div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-4 text-sm text-amber-800">
    ⏳ Ada <strong>{{ $menunggu }}</strong> pengajuan lembur menunggu persetujuan
</div>
@endif

<form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex gap-3">
    <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Semua Status</option>
        @foreach(['menunggu','disetujui','ditolak'] as $s)
        <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg">Filter</button>
</form>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200"><tr>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Karyawan</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Tanggal</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Jam</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Durasi</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Upah Lembur</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Status</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Aksi</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($lembur as $l)
            <tr class="hover:bg-gray-50 {{ $l->status==='menunggu'?'bg-amber-50/40':'' }}">
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-900">{{ $l->karyawan->nama }}</div>
                    <div class="text-xs text-gray-400">{{ $l->karyawan->departemen->nama }}</div>
                </td>
                <td class="px-5 py-3 text-gray-500 text-xs">{{ $l->tanggal->format('d M Y') }}</td>
                <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $l->jam_mulai }} – {{ $l->jam_selesai }}</td>
                <td class="px-5 py-3 text-center font-bold text-gray-900">{{ $l->durasi_jam }} jam</td>
                <td class="px-5 py-3 text-right font-semibold text-emerald-600">{{ $l->total_upah_format }}</td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $l->status_badge['class'] }}">{{ $l->status_badge['label'] }}</span>
                </td>
                <td class="px-5 py-3 text-right">
                    @if($l->status === 'menunggu')
                    <div class="flex justify-end gap-1">
                        <form action="{{ route('lembur.approval', $l) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="disetujui"/>
                            <button class="text-xs bg-green-50 text-green-600 hover:bg-green-100 px-2.5 py-1.5 rounded-lg">✓ Setuju</button>
                        </form>
                        <form action="{{ route('lembur.approval', $l) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="ditolak"/>
                            <button class="text-xs bg-red-50 text-red-600 hover:bg-red-100 px-2.5 py-1.5 rounded-lg">✕ Tolak</button>
                        </form>
                    </div>
                    @else
                    <span class="text-xs text-gray-400">{{ $l->approvedBy?->name ?? '-' }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">Tidak ada data lembur</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($lembur->hasPages())
    <div class="px-5 py-3 border-t">{{ $lembur->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
