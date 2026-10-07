<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $fillable = [
        'karyawan_id','tanggal','jam_masuk','jam_keluar',
        'jam_masuk_normal','jam_keluar_normal',
        'terlambat_menit','pulang_cepat_menit','jam_kerja',
        'status','keterangan','foto_masuk','foto_keluar',
        'lokasi_masuk','lokasi_keluar','approved_by',
    ];
    protected $casts = ['tanggal' => 'date'];

    public function karyawan()   { return $this->belongsTo(Karyawan::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'hadir'  => ['label' => 'Hadir',  'class' => 'bg-green-100 text-green-700'],
            'izin'   => ['label' => 'Izin',   'class' => 'bg-blue-100 text-blue-700'],
            'sakit'  => ['label' => 'Sakit',  'class' => 'bg-yellow-100 text-yellow-700'],
            'alpha'  => ['label' => 'Alpha',  'class' => 'bg-red-100 text-red-700'],
            'cuti'   => ['label' => 'Cuti',   'class' => 'bg-purple-100 text-purple-700'],
            'libur'  => ['label' => 'Libur',  'class' => 'bg-gray-100 text-gray-500'],
            default  => ['label' => ucfirst($this->status), 'class' => 'bg-gray-100 text-gray-600'],
        };
    }
}
