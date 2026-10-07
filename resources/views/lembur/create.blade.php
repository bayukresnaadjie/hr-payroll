@extends('layouts.app')
@section('title','Ajukan Lembur')
@section('content')
<div class="max-w-lg">
<div class="bg-white rounded-xl border border-gray-200 p-6">
<form action="{{ route('lembur.store') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Karyawan <span class="text-red-500">*</span></label>
        <select name="karyawan_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">-- Pilih Karyawan --</option>
            @foreach($karyawan as $k)
            <option value="{{ $k->id }}" {{ old('karyawan_id') == $k->id ? 'selected':'' }}>
                {{ $k->nama }} — Rp {{ number_format($k->tarif_lembur_per_jam, 0, ',', '.') }}/jam
            </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Lembur <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal" value="{{ old('tanggal', today()->format('Y-m-d')) }}" required
               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
            <input type="time" name="jam_mulai" value="{{ old('jam_mulai','17:00') }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
            <input type="time" name="jam_selesai" value="{{ old('jam_selesai','20:00') }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
        </div>
    </div>
    <div class="bg-indigo-50 rounded-lg p-3 text-xs text-indigo-700">
        💡 Upah lembur: jam ke-1 = 1,5× tarif/jam, jam ke-2 dst = 2× tarif/jam (sesuai UU Ketenagakerjaan)
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
        <textarea name="keterangan" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('keterangan') }}</textarea>
    </div>
    <div class="flex gap-3 pt-2 border-t border-gray-100">
        <a href="{{ route('lembur.index') }}" class="flex-1 text-center bg-gray-100 text-gray-700 text-sm font-medium py-2.5 rounded-lg hover:bg-gray-200 transition">Batal</a>
        <button type="submit" class="flex-1 bg-indigo-600 text-white text-sm font-medium py-2.5 rounded-lg hover:bg-indigo-700 transition">Kirim Pengajuan</button>
    </div>
</form>
</div>
</div>
@endsection
