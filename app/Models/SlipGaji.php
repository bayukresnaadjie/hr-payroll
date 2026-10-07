<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlipGaji extends Model
{
    protected $table = 'slip_gaji';
    protected $fillable = [
        'nomor_slip','periode_id','karyawan_id',
        'hari_kerja','hari_hadir','hari_izin','hari_sakit','hari_alpha','hari_cuti','total_terlambat_menit',
        'gaji_pokok','tunjangan_jabatan','tunjangan_transport','tunjangan_makan','tunjangan_lainnya',
        'upah_lembur','bonus','total_penghasilan',
        'potongan_bpjs_kesehatan','potongan_bpjs_tk_jht','potongan_bpjs_tk_jp',
        'potongan_pph21','potongan_alpha','potongan_terlambat','potongan_lainnya','total_potongan',
        'bpjs_kes_perusahaan','bpjs_jht_perusahaan','bpjs_jp_perusahaan','bpjs_jkk_perusahaan','bpjs_jkm_perusahaan',
        'gaji_bersih','status','dibayar_at',
    ];
    protected $casts = ['dibayar_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->nomor_slip)) {
                $count = static::where('periode_id', $model->periode_id)->count() + 1;
                $periode = PeriodeGaji::find($model->periode_id);
                $model->nomor_slip = 'SLIP-' . $periode->kode . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function periode()   { return $this->belongsTo(PeriodeGaji::class, 'periode_id'); }
    public function karyawan()  { return $this->belongsTo(Karyawan::class); }

    public function getGajiBersihFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->gaji_bersih, 0, ',', '.');
    }

    public function getTotalPenghasilanFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->total_penghasilan, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'draft'   => ['label' => 'Draft',   'class' => 'bg-gray-100 text-gray-600'],
            'final'   => ['label' => 'Final',   'class' => 'bg-blue-100 text-blue-700'],
            'dibayar' => ['label' => 'Dibayar', 'class' => 'bg-green-100 text-green-700'],
            default   => ['label' => ucfirst($this->status), 'class' => 'bg-gray-100 text-gray-500'],
        };
    }

    /**
     * Hitung PPh 21 dengan skema TER (Tarif Efektif Rata-rata) 2024
     * PMK No. 168/PMK.010/2023
     */
    public static function hitungPph21(float $penghasilanBruto, string $statusPajak): float
    {
        // PTKP 2024
        $ptkp = match($statusPajak) {
            'TK0' => 54_000_000,
            'TK1' => 58_500_000,
            'TK2' => 63_000_000,
            'TK3' => 67_500_000,
            'K0'  => 58_500_000,
            'K1'  => 63_000_000,
            'K2'  => 67_500_000,
            'K3'  => 72_000_000,
            default => 54_000_000,
        };

        $penghasilanNettoSetahun = ($penghasilanBruto * 12) - $ptkp;

        if ($penghasilanNettoSetahun <= 0) return 0;

        // Tarif progresif Pasal 17
        $pph = 0;
        if ($penghasilanNettoSetahun <= 60_000_000) {
            $pph = $penghasilanNettoSetahun * 0.05;
        } elseif ($penghasilanNettoSetahun <= 250_000_000) {
            $pph = 60_000_000 * 0.05 + ($penghasilanNettoSetahun - 60_000_000) * 0.15;
        } elseif ($penghasilanNettoSetahun <= 500_000_000) {
            $pph = 60_000_000 * 0.05 + 190_000_000 * 0.15 + ($penghasilanNettoSetahun - 250_000_000) * 0.25;
        } elseif ($penghasilanNettoSetahun <= 5_000_000_000) {
            $pph = 60_000_000 * 0.05 + 190_000_000 * 0.15 + 250_000_000 * 0.25 + ($penghasilanNettoSetahun - 500_000_000) * 0.30;
        } else {
            $pph = 60_000_000 * 0.05 + 190_000_000 * 0.15 + 250_000_000 * 0.25 + 4_500_000_000 * 0.30 + ($penghasilanNettoSetahun - 5_000_000_000) * 0.35;
        }

        return round($pph / 12); // per bulan
    }
}
