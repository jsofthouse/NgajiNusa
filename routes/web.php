<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MuridController;

/*
|--------------------------------------------------------------------------
| Contoh routes untuk view hasil konversi Blade NgajiNusa
|--------------------------------------------------------------------------
| Ini cuma CONTOH biar nama-nama route() yang dipanggil di layout/admin
| tidak error. Sesuaikan controller & middleware (auth, role, dll) sesuai
| kebutuhan sebenarnya.
*/

// ===== PUBLIC =====
Route::get('/', function () {
    return view('pages.home');
})->name('home');

// ===== PENDAFTARAN MURID (baru) =====
Route::post('/daftar', [MuridController::class, 'store'])
    ->middleware('throttle:10,1') // maks 10 request per menit per IP, cegah spam
    ->name('murid.store');

// ===== AUTH =====
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Route::post('/login', [AuthController::class, 'login'])
//     ->middleware('throttle:5,1'); // 5 attempts per 1 menit

// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== ADMIN (sebaiknya dibungkus middleware auth + prefix, contoh di bawah) =====
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/transaksi', function () {
        return view('admin.transaksi');
    })->name('transaksi');

    Route::get('/laporan', function () {
        return view('admin.laporan');
    })->name('laporan');

    Route::get('/pengaturan', function () {
        return view('admin.pengaturan');
    })->name('pengaturan');

    // Sekarang beneran ada view-nya (diambil dari tab tersembunyi di dashboard_html.html asli)
    Route::get('/murid', function () {
        return view('admin.murid');
    })->name('murid');

    Route::get('/guru', function () {
        return view('admin.guru');
    })->name('guru');

    Route::get('/jadwal', function () {
        return view('admin.jadwal');
    })->name('jadwal');

    Route::get('/paket', function () {
        return view('admin.paket');
    })->name('paket');
});
