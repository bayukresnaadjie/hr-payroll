<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\LaporanController;

// ─── AUTH ────────────────────────────────────────────────────────────────────
Route::get('/login', fn() => view('auth.login'))->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate(['email'=>'required|email','password'=>'required']);
    if (\Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }
    return back()->withErrors(['email'=>'Email atau password salah.'])->onlyInput('email');
})->name('login.post');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// ─── ADMIN ROUTES ────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Karyawan
    Route::resource('karyawan', KaryawanController::class)->except(['destroy']);

    // Departemen
    Route::resource('departemen', DepartemenController::class)->except(['show','destroy']);

    // Absensi
    Route::get('/absensi',              [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi',             [AbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/absensi/rekap',        [AbsensiController::class, 'rekap'])->name('absensi.rekap');

    // Cuti
    Route::get('/cuti',                 [CutiController::class, 'index'])->name('cuti.index');
    Route::get('/cuti/create',          [CutiController::class, 'create'])->name('cuti.create');
    Route::post('/cuti',                [CutiController::class, 'store'])->name('cuti.store');
    Route::patch('/cuti/{cuti}/approval',[CutiController::class, 'approval'])->name('cuti.approval');

    // Lembur
    Route::get('/lembur',               [LemburController::class, 'index'])->name('lembur.index');
    Route::get('/lembur/create',        [LemburController::class, 'create'])->name('lembur.create');
    Route::post('/lembur',              [LemburController::class, 'store'])->name('lembur.store');
    Route::patch('/lembur/{lembur}/approval',[LemburController::class, 'approval'])->name('lembur.approval');

    // Penggajian
    Route::get('/penggajian',                        [PenggajianController::class, 'index'])->name('penggajian.index');
    Route::get('/penggajian/create',                 [PenggajianController::class, 'create'])->name('penggajian.create');
    Route::post('/penggajian',                       [PenggajianController::class, 'store'])->name('penggajian.store');
    Route::get('/penggajian/{penggajian}',           [PenggajianController::class, 'show'])->name('penggajian.show');
    Route::post('/penggajian/{penggajian}/proses',   [PenggajianController::class, 'proses'])->name('penggajian.proses');
    Route::post('/penggajian/{penggajian}/finalisasi',[PenggajianController::class, 'finalisasi'])->name('penggajian.finalisasi');
    Route::post('/penggajian/{penggajian}/bayar',    [PenggajianController::class, 'bayar'])->name('penggajian.bayar');
    Route::get('/slip/{slip}',                       [PenggajianController::class, 'slip'])->name('slip.show');
    Route::get('/slip/{slip}/pdf',                   [PenggajianController::class, 'slipPdf'])->name('slip.pdf');

    // Laporan
    Route::get('/laporan/penggajian',   [LaporanController::class, 'penggajian'])->name('laporan.penggajian');
    Route::get('/laporan/absensi',      [LaporanController::class, 'absensi'])->name('laporan.absensi');
    Route::get('/laporan/karyawan',     [LaporanController::class, 'karyawan'])->name('laporan.karyawan');
});
