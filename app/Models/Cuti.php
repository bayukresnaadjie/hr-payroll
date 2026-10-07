<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cuti extends Model
{
    protected $table = 'cuti';
    protected $fillable = [
        'nomor_cuti','karyawan_id','approved_by','jenis',
        'tanggal_mulai','tanggal_selesai','jumlah_hari',
        'alasan','lampiran','status','catatan_approval','approved_at',
    ];
    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'approved_at'     => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->nomor_cuti)) {
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $model->nomor_cuti = 'CUTI-' . now()->format('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function karyawan()   { return $this->belongsTo(Karyawan::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'menunggu'  => ['label' => 'Menunggu',  'class' => 'bg-yellow-100 text-yellow-700'],
            'disetujui' => ['label' => 'Disetujui', 'class' => 'bg-green-100 text-green-700'],
            'ditolak'   => ['label' => 'Ditolak',   'class' => 'bg-red-100 text-red-700'],
            default     => ['label' => ucfirst($this->status), 'class' => 'bg-gray-100 text-gray-600'],
        };
    }

    public function getJenisLabelAttribute(): string
    {
        return match($this->jenis) {
            'tahunan'    => 'Cuti Tahunan',
            'sakit'      => 'Cuti Sakit',
            'melahirkan' => 'Cuti Melahirkan',
            'penting'    => 'Keperluan Penting',
            'lainnya'    => 'Lainnya',
            default      => ucfirst($this->jenis),
        };
    }
}
