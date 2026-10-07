<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $fillable = [
        'user_id','departemen_id','jabatan_id','nik','nik_ktp','nama','foto',
        'jenis_kelamin','tempat_lahir','tanggal_lahir','status_kawin','agama','alamat','telepon','email',
        'tanggal_masuk','tanggal_keluar','status_kerja','tipe_kontrak','kontrak_sampai',
        'gaji_pokok','tunjangan_jabatan','tunjangan_transport','tunjangan_makan','tunjangan_lainnya',
        'no_bpjs_kesehatan','no_bpjs_ketenagakerjaan','ikut_bpjs_kesehatan','ikut_bpjs_ketenagakerjaan',
        'bank','no_rekening','nama_rekening','npwp','status_pajak',
    ];
    protected $casts = [
        'tanggal_lahir'  => 'date',
        'tanggal_masuk'  => 'date',
        'tanggal_keluar' => 'date',
        'kontrak_sampai' => 'date',
        'ikut_bpjs_kesehatan'       => 'boolean',
        'ikut_bpjs_ketenagakerjaan' => 'boolean',
        'gaji_pokok'          => 'decimal:2',
        'tunjangan_jabatan'   => 'decimal:2',
        'tunjangan_transport' => 'decimal:2',
        'tunjangan_makan'     => 'decimal:2',
        'tunjangan_lainnya'   => 'decimal:2',
    ];

    // Relations
    public function user()       { return $this->belongsTo(User::class); }
    public function departemen() { return $this->belongsTo(Departemen::class); }
    public function jabatan()    { return $this->belongsTo(Jabatan::class); }
    public function absensi()    { return $this->hasMany(Absensi::class); }
    public function lembur()     { return $this->hasMany(Lembur::class); }
    public function cuti()       { return $this->hasMany(Cuti::class); }
    public function jatahCuti()  { return $this->hasMany(JatahCuti::class); }
    public function slipGaji()   { return $this->hasMany(SlipGaji::class); }

    // Accessors
    public function getUmurAttribute(): int
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : 0;
    }

    public function getMasaKerjaAttribute(): string
    {
        $masuk = $this->tanggal_masuk;
        $years  = $masuk->diffInYears(now());
        $months = $masuk->diffInMonths(now()) % 12;
        return $years > 0 ? "{$years} thn {$months} bln" : "{$months} bulan";
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->nama) . '&background=6366f1&color=fff&size=128';
    }

    public function getTotalTunjanganAttribute(): float
    {
        return (float)($this->tunjangan_jabatan + $this->tunjangan_transport + $this->tunjangan_makan + $this->tunjangan_lainnya);
    }

    public function getTotalPenghasilanKotorAttribute(): float
    {
        return (float)($this->gaji_pokok + $this->total_tunjangan);
    }

    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    public function getStatusKerjaBadgeAttribute(): array
    {
        return match($this->status_kerja) {
            'aktif'       => ['label' => 'Aktif',       'class' => 'bg-green-100 text-green-700'],
            'cuti_panjang'=> ['label' => 'Cuti Panjang','class' => 'bg-yellow-100 text-yellow-700'],
            'resign'      => ['label' => 'Resign',      'class' => 'bg-red-100 text-red-700'],
            'phk'         => ['label' => 'PHK',         'class' => 'bg-gray-100 text-gray-600'],
            default       => ['label' => ucfirst($this->status_kerja), 'class' => 'bg-gray-100 text-gray-600'],
        };
    }

    public function getGajiPokokFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->gaji_pokok, 0, ',', '.');
    }

    // Hitung tarif lembur per jam (UU: 1/173 × gaji pokok)
    public function getTarifLemburPerJamAttribute(): float
    {
        return round($this->gaji_pokok / 173);
    }

    // Jatah cuti tahun ini
    public function jatahCutiTahunIni()
    {
        return $this->jatahCuti()->where('tahun', now()->year)->first();
    }
}
