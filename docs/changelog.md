# Changelog

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
