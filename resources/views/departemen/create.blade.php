@extends('layouts.app')
@section('title', isset($departemen) ? 'Edit Departemen' : 'Tambah Departemen')
@section('content')
<div class="max-w-md">
<div class="bg-white rounded-xl border border-gray-200 p-6">
<form action="{{ isset($departemen) ? route('departemen.update', $departemen) : route('departemen.store') }}" method="POST" class="space-y-4">
    @csrf @if(isset($departemen)) @method('PUT') @endif
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Kode <span class="text-red-500">*</span></label>
        <input type="text" name="kode" value="{{ old('kode', $departemen->kode ?? '') }}" required placeholder="HRD"
               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Departemen <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $departemen->nama ?? '') }}" required
               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Kepala Departemen</label>
        <input type="text" name="kepala" value="{{ old('kepala', $departemen->kepala ?? '') }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
    </div>
    <div class="flex gap-3 pt-2 border-t border-gray-100">
        <a href="{{ route('departemen.index') }}" class="flex-1 text-center bg-gray-100 text-gray-700 text-sm font-medium py-2.5 rounded-lg">Batal</a>
        <button type="submit" class="flex-1 bg-indigo-600 text-white text-sm font-medium py-2.5 rounded-lg hover:bg-indigo-700">{{ isset($departemen) ? 'Simpan' : 'Tambah' }}</button>
    </div>
</form>
</div>
</div>
@endsection
