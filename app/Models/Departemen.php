<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    protected $table = 'departemen';
    protected $fillable = ['kode','nama','kepala','deskripsi','aktif'];
    protected $casts   = ['aktif' => 'boolean'];

    public function jabatan()    { return $this->hasMany(Jabatan::class); }
    public function karyawan()   { return $this->hasMany(Karyawan::class); }

    public function getJumlahKaryawanAttribute()
    {
        return $this->karyawan()->where('status_kerja','aktif')->count();
    }
}
