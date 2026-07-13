# AI Context — NgajiNusa

> **Single source of truth (SSOT) for any AI assistant** (Claude, ChatGPT, Gemini, Copilot, Cursor, dll) yang bekerja di repo ini.
> Baca file ini **lebih dulu** sebelum `CLAUDE.md` atau `docs/*.md` lain. Jika terjadi konflik dokumentasi, **file ini menang** (lihat §17 Documentation Map), dan implementasi kode menang atas dokumentasi mana pun.
> Semua isi di bawah sudah diverifikasi terhadap kode nyata. Tidak ada asumsi. Jika sesuatu belum dipastikan, ditandai eksplisit `[OPEN]`.
>
> **Last verified against codebase:** 2026-07-13.

---

## 1. Project Overview

**Tujuan.** NgajiNusa adalah platform pendaftaran untuk kelas ngaji (pengajian) online yang diselenggarakan via Zoom, terbuka untuk masyarakat umum lintas usia. Fokus produk saat ini: **landing page publik + form pendaftaran murid**, plus **panel admin** untuk mengelola operasional.

**Ruang lingkup saat ini (yang benar-benar ada di kode):**

- Landing page publik (`pages/home.blade.php`) dengan modal form pendaftaran.
- Endpoint pendaftaran murid publik (`POST /daftar`) yang menyimpan ke DB dan mengembalikan JSON + nomor WA admin.
- Sistem referral berbasis kode (backend + cookie capture), **tanpa UI admin**.
- Autentikasi admin (login/logout, session-based) dan proteksi semua route `admin.*`.
- Halaman-halaman admin masih berupa **view statis/mock** (belum ada controller/data dinamis) kecuali autentikasinya.
- Middleware pencatat kunjungan (`LogVisitor`).

**Di luar ruang lingkup / belum dibangun:** pembayaran, notifikasi WhatsApp/email, integrasi Zoom, role/permission, reporting, CRUD admin yang sesungguhnya.

**Istilah domain (bahasa Indonesia dipakai konsisten di kode, DB, dan UI — jangan diterjemahkan):**

- **Murid** = calon/peserta yang mendaftar ngaji online. Bukan konsep karyawan/payroll.
- **Paket** = paket langganan belajar (Basic, Pro, Premium, Platinum).
- **Level belajar** = tingkat materi (Hijaiyah, Iqra, Tahsin, Tajwid, Hafalan).
- **Referral Agent** = agen yang punya kode referral; murid yang daftar via `?share_via=KODE` diasosiasikan ke agen tsebut.
- **Guru** = pengajar (baru ada view mock, belum ada model/tabel).
- **Pengajian / Ngaji** = sesi belajar.

**Business flow singkat (yang sudah jalan):**

1. Pengunjung membuka landing page → `GET /` atau `GET /daftar`.
2. Jika ada `?share_via=KODE` valid & aktif → kode disimpan ke cookie `referral_code` (30 hari).
3. Pengunjung mengisi modal pendaftaran (nama, email, WA, level, paket) → JS `fetch('POST /daftar')` (JSON + CSRF).
4. Server memvalidasi (`StoreMuridRequest`), menormalkan nomor WA ke `62xxxx`, set `status = 'Daftar'`, resolve `referral_agent_id` dari cookie, simpan `Murid`.
5. Response JSON berisi data murid + `wa_admin_number` (murid diarahkan chat WA admin untuk proses lanjutan — **manual**).
6. Pembayaran & aktivasi = **proses manual bulanan di luar sistem** (belum ada modul pembayaran).

**Model bisnis pembayaran:** semi-subscription, ditagih bulanan, **manual — tidak auto-charge**. Jangan pernah membangun logika auto-billing tanpa konfirmasi owner.

**Tim:** solo full-stack developer, tanpa kontributor lain. Perlakukan owner sebagai rekan senior yang akrab.

---

## 2. Technology Stack

