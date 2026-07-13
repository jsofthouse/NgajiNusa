# TO DO

## Prioritas Normal

- [ ] Referral System: buat halaman & menu admin untuk Referral Agent (list, create, edit, aktif/nonaktif) — gaya mengikuti halaman Murid yang sudah ada. Backend (model, migration, service, cookie capture) sudah jadi di Phase 1; ini tinggal UI-nya.

## Prioritas Rendah

- [ ] Referral System: izinkan admin mengubah kode referral jadi custom "vanity string" (saat ini auto-generate acak huruf+angka lewat `ReferralAgentService::generateUniqueCode()`).

## Bug / Tech Debt

- [ ] `database/seeders/AdminSettingSeeder.php` (isi `wa_admin_number`) belum dipanggil dari `DatabaseSeeder::run()` — tidak akan jalan lewat `php artisan db:seed` sampai ditambahkan.

## Selesai (2026-07-13)

- [x] Admin Area Authentication: semua route `admin.*` di `routes/web.php` dibungkus middleware `auth` bawaan Laravel. Guest yang belum login otomatis diarahkan ke `/login`.
- [x] `routes/web.php` mendaftarkan `GET /login` dua kali (closure lama + `AuthController::create`, dua-duanya bernama `login`). Closure lama dihapus, `AuthController::create()` sekarang satu-satunya handler.
- [x] Tombol "Logout" di `layouts/admin.blade.php` ternyata cuma mock (toast + `window.location.href = '#'`, gak pernah hit `POST /logout`) — duplikat di 8 file `admin/*.blade.php`. Disentralisasi: satu implementasi nyata di layout, submit hidden form ke `route('logout')`.
