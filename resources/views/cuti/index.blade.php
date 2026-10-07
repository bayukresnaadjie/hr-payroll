@extends('layouts.app')
@section('title','Manajemen Cuti')
@section('header-action')
<a href="{{ route('cuti.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">+ Ajukan Cuti</a>
@endsection
@section('content')

@if($menunggu > 0)
<div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-4 text-sm text-amber-800">
    ⏳ Ada <strong>{{ $menunggu }}</strong> pengajuan cuti menunggu persetujuan
</div>
@endif

<form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex gap-3 items-end">
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
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Nomor</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Karyawan</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Jenis</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Tanggal</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Hari</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Status</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Aksi</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($cuti as $c)
            <tr class="hover:bg-gray-50 {{ $c->status === 'menunggu' ? 'bg-amber-50/40':'' }}">
                <td class="px-5 py-3 font-mono text-xs text-indigo-600">{{ $c->nomor_cuti }}</td>
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-900">{{ $c->karyawan->nama }}</div>
                    <div class="text-xs text-gray-400">{{ $c->karyawan->departemen->nama }}</div>
                </td>
                <td class="px-5 py-3 text-gray-600 text-xs">{{ $c->jenis_label }}</td>
                <td class="px-5 py-3 text-xs text-gray-500">
                    {{ $c->tanggal_mulai->format('d M') }} – {{ $c->tanggal_selesai->format('d M Y') }}
                </td>
                <td class="px-5 py-3 text-center font-bold text-gray-900">{{ $c->jumlah_hari }}</td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $c->status_badge['class'] }}">{{ $c->status_badge['label'] }}</span>
                </td>
                <td class="px-5 py-3 text-right">
                    @if($c->status === 'menunggu')
                    <div class="flex justify-end gap-1">
                        <form action="{{ route('cuti.approval', $c) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="disetujui"/>
                            <button class="text-xs bg-green-50 text-green-600 hover:bg-green-100 px-2.5 py-1.5 rounded-lg">✓ Setuju</button>
                        </form>
                        <form action="{{ route('cuti.approval', $c) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="ditolak"/>
                            <button class="text-xs bg-red-50 text-red-600 hover:bg-red-100 px-2.5 py-1.5 rounded-lg">✕ Tolak</button>
                        </form>
                    </div>
                    @else
                    <span class="text-xs text-gray-400">{{ $c->approvedBy?->name ?? '-' }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">Tidak ada data cuti</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($cuti->hasPages())
    <div class="px-5 py-3 border-t">{{ $cuti->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
