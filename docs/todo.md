# TO DO

## Prioritas Tinggi

- [ ] Admin Murid: Export berdasarkan filter/search (saat ini `AdminMuridController@export` selalu export seluruh data, tanpa memperhatikan search yang aktif di list).
- [ ] Admin Murid: Restore data Soft Delete (halaman Trash + tombol Restore — saat ini cuma soft delete, belum ada cara balikin data yang terhapus).

## Prioritas Menengah

- [ ] Admin Murid: Bulk Delete.
- [ ] Admin Murid: Bulk Export.
- [ ] Admin Murid: Sorting tiap kolom di tabel list.
- [ ] Admin Murid: Filter Status.
- [ ] Admin Murid: Filter Paket.
- [ ] Admin Murid: Filter Level Belajar.
- [ ] Admin Transaksi: Auto-create transaksi juga untuk murid yang ditambah admin manual dari Manajemen Murid (saat ini `TransaksiService::createFromMurid()` cuma dipanggil dari `MuridController@store` / pendaftaran publik — dikonfirmasi owner 2026-07-13, scope fase ini sengaja cuma pendaftaran publik).

## Prioritas Rendah

- [ ] Admin Murid: Audit Log perubahan data Murid.
- [ ] Admin Murid: Tombol WhatsApp langsung dari modal Detail Murid.
- [ ] Admin Murid: Workflow perubahan status Daftar → Aktif setelah pembayaran manual (butuh keputusan owner soal set status Murid lengkap dulu).
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

- [x] Admin Murid: modul CRUD nyata menggantikan view mock — list (search server-side + pagination + total + empty state, eager load referral agent, kolom "Waktu Daftar" tampil tanggal+jam:menit), tambah/edit via modal (status otomatis Daftar, referral kosong, normalisasi WA identik pendaftaran publik, proteksi unsaved-changes saat modal ditutup), detail via modal (fetch JSON live, style sama Referral Agent, referral agent kosong tampil "-"), soft delete (`SoftDeletes` + toast + reload async), export CSV seluruh data (kolom Waktu Daftar format datetime, tombol disabled kalau data kosong). File baru: `AdminMuridController`, `MuridService`, `StoreAdminMuridRequest`/`UpdateAdminMuridRequest`, `admin/partials/murid-list.blade.php`, `admin-murid.css`, migration `add_soft_deletes_to_murid_table`. Route `admin.murid` (closure) diganti `admin.murid.index` (RESTful, sidebar & section-tabs disesuaikan). Tambah meta `csrf-token` di layout admin. Migration sudah dijalankan & ditest owner (list, tambah/edit, detail, soft delete, export — semua oke).

- [x] Referral System: halaman & menu admin untuk Referral Agent (list, tambah, edit, toggle aktif/nonaktif, copy kode & link referral). Kolom `email` ditambahkan ke `referral_agents` (unique, disiapkan untuk login Agent nanti). Param URL referral diganti dari `?ref=` jadi `?share_via=` (lewat konstanta `ReferralAgentService::QUERY_PARAM`), dan capture referral sekarang jalan juga di `/` (root), tidak cuma `/daftar`. Sudah ditest owner & fix pasca-review: (1) copy kode/link pakai fallback `execCommand('copy')` biar tetap jalan di non-HTTPS lokal (di VPS HTTPS otomatis pakai clipboard API asli, gak perlu diubah lagi), (2) kode referral sekarang huruf kecil semua (`generateUniqueCode()`), (3) `?share_via=KODE` di address bar otomatis dibersihin via `history.replaceState()` setelah cookie ke-capture. Migration (kolom `email` + unique `whatsapp`) sudah dijalankan manual oleh owner di lokal.

- [x] Admin Area Authentication: semua route `admin.*` di `routes/web.php` dibungkus middleware `auth` bawaan Laravel. Guest yang belum login otomatis diarahkan ke `/login`.
- [x] `routes/web.php` mendaftarkan `GET /login` dua kali (closure lama + `AuthController::create`, dua-duanya bernama `login`). Closure lama dihapus, `AuthController::create()` sekarang satu-satunya handler.
- [x] Tombol "Logout" di `layouts/admin.blade.php` ternyata cuma mock (toast + `window.location.href = '#'`, gak pernah hit `POST /logout`) — duplikat di 8 file `admin/*.blade.php`. Disentralisasi: satu implementasi nyata di layout, submit hidden form ke `route('logout')`.