| Layer                             | Pilihan                                                                      | Catatan verifikasi                                                                                                                                                                                 |
| --------------------------------- | ---------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Framework                         | **Laravel 13** (`laravel/framework: ^13.8`)                                  | `composer.json`                                                                                                                                                                                    |
| Bahasa                            | **PHP 8.5** (target) — `composer.json` masih pin `php: ^8.3`                 | Ada ketidaksesuaian minor: CLAUDE.md bilang 8.5, composer izinkan ≥8.3                                                                                                                             |
| Database (produksi & lokal aktif) | **MySQL / MariaDB**                                                          | `.env` → `DB_CONNECTION=mysql`, `DB_DATABASE=ngaji-nusa`, host `127.0.0.1:3306`, user `root`. Dev lokal jalan di **Laragon** (path `C:\laragon\www\ngaji-nusa`, `APP_URL=http://ngaji-nusa.test/`) |
| Database (opsional/tidak aktif)   | file `database/database.sqlite` ada di repo                                  | **Tidak dipakai** oleh koneksi aktif. Jangan asumsikan lokal = SQLite                                                                                                                              |
| Frontend                          | **Blade + Bootstrap 5 + vanilla JS**                                         | Tidak ada Livewire/Vue/React                                                                                                                                                                       |
| Build tool                        | **Vite 8** + Tailwind 4 (devDependency)                                      | Tailwind terpasang sebagai dev tool, tapi **UI nyata di view pakai CSS custom + Bootstrap 5**, bukan utility Tailwind                                                                              |
| Server (produksi)                 | **Ubuntu + Nginx + PHP-FPM**                                                 | `docs/deployment.md`                                                                                                                                                                               |
| Proxy                             | App di belakang **Cloudflare**                                               | `bootstrap/app.php` set `trustProxies` ke IP range Cloudflare                                                                                                                                      |
| Auth                              | **Built-in Laravel `Auth` facade** (session), hand-rolled — **BUKAN Breeze** | `AuthController` + `LoginRequest`. Breeze tidak terpasang                                                                                                                                          |
| Locale                            | `APP_LOCALE=en`, `APP_TIMEZONE=Asia/Jakarta`                                 | UI teks Indonesia, tapi locale app default `en`                                                                                                                                                    |

**Package penting (composer require):** `laravel/framework ^13.8`, `laravel/tinker ^3.0`.
**Dev deps:** `laravel/pint` (code style), `laravel/pail` (log tail), `laravel/pao`, `nunomaduro/collision`, `phpunit/phpunit ^12`, `mockery`, `fakerphp/faker`.
**Node devDeps:** `vite ^8`, `laravel-vite-plugin ^3.1`, `@tailwindcss/vite ^4`, `tailwindcss ^4`, `concurrently`.

**Third-party / external service:** **belum ada satu pun yang benar-benar terhubung** (lihat §15). Semua referensi Midtrans/Zoom/WhatsApp API di view = placeholder/mock.

**Driver runtime (dari `.env` lokal):** session `database`, cache `database`, queue `database`, filesystem `local`, mail `log` (mail hanya masuk ke log file, tidak terkirim).

---

## 3. Architecture

**Pola arsitektur (target):**

```
Route → Controller → Service → (Repository hanya jika sudah dipakai) → Model
```

- **Controller**: tipis. Validasi via Form Request, panggil Service, kembalikan response.
- **Service**: tempat business logic, transaksi DB, kode reusable.
- **Model**: persistence/Eloquent saja, tanpa business logic.
- **Repository pattern: TIDAK dipakai** (keputusan sengaja, `docs/decisions.md`). Jangan tambahkan tanpa izin.

**Dependency flow nyata (per 2026-07-13):**

- `MuridController` meng-inject `ReferralAgentService` (constructor/method injection) → contoh penerapan Service layer yang benar.
- Normalisasi nomor WA (`normalizeWhatsapp()`) masih **di dalam `MuridController`** (private method), belum dipindah ke Service. Ini diterima apa adanya — **jangan refactor spekulatif** tanpa diminta.
- `AuthController` memakai `LoginRequest` (yang menampung logika rate-limit + `authenticate()` sendiri), bukan Service.

**Layer responsibility:**

- Validasi **selalu** lewat Form Request (`app/Http/Requests`), **tidak pernah** inline di controller.
- Business logic baru yang non-trivial → letakkan di Service.
- Konstanta domain (opsi valid) hidup di **Model** sebagai `const` (mis. `Murid::LEVEL_OPTIONS`) dan dipakai ulang di Form Request.

