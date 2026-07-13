<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminMuridController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\ReferralAgentController;
use App\Services\ReferralAgentService;

/*
|--------------------------------------------------------------------------
| Contoh routes untuk view hasil konversi Blade NgajiNusa
|--------------------------------------------------------------------------
| Ini cuma CONTOH biar nama-nama route() yang dipanggil di layout/admin
| tidak error. Sesuaikan controller & middleware (auth, role, dll) sesuai
| kebutuhan sebenarnya.
*/

// ===== PUBLIC =====
// Capture ?share_via=KODE di sini juga, biar link referral yang di-share ke root domain tetap kena track.
Route::get('/', function (Request $request, ReferralAgentService $referralAgentService) {
    $referralAgentService->captureFromRequest($request);

    return view('pages.home');
})->name('home');

// ===== PENDAFTARAN MURID (baru) =====
Route::get('/daftar', [MuridController::class, 'create'])
    ->name('murid.create');

Route::post('/daftar', [MuridController::class, 'store'])
    ->middleware('throttle:10,1') // maks 10 request per menit per IP, cegah spam
    ->name('murid.store');

// ===== AUTH =====
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store']);
Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

// ===== ADMIN (protected by auth middleware) =====
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
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

    // ===== MURID (CRUD nyata, AJAX) =====
    // /murid/export didaftarkan sebelum /murid/{murid} biar "export" gak ketangkep sbg route model binding.
    Route::get('/murid/export', [AdminMuridController::class, 'export'])->name('murid.export');
    Route::get('/murid', [AdminMuridController::class, 'index'])->name('murid.index');
    Route::post('/murid', [AdminMuridController::class, 'store'])->name('murid.store');
    Route::get('/murid/{murid}', [AdminMuridController::class, 'show'])->name('murid.show');
    Route::put('/murid/{murid}', [AdminMuridController::class, 'update'])->name('murid.update');
    Route::delete('/murid/{murid}', [AdminMuridController::class, 'destroy'])->name('murid.destroy');

    Route::get('/guru', function () {
        return view('admin.guru');
    })->name('guru');

    Route::get('/jadwal', function () {
        return view('admin.jadwal');
    })->name('jadwal');

    Route::get('/paket', function () {
        return view('admin.paket');
    })->name('paket');

    // ===== REFERRAL AGENT =====
    Route::get('/referral-agent', [ReferralAgentController::class, 'index'])->name('referral-agent.index');
    Route::post('/referral-agent', [ReferralAgentController::class, 'store'])->name('referral-agent.store');
    Route::put('/referral-agent/{referralAgent}', [ReferralAgentController::class, 'update'])->name('referral-agent.update');
    Route::patch('/referral-agent/{referralAgent}/toggle-status', [ReferralAgentController::class, 'toggleStatus'])->name('referral-agent.toggle-status');
});
