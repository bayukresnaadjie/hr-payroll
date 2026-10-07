<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JatahCuti extends Model
{
    protected $table = 'jatah_cuti';
    protected $fillable = ['karyawan_id','tahun','jatah','terpakai'];

    public function karyawan() { return $this->belongsTo(Karyawan::class); }

    public function getSisaAttribute(): int
    {
        return max(0, $this->jatah - $this->terpakai);
    }
}
