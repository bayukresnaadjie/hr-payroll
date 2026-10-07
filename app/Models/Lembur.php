<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    protected $table = 'lembur';
    protected $fillable = [
        'karyawan_id','approved_by','tanggal',
        'jam_mulai','jam_selesai','durasi_jam',
        'tarif_per_jam','total_upah','keterangan',
        'status','catatan_approval','approved_at',
    ];
    protected $casts = [
        'tanggal'     => 'date',
        'approved_at' => 'datetime',
        'durasi_jam'  => 'decimal:2',
        'tarif_per_jam' => 'decimal:2',
        'total_upah'    => 'decimal:2',
    ];

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

    public function getTotalUpahFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->total_upah, 0, ',', '.');
    }
}
