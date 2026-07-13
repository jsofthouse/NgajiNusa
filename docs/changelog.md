# Changelog

## v1.3 — 2026-07-13

Admin Murid (CRUD nyata, AJAX): modul admin Murid yang sebelumnya mock diubah jadi CRUD fungsional penuh — list (search server-side + pagination + total + empty state, kolom "Waktu Daftar" tampil tanggal+jam:menit), tambah/edit via modal, detail via modal (style sama Referral Agent), soft delete, export CSV seluruh data (kolom waktu daftar ikut format datetime), semuanya AJAX dengan toast notification & loading state. Route `admin.murid` (closure) diganti `admin.murid.index` (RESTful). Migration `add_soft_deletes_to_murid_table` sudah dijalankan & ditest owner.

## v1.2 — 2026-07-13

Admin Area Authentication: semua route `admin.*` sekarang dilindungi middleware `auth` bawaan Laravel (guest → redirect `/login`), tanpa role/permission layer. Sekalian dibereskan: duplikat route `GET /login` (closure lama vs `AuthController::create()`) dihapus, dan tombol logout di dashboard admin — yang sebelumnya cuma mock UI, gak pernah hit backend — sekarang beneran submit `POST /logout`.

## v1.1 — 2026-07-12

Login (real auth via Laravel's `Auth` facade, rate-limited) dan backend Referral Agent (model, migration, service, capture cookie di `/daftar`).

## v1.0

Initial Project
