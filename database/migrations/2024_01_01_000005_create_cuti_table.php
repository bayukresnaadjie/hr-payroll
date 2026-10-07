<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jatah_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->year('tahun');
            $table->integer('jatah')->default(12);        // hari cuti setahun
            $table->integer('terpakai')->default(0);
            $table->integer('sisa')->virtualAs('jatah - terpakai');
            $table->timestamps();
            $table->unique(['karyawan_id','tahun']);
        });

        Schema::create('cuti', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_cuti', 30)->unique();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('jenis', ['tahunan','sakit','melahirkan','penting','lainnya'])->default('tahunan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('jumlah_hari');
            $table->text('alasan');
            $table->string('lampiran')->nullable();
            $table->enum('status', ['menunggu','disetujui','ditolak'])->default('menunggu');
            $table->text('catatan_approval')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuti');
        Schema::dropIfExists('jatah_cuti');
    }
};
