# TO DO

## Revisi Landing Page & Pengaturan — Rapat Pra-Rilis (2026-07-21 + keputusan susulan 2026-07-22) — SELESAI DIEKSEKUSI 2026-07-22

> Semua poin landing page & pengaturan di bawah sudah dieksekusi. Referral Komisi Agent (Fase 2+) masih PENDING, terpisah — lihat section di bawah.

### Landing Page (`resources/views/pages/home.blade.php`)

- [x] Section Fitur: "Guru Bersertifikat" → "Guru Bersertifikat/Lulusan Ponpes" + tambah deskripsi "lulusan pondok pesantren".
- [x] Section Fitur: "Bayar Mudah" — hapus opsi e-wallet, ganti jadi "Pembayaran via QRIS atau transfer bank".
- [x] Section Fitur: "Sertifikat" → "Sertifikat Kelulusan".
- [x] Section Paket: tambah paket baru **Diamond** (Rp1,5jt/bulan) — isi sama seperti Premium + opsi "Guru pilihan (tahsin/tajwid/qori)". Jumlah sesi: **sama seperti Premium** (keputusan owner 2026-07-22).
- [x] Footer — Sosmed: hapus YouTube, sisakan Instagram + WhatsApp.
- [x] Footer — Layanan: urutan baru Iqra, Tahsin, Tajwid, Qori — "Hafalan" dihapus dari list ini (catatan: `Murid::LEVEL_OPTIONS` di backend TIDAK berubah, ini murni tampilan footer).
- [x] Tombol CTA "Daftar Sekarang": background tetap hijau, ubah warna font jadi putih (keputusan owner 2026-07-22). Catatan eksekusi: CSS `.cta .btn-primary` ternyata override bg jadi putih + font hijau (kebalikan asumsi awal) — kalau dituruti mentah-mentah font putih di atas bg putih jadi tidak kelihatan. Di-fix dengan inline style `background:var(--primary);color:var(--white)` di tombol tersebut supaya hasil akhirnya persis sesuai maksud owner: bg hijau, font putih.
- [x] Header/Hero: hero-stats (Murid Aktif 1.000+, Guru Bersertifikasi 50+, Rating 4.9) di-hide sementara (inline `style="display:none"` — bukan `d-none` karena landing page ternyata tidak load Bootstrap CSS sama sekali, cek `layouts/app.blade.php`).
- [x] Testimoni: no touch, tetap dummy.
- [x] Section baru — Alur/Cara Daftar (infografis 4 langkah), posisi setelah Paket sebelum Testimonial, reuse class `.features`/`.features-grid`/`.feature-card` existing (konsisten visual, tanpa nambah CSS baru).

### Paket Diamond (Backend)

- [x] `Murid::PAKET_OPTIONS` — tambah `'Diamond'`.
- [x] `TransaksiService::PAKET_PRICES` — tambah `'Diamond' => 1_500_000`.
- [x] Validasi (`StoreMuridRequest`, `StoreAdminMuridRequest`, `UpdateAdminMuridRequest`) — otomatis ikut karena rule `in:` loop dari `Murid::PAKET_OPTIONS`, tidak ada hardcode.
- [x] Dropdown admin murid (`admin/murid.blade.php`) — otomatis ikut karena `@foreach (\App\Models\Murid::PAKET_OPTIONS as $paket)`, tidak ada hardcode.

### Pengaturan (`resources/views/admin/pengaturan.blade.php`)

- [x] E-wallet toggle — dihapus, sisa 2 metode: Transfer Bank dan QRIS (keputusan owner 2026-07-22). Checkbox "Kartu Kredit" (unchecked, belum aktif) ikut dihapus supaya benar-benar sisa 2 sesuai keputusan. Halaman ini murni mock UI (route closure, no controller/model), tidak ada const metode pembayaran backend yang perlu disesuaikan.

### Backend Fase 2+ (belum ada implementasi — catatan roadmap, TIDAK termasuk scope eksekusi 2026-07-22)

