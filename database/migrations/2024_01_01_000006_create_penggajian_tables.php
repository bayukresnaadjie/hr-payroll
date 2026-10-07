<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Header penggajian per periode
        Schema::create('periode_gaji', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();           // e.g. GAJI-2024-10
            $table->string('nama');                          // e.g. Gaji Oktober 2024
            $table->integer('bulan');
            $table->year('tahun');
            $table->date('tanggal_bayar');
            $table->enum('status', ['draft','proses','final','dibayar'])->default('draft');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('difinalisasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalisasi_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['bulan','tahun']);
        });

        // Detail gaji per karyawan per periode
        Schema::create('slip_gaji', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_slip', 30)->unique();
            $table->foreignId('periode_id')->constrained('periode_gaji')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();

            // Kehadiran
            $table->integer('hari_kerja')->default(0);      // hari kerja bulan ini
            $table->integer('hari_hadir')->default(0);
            $table->integer('hari_izin')->default(0);
            $table->integer('hari_sakit')->default(0);
            $table->integer('hari_alpha')->default(0);
            $table->integer('hari_cuti')->default(0);
            $table->decimal('total_terlambat_menit', 8, 2)->default(0);

            // Penghasilan
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('tunjangan_jabatan', 15, 2)->default(0);
            $table->decimal('tunjangan_transport', 15, 2)->default(0);
            $table->decimal('tunjangan_makan', 15, 2)->default(0);
            $table->decimal('tunjangan_lainnya', 15, 2)->default(0);
            $table->decimal('upah_lembur', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('total_penghasilan', 15, 2)->default(0);

            // Potongan
            $table->decimal('potongan_bpjs_kesehatan', 15, 2)->default(0);   // 1%
            $table->decimal('potongan_bpjs_tk_jht', 15, 2)->default(0);      // 2%
            $table->decimal('potongan_bpjs_tk_jp', 15, 2)->default(0);       // 1%
            $table->decimal('potongan_pph21', 15, 2)->default(0);
            $table->decimal('potongan_alpha', 15, 2)->default(0);
            $table->decimal('potongan_terlambat', 15, 2)->default(0);
            $table->decimal('potongan_lainnya', 15, 2)->default(0);
            $table->decimal('total_potongan', 15, 2)->default(0);

            // Iuran perusahaan (tidak memotong gaji tapi dicatat)
            $table->decimal('bpjs_kes_perusahaan', 15, 2)->default(0);       // 4%
            $table->decimal('bpjs_jht_perusahaan', 15, 2)->default(0);       // 3.7%
            $table->decimal('bpjs_jp_perusahaan', 15, 2)->default(0);        // 2%
            $table->decimal('bpjs_jkk_perusahaan', 15, 2)->default(0);       // 0.24%
            $table->decimal('bpjs_jkm_perusahaan', 15, 2)->default(0);       // 0.3%

            $table->decimal('gaji_bersih', 15, 2)->default(0);
            $table->enum('status', ['draft','final','dibayar'])->default('draft');
            $table->timestamp('dibayar_at')->nullable();
            $table->timestamps();

            $table->unique(['periode_id','karyawan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slip_gaji');
        Schema::dropIfExists('periode_gaji');
    }
};
