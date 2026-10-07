<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $fillable = ['departemen_id','kode','nama','level','gaji_pokok_min','gaji_pokok_max','aktif'];
    protected $casts   = ['aktif' => 'boolean', 'gaji_pokok_min' => 'decimal:2', 'gaji_pokok_max' => 'decimal:2'];

    public function departemen() { return $this->belongsTo(Departemen::class); }
    public function karyawan()   { return $this->hasMany(Karyawan::class); }

    public function getLevelLabelAttribute(): string
    {
        return match($this->level) {
            'staff'      => 'Staff',
            'supervisor' => 'Supervisor',
            'manager'    => 'Manager',
            'director'   => 'Director',
            'c_level'    => 'C-Level',
            default      => ucfirst($this->level),
        };
    }

    public function getRangeGajiAttribute(): string
    {
        return 'Rp ' . number_format($this->gaji_pokok_min, 0, ',', '.') .
               ' – Rp ' . number_format($this->gaji_pokok_max, 0, ',', '.');
    }
}
