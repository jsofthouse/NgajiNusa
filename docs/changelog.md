# Changelog

## v1.2 — 2026-07-13

Admin Area Authentication: semua route `admin.*` sekarang dilindungi middleware `auth` bawaan Laravel (guest → redirect `/login`), tanpa role/permission layer. Sekalian dibereskan: duplikat route `GET /login` (closure lama vs `AuthController::create()`) dihapus, dan tombol logout di dashboard admin — yang sebelumnya cuma mock UI, gak pernah hit backend — sekarang beneran submit `POST /logout`.

## v1.1 — 2026-07-12

Login (real auth via Laravel's `Auth` facade, rate-limited) dan backend Referral Agent (model, migration, service, capture cookie di `/daftar`).

## v1.0

Initial Project
