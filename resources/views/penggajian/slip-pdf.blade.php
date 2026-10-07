<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <title>Slip Gaji {{ $slip->nomor_slip }}</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Arial,sans-serif;font-size:11px;color:#111;padding:20px}
        .header{background:#312e81;color:#fff;padding:16px;border-radius:6px;margin-bottom:12px;display:flex;justify-content:space-between}
        .header h1{font-size:14px;margin-bottom:2px}
        .header small{color:#a5b4fc;font-size:10px}
        .grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
        .section-title{font-size:9px;font-weight:bold;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;padding-bottom:3px;border-bottom:1px solid #e5e7eb}
        .row{display:flex;justify-content:space-between;padding:2px 0;border-bottom:1px solid #f3f4f6}
        .row.total{font-weight:bold;border-top:2px solid #d1d5db;border-bottom:none;margin-top:4px;padding-top:4px}
        .kehadiran{display:grid;grid-template-columns:repeat(6,1fr);gap:4px;margin-bottom:12px}
        .kh-item{background:#f9fafb;border:1px solid #e5e7eb;border-radius:4px;padding:6px;text-align:center}
        .kh-item .val{font-size:16px;font-weight:bold}
        .kh-item .lbl{font-size:8px;color:#9ca3af}
        .takehome{background:#ecfdf5;border:2px solid #6ee7b7;padding:12px;border-radius:6px;display:flex;justify-content:space-between;align-items:center;margin-top:12px}
        .takehome .amount{font-size:18px;font-weight:bold;color:#059669}
        .info{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:12px}
        .info-item span:first-child{color:#9ca3af;font-size:9px;display:block}
        .info-item span:last-child{font-weight:600}
    </style>
</head>
<body>
<div class="header">
    <div><h1>SLIP GAJI KARYAWAN</h1><small>{{ $slip->periode->nama }}</small></div>
    <div style="text-align:right"><div style="font-family:monospace;font-size:10px">{{ $slip->nomor_slip }}</div><small>{{ $slip->dibayar_at ? $slip->dibayar_at->format('d M Y') : now()->format('d M Y') }}</small></div>
</div>

<div class="info">
    <div class="info-item"><span>Nama</span><span>{{ $slip->karyawan->nama }}</span></div>
    <div class="info-item"><span>NIK</span><span style="font-family:monospace">{{ $slip->karyawan->nik }}</span></div>
    <div class="info-item"><span>Jabatan</span><span>{{ $slip->karyawan->jabatan->nama }}</span></div>
    <div class="info-item"><span>Departemen</span><span>{{ $slip->karyawan->departemen->nama }}</span></div>
    <div class="info-item"><span>Status Pajak (PTKP)</span><span>{{ $slip->karyawan->status_pajak }}</span></div>
    <div class="info-item"><span>Rekening</span><span>{{ $slip->karyawan->bank }} — {{ $slip->karyawan->no_rekening }}</span></div>
</div>

<div class="section-title">Kehadiran</div>
<div class="kehadiran">
    @foreach(['Hari Kerja'=>$slip->hari_kerja,'Hadir'=>$slip->hari_hadir,'Izin'=>$slip->hari_izin,'Sakit'=>$slip->hari_sakit,'Alpha'=>$slip->hari_alpha,'Terlambat'=>round($slip->total_terlambat_menit).'mn'] as $l => $v)
    <div class="kh-item"><div class="val">{{ $v }}</div><div class="lbl">{{ $l }}</div></div>
    @endforeach
</div>

<div class="grid2">
    <div>
        <div class="section-title">Penghasilan</div>
        @foreach(['Gaji Pokok'=>$slip->gaji_pokok,'Tj. Jabatan'=>$slip->tunjangan_jabatan,'Tj. Transport'=>$slip->tunjangan_transport,'Tj. Makan'=>$slip->tunjangan_makan,'Upah Lembur'=>$slip->upah_lembur,'Bonus'=>$slip->bonus] as $l => $v)
        @if($v > 0)<div class="row"><span>{{ $l }}</span><span>Rp {{ number_format($v,0,',','.') }}</span></div>@endif
        @endforeach
        <div class="row total"><span>Total Penghasilan</span><span>Rp {{ number_format($slip->total_penghasilan,0,',','.') }}</span></div>
    </div>
    <div>
        <div class="section-title">Potongan</div>
        @foreach(['BPJS Kes 1%'=>$slip->potongan_bpjs_kesehatan,'BPJS JHT 2%'=>$slip->potongan_bpjs_tk_jht,'BPJS JP 1%'=>$slip->potongan_bpjs_tk_jp,'PPh 21'=>$slip->potongan_pph21,'Alpha'=>$slip->potongan_alpha,'Terlambat'=>$slip->potongan_terlambat,'Lainnya'=>$slip->potongan_lainnya] as $l => $v)
        @if($v > 0)<div class="row"><span>{{ $l }}</span><span style="color:#dc2626">Rp {{ number_format($v,0,',','.') }}</span></div>@endif
        @endforeach
        <div class="row total"><span>Total Potongan</span><span style="color:#dc2626">Rp {{ number_format($slip->total_potongan,0,',','.') }}</span></div>
    </div>
</div>

<div class="takehome">
    <div><div style="font-weight:600;color:#065f46">GAJI BERSIH (TAKE HOME PAY)</div><div style="color:#6ee7b7;font-size:10px">{{ $slip->karyawan->bank }} — {{ $slip->karyawan->no_rekening }}</div></div>
    <div class="amount">{{ $slip->gaji_bersih_format }}</div>
</div>
</body>
</html>