**Coding pattern khas project ini:**

- Endpoint publik pendaftaran mengembalikan **JSON** (bukan redirect) — `StoreMuridRequest::failedValidation()` sengaja di-override agar error selalu JSON 422 `{success:false, errors:{...}}`.
- Settings global disimpan sebagai key-value di tabel `admin_settings`, diakses via helper statis `AdminSetting::get($key, $default)` (pengecualian pragmatis terhadap aturan "hindari static").

---

## 4. Folder Structure (aktual, bukan aspirasional)

```
app/
  Http/
    Controllers/   Controller.php (abstract base, kosong), MuridController.php, AuthController.php
    Middleware/    LogVisitor.php
    Requests/      LoginRequest.php, StoreMuridRequest.php, StorePostRequest.php (orphan — lihat §13)
  Models/          Murid.php, ReferralAgent.php, AdminSetting.php, VisitorLog.php, User.php
  Services/        ReferralAgentService.php  (satu-satunya service)
  Providers/       AppServiceProvider.php (kosong, boot/register belum dipakai)
bootstrap/
  app.php          registrasi middleware global (LogVisitor), trustProxies Cloudflare, JSON exception utk api/*
config/            konfigurasi Laravel standar (auth guard 'web'/session/provider users)
database/
  migrations/      users+cache+jobs (default), visitor_logs, murid, admin_settings, referral_agents, add_referral_agent_id_to_murid
  seeders/         DatabaseSeeder.php (buat 1 Test User), AdminSettingSeeder.php (BELUM dipanggil — lihat §13)
  factories/       UserFactory (default)
  database.sqlite  ada tapi tidak dipakai koneksi aktif
resources/views/
  pages/home.blade.php        landing page publik + modal pendaftaran (669 baris)
  auth/login.blade.php        halaman login (267 baris)
  admin/                      dashboard, murid, guru, jadwal, paket, transaksi, laporan, pengaturan (semua view mock/statis)
  admin/partials/section-tabs.blade.php
  layouts/                    app.blade.php (publik), admin.blade.php (shell admin + logout form), auth.blade.php
  welcome.blade.php           default Laravel, tidak dipakai
routes/
  web.php          semua route (publik, auth, admin group)
  console.php      command 'inspire' (default)
tests/
  Feature/ExampleTest.php, Unit/ExampleTest.php   masih scaffolding default — belum ada test nyata
docs/              file ini + dokumen legacy per-topik (lihat §17)
CLAUDE.md          instruksi wajib untuk AI assistant — jangan diedit tanpa diminta
PROJECT_MEMORY.md  catatan audit AI sebelumnya — jangan ditimpa
```

**Yang BELUM ada:** `app/Repositories`, `app/Policies`, `app/Enums`, `app/Events`, `app/Jobs`, `app/Notifications`, `app/Actions`. Jangan asumsikan folder-folder ini ada.

---

## 5. Coding Convention

**Style & prinsip:**

- **PSR-12**. Strict typing bila praktis. Fungsi < ~50 baris. Early return > nested if. Dependency injection > static method (pengecualian pragmatis: `AdminSetting::get()` — jangan diperluas polanya).
- Tidak ada unused import, tidak ada kode duplikat.
- **Eloquent only** — hindari raw SQL, hindari `SELECT *`, cegah N+1 (`with()`, `load()`, `paginate()`).
- Relasi yang dipakai: `belongsTo`, `hasMany` (mis. `Murid::referralAgent`, `ReferralAgent::murid`).
- **Istilah domain tetap Indonesia** (`Murid`, `paket`, `level_belajar`, `nama`, `kode`) — jangan diterjemahkan ke Inggris di kode/DB/UI.

**Naming convention:**

| Tipe            | Konvensi                                  | Contoh nyata                                                   |
| --------------- | ----------------------------------------- | -------------------------------------------------------------- |
| Controller      | `PascalCase` + `Controller`               | `MuridController`, `AuthController`                            |
| Service         | `PascalCase` + `Service`                  | `ReferralAgentService`                                         |
| Form Request    | `Store/Update` + Model + `Request`        | `StoreMuridRequest`, `LoginReques