@extends('layouts.app')
@section('title','Buat Periode Gaji')
@section('content')
<div class="max-w-md">
<div class="bg-white rounded-xl border border-gray-200 p-6">
<form action="{{ route('penggajian.store') }}" method="POST" class="space-y-4">
    @csrf
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Bulan <span class="text-red-500">*</span></label>
            <select name="bulan" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach(range(1,12) as $b)
                <option value="{{ $b }}" {{ old('bulan', now()->month) == $b ? 'selected':'' }}>{{ \Carbon\Carbon::create(null,$b)->translatedFormat('F') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Tahun <span class="text-red-500">*</span></label>
            <select name="tahun" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach(range(now()->year, now()->year-2) as $y)
                <option value="{{ $y }}" {{ old('tahun', now()->year) == $y ? 'selected':'' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Bayar <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar', now()->endOfMonth()->format('Y-m-d')) }}" required
               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Catatan</label>
        <textarea name="catatan" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('catatan') }}</textarea>
    </div>
    <div class="flex gap-3 pt-2 border-t border-gray-100">
        <a href="{{ route('penggajian.index') }}" class="flex-1 text-center bg-gray-100 text-gray-700 text-sm font-medium py-2.5 rounded-lg">Batal</a>
        <button type="submit" class="flex-1 bg-indigo-600 text-white text-sm font-medium py-2.5 rounded-lg hover:bg-indigo-700">Buat Periode</button>
    </div>
</form>
</div>
</div>
@endsection
