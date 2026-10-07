@extends('layouts.app')
@section('title','Ajukan Cuti')
@section('content')
<div class="max-w-lg">
<div class="bg-white rounded-xl border border-gray-200 p-6">
<form action="{{ route('cuti.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Karyawan <span class="text-red-500">*</span></label>
        <select name="karyawan_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">-- Pilih Karyawan --</option>
            @foreach($karyawan as $k)
            <option value="{{ $k->id }}" {{ old('karyawan_id') == $k->id ? 'selected':'' }}>{{ $k->nama }} — {{ $k->departemen->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Cuti <span class="text-red-500">*</span></label>
        <select name="jenis" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach(['tahunan'=>'Cuti Tahunan','sakit'=>'Cuti Sakit','melahirkan'=>'Cuti Melahirkan','penting'=>'Keperluan Penting','lainnya'=>'Lainnya'] as $v => $l)
            <option value="{{ $v }}" {{ old('jenis') === $v ? 'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required
                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Alasan <span class="text-red-500">*</span></label>
        <textarea name="alasan" rows="3" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('alasan') }}</textarea>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Lampiran (opsional)</label>
        <input type="file" name="lampiran" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"/>
    </div>
    <div class="flex gap-3 pt-2 border-t border-gray-100">
        <a href="{{ route('cuti.index') }}" class="flex-1 text-center bg-gray-100 text-gray-700 text-sm font-medium py-2.5 rounded-lg hover:bg-gray-200 transition">Batal</a>
        <button type="submit" class="flex-1 bg-indigo-600 text-white text-sm font-medium py-2.5 rounded-lg hover:bg-indigo-700 transition">Kirim Pengajuan</button>
    </div>
</form>
</div>
</div>
@endsection
