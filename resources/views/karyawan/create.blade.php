@extends('layouts.app')
@section('title', isset($karyawan) ? 'Edit Karyawan' : 'Tambah Karyawan')
@section('content')
<div class="max-w-3xl">
<div class="bg-white rounded-xl border border-gray-200 p-6">
<form action="{{ isset($karyawan) ? route('karyawan.update', $karyawan) : route('karyawan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf @if(isset($karyawan)) @method('PUT') @endif

    {{-- Identitas --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-1 border-b">Data Identitas</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $karyawan->nama ?? '') }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select name="jenis_kelamin" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="L" {{ old('jenis_kelamin', $karyawan->jenis_kelamin ?? '') === 'L' ? 'selected':'' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $karyawan->jenis_kelamin ?? '') === 'P' ? 'selected':'' }}>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', isset($karyawan) ? $karyawan->tanggal_lahir?->format('Y-m-d') : '') }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status Kawin <span class="text-red-500">*</span></label>
                <select name="status_kawin" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach(['TK'=>'TK (Tidak Kawin)','K'=>'K (Kawin)','K1'=>'K1 (Kawin 1 anak)','K2'=>'K2 (Kawin 2 anak)','K3'=>'K3 (Kawin 3 anak)'] as $val => $label)
                    <option value="{{ $val }}" {{ old('status_kawin', $karyawan->status_kawin ?? 'TK') === $val ? 'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $karyawan->email ?? '') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Telepon</label>
                <input type="text" name="telepon" value="{{ old('telepon', $karyawan->telepon ?? '') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Foto</label>
                <input type="file" name="foto" accept="image/*" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none"/>
            </div>
        </div>
    </div>

    {{-- Kepegawaian --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-1 border-b">Data Kepegawaian</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Departemen <span class="text-red-500">*</span></label>
                <select name="departemen_id" required id="departemen-select"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Departemen --</option>
                    @foreach($departemen as $d)
                    <option value="{{ $d->id }}" {{ old('departemen_id', $karyawan->departemen_id ?? '') == $d->id ? 'selected':'' }}>{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Jabatan <span class="text-red-500">*</span></label>
                <select name="jabatan_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($jabatan as $j)
                    <option value="{{ $j->id }}" {{ old('jabatan_id', $karyawan->jabatan_id ?? '') == $j->id ? 'selected':'' }}>{{ $j->departemen->nama }} → {{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Masuk <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', isset($karyawan) ? $karyawan->tanggal_masuk->format('Y-m-d') : '') }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Kontrak <span class="text-red-500">*</span></label>
                <select name="tipe_kontrak" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach(['tetap'=>'Karyawan Tetap','kontrak'=>'Kontrak','magang'=>'Magang','harian'=>'Harian'] as $v => $l)
                    <option value="{{ $v }}" {{ old('tipe_kontrak', $karyawan->tipe_kontrak ?? 'tetap') === $v ? 'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            @if(isset($karyawan))
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status Kerja</label>
                <select name="status_kerja" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach(['aktif','cuti_panjang','resign','phk'] as $s)
                    <option value="{{ $s }}" {{ old('status_kerja', $karyawan->status_kerja) === $s ? 'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>

    {{-- Gaji --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-1 border-b">Komponen Gaji</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Gaji Pokok (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="gaji_pokok" value="{{ old('gaji_pokok', $karyawan->gaji_pokok ?? 0) }}" required min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tunjangan Jabatan (Rp)</label>
                <input type="number" name="tunjangan_jabatan" value="{{ old('tunjangan_jabatan', $karyawan->tunjangan_jabatan ?? 0) }}" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tunjangan Transport (Rp)</label>
                <input type="number" name="tunjangan_transport" value="{{ old('tunjangan_transport', $karyawan->tunjangan_transport ?? 0) }}" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tunjangan Makan (Rp)</label>
                <input type="number" name="tunjangan_makan" value="{{ old('tunjangan_makan', $karyawan->tunjangan_makan ?? 0) }}" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status Pajak (PTKP) <span class="text-red-500">*</span></label>
                <select name="status_pajak" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach(['TK0','TK1','TK2','TK3','K0','K1','K2','K3'] as $p)
                    <option value="{{ $p }}" {{ old('status_pajak', $karyawan->status_pajak ?? 'TK0') === $p ? 'selected':'' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="flex gap-3 pt-2 border-t border-gray-100">
        <a href="{{ route('karyawan.index') }}" class="flex-1 text-center bg-gray-100 text-gray-700 text-sm font-medium py-2.5 rounded-lg hover:bg-gray-200 transition">Batal</a>
        <button type="submit" class="flex-1 bg-indigo-600 text-white text-sm font-medium py-2.5 rounded-lg hover:bg-indigo-700 transition">
            {{ isset($karyawan) ? 'Simpan Perubahan' : 'Tambah Karyawan' }}
        </button>
    </div>
</form>
</div>
</div>
@endsection
