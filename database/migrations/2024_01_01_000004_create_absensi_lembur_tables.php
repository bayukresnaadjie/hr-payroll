<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->time('jam_masuk_normal')->default('08:00:00');
            $table->time('jam_keluar_normal')->default('17:00:00');
            $table->integer('terlambat_menit')->default(0);      // menit terlambat
            $table->integer('pulang_cepat_menit')->default(0);   // menit pulang lebih awal
            $table->decimal('jam_kerja', 5, 2)->default(0);      // total jam kerja
            $table->enum('status', ['hadir','izin','sakit','alpha','cuti','libur'])->default('hadir');
            $table->text('keterangan')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->string('foto_keluar')->nullable();
            $table->string('lokasi_masuk')->nullable();
            $table->string('lokasi_keluar')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['karyawan_id','tanggal']);
        });

        Schema::create('lembur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->decimal('durasi_jam', 5, 2)->default(0);
            $table->decimal('tarif_per_jam', 15, 2)->default(0);
            $table->decimal('total_upah', 15, 2)->default(0);
            $table->string('keterangan')->nullable();
            $table->enum('status', ['menunggu','disetujui','ditolak'])->default('menunggu');
            $table->text('catatan_approval')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembur');
        Schema::dropIfExists('absensi');
    }
};
