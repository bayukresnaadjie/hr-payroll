@extends('layouts.app')
@section('title','Data Departemen')
@section('header-action')
<a href="{{ route('departemen.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">+ Tambah Departemen</a>
@endsection
@section('content')
<div class="grid grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($departemen as $d)
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-start justify-between mb-3">
            <div>
                <div class="font-bold text-gray-900">{{ $d->nama }}</div>
                <div class="text-xs font-mono text-indigo-500">{{ $d->kode }}</div>
            </div>
            <span class="text-2xl font-bold text-indigo-600">{{ $d->karyawan_count }}</span>
        </div>
        <div class="text-xs text-gray-500 mb-3">Kepala: {{ $d->kepala ?? '-' }}</div>
        <div class="space-y-1 mb-3">
            @foreach($d->jabatan->take(3) as $j)
            <div class="text-xs text-gray-400 flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-indigo-300 rounded-full"></span>{{ $j->nama }}
            </div>
            @endforeach
            @if($d->jabatan->count() > 3)
            <div class="text-xs text-gray-300">+{{ $d->jabatan->count()-3 }} jabatan lainnya</div>
            @endif
        </div>
        <a href="{{ route('departemen.edit', $d) }}" class="block text-center text-xs bg-gray-100 hover:bg-gray-200 text-gray-600 py-1.5 rounded-lg transition">Edit</a>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-xl border border-gray-200 p-10 text-center text-sm text-gray-400">Belum ada departemen</div>
    @endforelse
</div>
@endsection
