# TO DO

## Prioritas Rendah

- [ ] Referral System: izinkan admin mengubah kode referral jadi custom "vanity string" (saat ini auto-generate acak huruf+angka lewat `ReferralAgentService::generateUniqueCode()`).

## Opsional (menunggu keputusan client — JANGAN diimplementasikan sekarang)

- [ ] Login Agent menggunakan email (kolom `email` di `referral_agents` sudah disiapkan untuk ini).
- [ ] Dashboard Agent.
- [ ] Statistik referral (lebih lengkap dari sekadar jumlah murid).
- [ ] Total komisi — nilai komisi & aturan hitungnya belum diputuskan client.
- [ ] Riwayat referral.
- [ ] Reset password Agent.

## Bug / Tech Debt

- [ ] `database/seeders/AdminSettingSeeder.php` (isi `wa_admin_number`) belum dipanggil dari `DatabaseSeeder::run()` — tidak akan jalan lewat `php artisan db:seed` sampai ditambahkan.

## Selesai (2026-07-13)

- [x] Referral System: halaman & menu admin untuk Referral Agent (list, tambah, edit, toggle aktif/nonaktif, copy kode & link referral). Kolom `email` ditambahkan ke `referral_agents` (unique, disiapkan untuk login Agent nanti). Param URL referral diganti dari `?ref=` jadi `?share_via=` (lewat konstanta `ReferralAgentService::QUERY_PARAM`), dan capture referral sekarang jalan juga di `/` (root), tidak cuma `/daftar`. Sudah ditest owner & fix pasca-review: (1) copy kode/link pakai fallback `execCommand('copy')` biar tetap ja