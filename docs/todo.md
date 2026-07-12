# TO DO

## Prioritas Normal

- [ ] Referral System: buat halaman & menu admin untuk Referral Agent (list, create, edit, aktif/nonaktif) — gaya mengikuti halaman Murid yang sudah ada. Backend (model, migration, service, cookie capture) sudah jadi di Phase 1; ini tinggal UI-nya.

## Prioritas Rendah

- [ ] Referral System: izinkan admin mengubah kode referral jadi custom "vanity string" (saat ini auto-generate acak huruf+angka lewat `ReferralAgentService::generateUniqueCode()`).

## Bug / Tech Debt

- [ ] `routes/web.php` mendaftarkan `GET /login` dua kali (closure lama baris ~30 + `AuthController::create` baris ~34, dua-duanya bernama `login`). Closure lama yang menang saat matching, jadi `AuthController::create()` saat ini dead code untuk GET. Hapus closure lama.
- [ ] `database/seeders/AdminUserSeeder.php` belum dipanggil dari `DatabaseSeeder::run()` — tidak akan jalan lewat `php artisan db:seed` sampai ditambahkan.
