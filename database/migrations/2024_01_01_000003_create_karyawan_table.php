<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('departemen_id')->constrained('departemen');
            $table->foreignId('jabatan_id')->constrained('jabatan');

            // Identitas
            $table->string('nik', 20)->unique();
            $table->string('nik_ktp', 16)->nullable();
            $table->string('nama');
            $table->string('foto')->nullable();
            $table->enum('jenis_kelamin', ['L','P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            // PERBAIKAN: tambah K0,K1,K2,K3 di status_kawin
            $table->enum('status_kawin', ['TK','TK1','TK2','TK3','K','K1','K2','K3'])->default('TK');
            $table->string('agama')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email')->nullable();

            // Kepegawaian
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status_kerja', ['aktif','cuti_panjang','resign','phk'])->default('aktif');
            $table->enum('tipe_kontrak', ['tetap','kontrak','magang','harian'])->default('tetap');
            $table->date('kontrak_sampai')->nullable();

            // Gaji
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('tunjangan_jabatan', 15, 2)->default(0);
            $table->decimal('tunjangan_transport', 15, 2)->default(0);
            $table->decimal('tunjangan_makan', 15, 2)->default(0);
            $table->decimal('tunjangan_lainnya', 15, 2)->default(0);

            // BPJS
            $table->string('no_bpjs_kesehatan', 30)->nullable();
            $table->string('no_bpjs_ketenagakerjaan', 30)->nullable();
            $table->boolean('ikut_bpjs_kesehatan')->default(true);
            $table->boolean('ikut_bpjs_ketenagakerjaan')->default(true);

            // Bank
            $table->string('bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('nama_rekening')->nullable();

            // NPWP & Pajak
            $table->string('npwp', 20)->nullable();
            // PERBAIKAN: tambah TK1,TK2,TK3 di status_pajak
            $table->enum('status_pajak', ['TK0','TK1','TK2','TK3','K0','K1','K2','K3'])->default('TK0');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