- [ ] Referral — Komisi Agent: komisi 5% dari harga paket (nominal sementara, belum fix). Trigger perhitungan: saat pembayaran murid masuk DAN terverifikasi di sistem (selaras alur verifikasi manual Transaksi yang sudah ada).
- [ ] Opsional/wacana jangka panjang (bukan requirement pasti): skema multi-line marketing (referral berjenjang) — masih wacana, masuk sebagai opsi jangka panjang saja.

---

## Prioritas Tinggi

- [ ] Admin Murid: Export berdasarkan filter/search (saat ini `AdminMuridController@export` selalu export seluruh data, tanpa memperhatikan search yang aktif di list).
- [ ] Admin Murid: Restore data Soft Delete (halaman Trash + tombol Restore — saat ini cuma soft delete, belum ada cara balikin data yang terhapus).
- [ ] Manajemen User: tab Guru (kelola akun login Guru — beda dari data Guru di menu terpisah).
- [ ] Manajemen User: tab Murid (kelola akun login Murid/wali, kalau nanti dibutuhkan).
- [ ] Manajemen User: Role & Permission granular per menu + per action (View/Create/Edit/Delete/Export/Verify/Approve/Reject) — saat ini baru role-level check kasar (Super Admin vs Admin) di `UserService`, "Permission detail" sesuai requirement awal.
- [ ] Manajemen User: dukungan Multi Role (1 user bisa punya lebih dari 1 role) — saat ini 1 user = 1 role (`role` kolom string tunggal).

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
- [ ] Manajemen User: Reset Password oleh Super Admin (tanpa perlu tahu password lama admin lain).
- [ ] Manajemen User: Audit Log User (siapa mengubah apa, kapan — mirip `transaksi_activities`).
- [ ] Manajemen User: Login History (riwayat login per akun, bukan cuma `last_login_at` terakhir).
- [ ] Manajemen User: Force Logout User (invalidate session user lain dari admin).
- [ ] Manajemen User: Avatar Admin (upload foto profil).
- [ ] Manajemen User: Two Factor Authentication.
- [ ] Manajemen User: Invite Admin via Email (undangan set password sendiri, bukan Super Admin yang set password awal).

## Opsional (menunggu keputusan client — JANGAN diimplementasikan sekarang)

- [ ] Login Agent menggunakan email (kolom `email` di `referral_agents` sudah disiapkan untuk ini).
- [ ] Dashboard Agent.
- [ ] Statistik referral (lebih lengkap dari sekadar jumlah murid).
- [ ] Total komisi — nilai komisi & aturan hitungnya belum diputuskan client.
- [ ] Riwayat referral.
- [ ] Reset password Agent.

## Bug / Tech Debt

- [ ] `database/seeders/AdminSettingSeeder.php` (isi `wa_admin_number`) belum dipanggil dari `DatabaseSeeder::run()` — tidak akan jalan lewat `php artisan db:seed` sampai ditambahkan.

## Selesai (2026-07-14)

- [x] Manajemen User — tab Admin: CRUD nyata akun Admin/Super Admin menggantikan halaman yang belum ada sama sekali. List (search nama/email + filter role + filter status + pagination + sort terbaru + reload async), tambah/edit via modal (password opsional saat edit, konfirmasi password, validasi unik email, min 8 karakter), soft delete via modal konfirmasi + toast + reload async. Role & status pakai konstanta (`User::ROLE_SUPER_ADMIN`/`ROLE_ADMIN`, `User::STATUS_ACTIVE`/`STATUS_INACTIVE`), bukan hardcode string. Business rule (dicek di `UserService`): hanya Super Admin bisa CRUD Admin (Admin biasa read-only di halaman ini), Super Admin tidak bisa hapus/nonaktifkan/ubah role akun sendiri, sistem selalu menjaga minimal 1 Super Admin aktif. Tabel `users` dapat kolom baru: `role`, `status`, `last_login_at`, `created_by`, `updated_by`, `deleted_at` (SoftDeletes). Seeder `Test User` sekarang otomatis jadi Super Admin aktif. Tambahan di luar requirement awal (didokumentasikan transparan, dikonfirmasi lewat pertanyaan ke owner sebelum implementasi): login diblokir kalau akun Status Nonaktif, `last_login_at` ke-update otomatis tiap login sukses. File baru: `UserService`, `AdminUserController`, `StoreAdminUserRequest`/`UpdateAdminUserRequest`, `admin/user.blade.php`, `admin/partials/user-list.blade.php`, `admin-user.css`, migration `add_admin_management_fields_to_users_table`. File diubah: `User` model (SoftDeletes + konstanta + relasi createdBy/updatedBy), `routes/web.php` (`admin.user.*`), `layouts/admin.blade.php` (menu sidebar), `LoginRequest` (cek status + set last_login_at), `DatabaseSeeder`. Tab Guru & Murid di halaman ini sengaja belum dikerjakan (lihat Prioritas Tinggi), begitu juga permission granular per action (lihat Prioritas Tinggi/Rendah).

