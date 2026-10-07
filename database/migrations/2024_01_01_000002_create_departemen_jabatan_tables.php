<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('departemen', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama');
            $table->string('kepala')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departemen_id')->constrained('departemen')->cascadeOnDelete();
            $table->string('kode', 10)->unique();
            $table->string('nama');
            $table->enum('level', ['staff','supervisor','manager','director','c_level'])->default('staff');
            $table->decimal('gaji_pokok_min', 15, 2)->default(0);
            $table->decimal('gaji_pokok_max', 15, 2)->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan');
        Schema::dropIfExists('departemen');
    }
};
