# Changelog

## v1.5 — 2026-07-22

Eksekusi revisi landing page & pengaturan hasil rapat pra-rilis (2026-07-21) + keputusan susulan owner (2026-07-22). Landing page (`pages/home.blade.php`): rewording 3 fitur (Guru Bersertifikat/Lulusan Ponpes, Bayar Mudah jadi QRIS/transfer bank tanpa e-wallet, Sertifikat Kelulusan), paket baru **Diamond** (Rp1,5jt/bulan, 12x pertemuan 60 menit sama seperti Premium + "Guru pilihan (tahsin/tajwid/qori)") ditambahkan sebagai paket termahal — masuk `Murid::PAKET_OPTIONS` & `TransaksiService::PAKET_PRICES`, otomatis berlaku di validasi (`StoreMuridRequest`/`StoreAdminMuridRequest`/`UpdateAdminMuridRequest`) dan dropdown admin murid (loop dari konstanta, tanpa hardcode). Footer: sosmed sisa Instagram+WhatsApp (YouTube dihapus), urutan layanan jadi Iqra/Tahsin/Tajwid/Qori (Hafalan dihapus dari footer saja, `Murid::LEVEL_OPTIONS` tidak disentuh). Tombol CTA "Daftar Sekarang" di-fix jadi bg hijau + font putih (ada isu CSS `.cta .btn-primary` yang aslinya override ke bg putih/font hijau — di-fix pakai inline style biar sesuai maksud, bukan malah bikin font tidak kelihatan). Tombol "Daftar" di navbar ikut kena bug serupa (`.nav-links a` punya specificity lebih tinggi dari `.btn-primary`, bikin font jadi abu-abu bukan putih) — di-fix pakai inline style `color:var(--white)` juga. Hero-stats di-hide (inline `display:none`, bukan `d-none` — landing page ternyata tidak load Bootstrap CSS). Section baru "Alur/Cara Daftar" (4 langkah) ditambahkan setelah Paket sebelum Testimonial, reuse class existing tanpa CSS baru. Testimoni tidak disentuh. Pengaturan admin (`admin/pengaturan.blade.php`): toggle metode pembayaran sisa Transfer Bank + QRIS (E-Wallet & Kartu Kredit dihapus) — halaman ini murni mock UI, tidak ada backend yang perlu disesuaikan. Tidak ada perubahan skema DB (kolom `paket` tetap string bebas).

## v1.4 — 2026-07-15

Revisi landing page paket pengajian (minor): paket baru "Group" (Rp150K/bulan, maks 8 murid, 8 sesi) ditambahkan sebagai paket pertama — masuk `Murid::PAKET_OPTIONS` dan `TransaksiService::PAKET_PRICES`, otomatis berlaku di validasi (`StoreMuridRequest`/admin murid) dan dropdown admin (loop dari konstanta). Landing page: card Group ditambah sebelum Basic di section Paket, dropdown modal pendaftaran dapat opsi Group, benefit baru ("Privat 1 Murid 1 Guru", "Garansi Ganti Guru Tanpa Biaya", "Materi Disesuaikan dengan Level & Tujuan Murid") ditambahkan ke 4 paket privat (Basic/Pro/Premium/Platinum) — Group dikecualikan karena bukan privat. Title tag & meta description diperbarui. Tidak ada perubahan skema DB, service/controller flow, payment, referral, atau membership.

## v1.3 — 2026-07-13

Admin Murid (CRUD nyata, AJAX): modul admin Murid yang sebelumnya mock diubah jadi CRUD fungsional penuh — list (search server-side + pagination + total + empty state, kolom "Waktu Daftar" tampil tanggal+jam:menit), tambah/edit via modal, detail via modal (style sama Referral Agent), soft delete, export CSV seluruh data (kolom waktu daftar ikut format datetime), semuanya AJAX dengan toast notification & loading state. Route `admin.murid` (closure) diganti `admin.murid.index` (RESTful). Migration `add_soft_deletes_to_murid_table` sudah dijalankan & ditest owner.

## v1.2 — 2026-07-13

Admin Area Authentication: semua route `admin.*` sekarang dilindungi middleware `auth` bawaan Laravel (guest → redirect `/login`), tanpa role/permission layer. Sekalian dibereskan: duplikat route `GET /login` (closure lama vs `AuthController::create()`) dihapus, dan tombol logout di dashboard admin — yang sebelumnya cuma mock UI, gak pernah hit backend — sekarang beneran submit `POST /logout`.

## v1.1 — 2026-07-12

Login (real auth via Laravel's `Auth` facade, rate-limited) dan backend Referral Agent (model, migration, service, capture cookie di `/daftar`).

## v1.0

Initial Project
