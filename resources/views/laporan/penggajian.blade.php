@extends('layouts.app')
@section('title','Laporan Penggajian')
@section('content')

<form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Periode</label>
        <select name="periode_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">-- Pilih Periode --</option>
            @foreach($periodeList as $p)
            <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected':'' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Departemen</label>
        <select name="departemen_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Departemen</option>
            @foreach($deptList as $d)
            <option value="{{ $d->id }}" {{ $deptId == $d->id ? 'selected':'' }}>{{ $d->nama }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-700 transition">Tampilkan</button>
</form>

@if($periode)
<div class="grid grid-cols-4 gap-4 mb-4">
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
        <div class="text-xs text-emerald-600 mb-1">Total Gaji Bersih</div>
        <div class="text-xl font-bold text-emerald-700">Rp {{ number_format($totalGaji, 0, ',', '.') }}</div>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="text-xs text-blue-600 mb-1">Total Potongan</div>
        <div class="text-xl font-bold text-blue-700">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</div>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <div class="text-xs text-red-600 mb-1">Total PPh 21</div>
        <div class="text-xl font-bold text-red-700">Rp {{ number_format($totalPph21, 0, ',', '.') }}</div>
    </div>
    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
        <div class="text-xs text-purple-600 mb-1">Total BPJS Karyawan</div>
        <div class="text-xl font-bold text-purple-700">Rp {{ number_format($totalBpjs, 0, ',', '.') }}</div>
    </div>
</div>

{{-- Per Departemen --}}
@if($perDept->count() > 0)
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
    <h2 class="text-sm font-semibold text-gray-900 mb-3">Total Gaji per Departemen</h2>
    @php $maxDept = max($perDept->max(), 1); @endphp
    <div class="space-y-2">
        @foreach($perDept as $nama => $total)
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-600 w-40 truncate">{{ $nama }}</span>
            <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-indigo-400 rounded-full" style="width:{{ ($total/$maxDept)*100 }}%"></div>
            </div>
            <span class="text-xs font-semibold text-indigo-600 w-32 text-right">Rp {{ number_format($total/1000000, 1) }}jt</span>
        </div>
        @endforeach
    </div>
</div>
@endif
@endif

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200"><tr>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Karyawan</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400">Departemen</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400">Hadir</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Penghasilan</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">PPh 21</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">BPJS</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400">Gaji Bersih</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($slip as $s)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-900">{{ $s->karyawan->nama }}</div>
                    <div class="text-xs text-gray-400">{{ $s->karyawan->jabatan->nama }}</div>
                </td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $s->karyawan->departemen->nama }}</td>
                <td class="px-5 py-3 text-center text-xs">
                    <span class="font-bold text-green-600">{{ $s->hari_hadir }}</span>/<span class="text-gray-400">{{ $s->hari_kerja }}</span>
                </td>
                <td class="px-5 py-3 text-right text-gray-700">Rp {{ number_format($s->total_penghasilan, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right text-xs text-red-500">Rp {{ number_format($s->potongan_pph21, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right text-xs text-purple-500">Rp {{ number_format($s->potongan_bpjs_kesehatan + $s->potongan_bpjs_tk_jht + $s->potongan_bpjs_tk_jp, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right font-bold text-emerald-600">{{ $s->gaji_bersih_format }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">
                {{ $periode ? 'Belum ada slip gaji untuk periode ini' : 'Pilih periode terlebih dahulu' }}
            </td></tr>
            @endforelse
        </tbody>
        @if($slip->count() > 0)
        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
            <tr>
                <td colspan="3" class="px-5 py-3 text-xs font-semibold text-gray-500">TOTAL ({{ $slip->count() }} karyawan)</td>
                <td class="px-5 py-3 text-right font-bold text-gray-900">Rp {{ number_format($slip->sum('total_penghasilan'), 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right font-bold text-red-600">Rp {{ number_format($totalPph21, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right font-bold text-purple-600">Rp {{ number_format($totalBpjs, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right font-bold text-emerald-700">Rp {{ number_format($totalGaji, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