## Selesai (2026-07-13)

- [x] Admin Murid: modul CRUD nyata menggantikan view mock — list (search server-side + pagination + total + empty state, eager load referral agent, kolom "Waktu Daftar" tampil tanggal+jam:menit), tambah/edit via modal (status otomatis Daftar, referral kosong, normalisasi WA identik pendaftaran publik, proteksi unsaved-changes saat modal ditutup), detail via modal (fetch JSON live, style sama Referral Agent, referral agent kosong tampil "-"), soft delete (`SoftDeletes` + toast + reload async), export CSV seluruh data (kolom Waktu Daftar format datetime, tombol disabled kalau data kosong). File baru: `AdminMuridController`, `MuridService`, `StoreAdminMuridRequest`/`UpdateAdminMuridRequest`, `admin/partials/murid-list.blade.php`, `admin-murid.css`, migration `add_soft_deletes_to_murid_table`. Route `admin.murid` (closure) diganti `admin.murid.index` (RESTful, sidebar & section-tabs disesuaikan). Tambah meta `csrf-token` di layout admin. Migration sudah dijalankan & ditest owner (list, tambah/edit, detail, soft delete, export — semua oke).

- [x] Referral System: halaman & menu admin untuk Referral Agent (list, tambah, edit, toggle aktif/nonaktif, copy kode & link referral). Kolom `email` ditambahkan ke `referral_agents` (unique, disiapkan untuk login Agent nanti). Param URL referral diganti dari `?ref=` jadi `?share_via=` (lewat konstanta `ReferralAgentService::QUERY_PARAM`), dan capture referral sekarang jalan juga di `/` (root), tidak cuma `/daftar`. Sudah ditest owner & fix pasca-review: (1) copy kode/link pakai fallback `execCommand('copy')` biar tetap jalan di non-HTTPS lokal (di VPS HTTPS otomatis pakai clipboard API asli, gak perlu diubah lagi), (2) kode referral sekarang huruf kecil semua (`generateUniqueCode()`), (3) `?share_via=KODE` di address bar otomatis dibersihin via `history.replaceState()` setelah cookie ke-capture. Migration (kolom `email` + unique `whatsapp`) sudah dijalankan manual oleh owner di lokal.

- [x] Admin Area Authentication: semua route `admin.*` di `routes/web.php` dibungkus middleware `auth` bawaan Laravel. Guest yang belum login otomatis diarahkan ke `/login`.
- [x] `routes/web.php` mendaftarkan `GET /login` dua kali (closure lama + `AuthController::create`, dua-duanya bernama `login`). Closure lama dihapus, `AuthController::create()` sekarang satu-satunya handler.
- [x] Tombol "Logout" di `layouts/admin.blade.php` ternyata cuma mock (toast + `window.location.href = '#'`, gak pernah hit `POST /logout`) — duplikat di 8 file `admin/*.blade.php`. Disentralisasi: satu implementasi nyata di layout, submit hidden form ke `route('logout')`.
