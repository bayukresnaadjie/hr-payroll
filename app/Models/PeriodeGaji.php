<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeGaji extends Model
{
    protected $table = 'periode_gaji';
    protected $fillable = [
        'kode','nama','bulan','tahun','tanggal_bayar',
        'status','dibuat_oleh','difinalisasi_oleh','finalisasi_at','catatan',
    ];
    protected $casts = [
        'tanggal_bayar'  => 'date',
        'finalisasi_at'  => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->kode)) {
                $model->kode = 'GAJI-' . $model->tahun . '-' . str_pad($model->bulan, 2, '0', STR_PAD_LEFT);
            }
            if (empty($model->nama)) {
                $bulanNama = \Carbon\Carbon::create($model->tahun, $model->bulan, 1)->translatedFormat('F Y');
                $model->nama = 'Gaji ' . $bulanNama;
            }
        });
    }

    public function slipGaji()        { return $this->hasMany(SlipGaji::class, 'periode_id'); }
    public function dibuatOleh()      { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function difinalisasiOleh(){ return $this->belongsTo(User::class, 'difinalisasi_oleh'); }

    public function getTotalGajiBersihAttribute(): float
    {
        return (float) $this->slipGaji()->sum('gaji_bersih');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'draft'   => ['label' => 'Draft',   'class' => 'bg-gray-100 text-gray-600'],
            'proses'  => ['label' => 'Proses',  'class' => 'bg-yellow-100 text-yellow-700'],
            'final'   => ['label' => 'Final',   'class' => 'bg-blue-100 text-blue-700'],
            'dibayar' => ['label' => 'Dibayar', 'class' => 'bg-green-100 text-green-700'],
            default   => ['label' => ucfirst($this->status), 'class' => 'bg-gray-100 text-gray-500'],
        };
    }
}
