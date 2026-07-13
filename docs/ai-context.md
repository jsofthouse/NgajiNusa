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
| Form Request    | `Store/Update` + Model + `Request`        | `StoreMuridRequest`, `LoginRequest`                            |
| Model           | PascalCase singular                       | `Murid`, `ReferralAgent`, `AdminSetting`                       |
| Migration       | snake_case deskriptif                     | `create_murid_table`, `add_referral_agent_id_to_murid_table`   |
| Method          | camelCase                                 | `normalizeWhatsapp()`, `captureFromRequest()`                  |
| Konstanta model | UPPER_SNAKE, biasanya `_OPTIONS` utk enum | `Murid::LEVEL_OPTIONS`, `ReferralAgent::STATUS_ACTIVE`         |
| Tabel/kolom DB  | snake_case                                | `murid`, `referral_agents`, `wa_admin_number`, `level_belajar` |
| Route (path)    | kebab-case / Indonesia                    | `/daftar`, `/login`, `admin/dashboard`                         |
| Route name      | dot notation                              | `murid.store`, `admin.dashboard`, `login`, `logout`            |

**Wajib dipatuhi:**

- Validasi hanya di Form Request.
- Tabel `murid` bernama **singular** (bukan `murids`) — model set `$table = 'murid'` eksplisit.
- Nomor WhatsApp selalu disimpan format `62xxxxxxxxxx` (tanpa `+`, tanpa `0` depan).
- Jangan mengarang nilai enum baru (status Murid, dsb) — cek konstanta model dulu.

---

## 6. Database

Koneksi aktif: **MySQL/MariaDB** (`ngaji-nusa`). Semua tabel dibuat via migration.

### Tabel & kolom

**`users`** (default Laravel auth)
`id, name, email (unique), email_verified_at, password (hashed), remember_token, timestamps`. Plus `password_reset_tokens`, `sessions` (session driver = database).

**`murid`** (tabel inti pendaftaran, **singular**)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint PK | |
| nama | string | |
| email | string | index |
| whatsapp | string(20) | disimpan `62xxxx` |
| level_belajar | string(50) | salah satu `Murid::LEVEL_OPTIONS` |
| paket | string(50) | salah satu `Murid::PAKET_OPTIONS` |
| status | string(30) | default `'Daftar'`, index |
| referral_agent_id | bigint FK nullable | → `referral_agents.id`, `nullOnDelete()` |
| timestamps | | |

Index: `status`, `email`. Fillable termasuk semua kolom di atas + `referral_agent_id`.

**`referral_agents`**
`id, nama, email(150, unique), whatsapp(20, unique), kode(50, unique), status(20) default 'Aktif' (index), timestamps`. Relasi `hasMany(Murid)`. Kolom `email` disiapkan untuk fitur login Agent di masa depan (belum diimplementasikan).

**`admin_settings`** (key-value store)
`id, key(100, unique), value(255), timestamps`. Diakses via `AdminSetting::get($key, $default)`.

**`visitor_logs`**
`id, visit_date(date, index), path(255), ip_address(45, nullable), hit_count(unsigned int, default 1), timestamps`. **Unique** pada `(visit_date, path, ip_address)`. Ditulis oleh `LogVisitor` (increment `hit_count`).

Plus tabel default framework: `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`.

### Enum / konstanta penting (di Model, bukan DB enum)

- `Murid::LEVEL_OPTIONS` = `['Hijaiyah', 'Iqra', 'Tahsin', 'Tajwid', 'Hafalan']`
- `Murid::PAKET_OPTIONS` = `['Basic', 'Pro', 'Premium', 'Platinum']`
- `Murid::STATUS_DAFTAR` = `'Daftar'` — **satu-satunya status yang terdefinisi**. Komentar migration menyebut status lain menyusul (`Aktif, Pending, Nonaktif, dst`) tapi **belum ada** → jangan mengarang nilai status.
- `ReferralAgent::STATUS_ACTIVE` = `'Aktif'`, `ReferralAgent::STATUS_INACTIVE` = `'Nonaktif'`, `ReferralAgent::STATUS_OPTIONS` = `[STATUS_ACTIVE, STATUS_INACTIVE]` (dipakai ulang di Form Request).

### Relasi

- `Murid belongsTo ReferralAgent` (`referral_agent_id`, nullable).
- `ReferralAgent hasMany Murid` (via method `murid()`).

### Business constraint

- `referral_agents.kode` unik, `email` unik, `whatsapp` unik. FK `murid.referral_agent_id` set null bila agen dihapus.
- `visitor_logs` unik per `(tanggal, path, ip)` → 1 IP menambah `hit_count`, bukan baris baru.
- `admin_settings.key` unik.

> **Harga paket TIDAK ada di DB/backend.** Angka harga (Basic Rp300K/4 sesi, Pro Rp550K/8 sesi, Premium Rp800K/12 sesi, Platinum Rp1,2jt/20 sesi) hanya **teks di `pages/home.blade.php`**. Tidak ada tabel `pakets`/pricing. Jangan anggap ini otoritatif backend.

---

## 7. Business Rules (yang sudah terimplementasi)

1. **Pendaftaran publik:** `GET /daftar` menampilkan landing + menangkap referral; `POST /daftar` menyimpan murid.
2. **Validasi pendaftaran** (`StoreMuridRequest`): `nama` (3–100), `email` (rfc,dns, max 150), `whatsapp` regex `^(\+?62|0)8[0-9]{8,12}$`, `level_belajar` ∈ `LEVEL_OPTIONS`, `paket` ∈ `PAKET_OPTIONS`. Pesan error berbahasa Indonesia. Error **selalu JSON 422**.
3. **Normalisasi WhatsApp:** `08xx`/`+62xx` → `62xx` sebelum disimpan (untuk konsistensi & link `wa.me`).
4. **Status default** murid baru = `Murid::STATUS_DAFTAR` (`'Daftar'`).
5. **Anti-spam:** `POST /daftar` di-throttle `10,1` (maks 10 req/menit/IP).
6. **Referral capture:** `GET /` atau `GET /daftar` dengan `?share_via=KODE` → jika kode cocok `ReferralAgent` ber-`status=Aktif`, simpan ke cookie `referral_code` (30 hari). Kode invalid diabaikan (tidak disimpan). Nama param disimpan sebagai konstanta `ReferralAgentService::QUERY_PARAM`.
7. **Referral resolve:** saat `POST /daftar`, `referral_agent_id` diambil dari cookie (`resolveAgentIdFromCookie`) — **user tidak pernah input manual**.
8. **Generate kode referral:** `ReferralAgentService::generateUniqueCode()` menghasilkan string acak huruf kecil+angka yang dijamin unik (default 8 char, dipanggil dengan 12 char saat create dari admin). Sengaja acak (tidak dari nama) agar sulit ditebak.
9. **Response sukses pendaftaran:** JSON `{success, message, data:{id,nama,paket,level_belajar}, wa_admin_number}`. `wa_admin_number` diambil dari `AdminSetting::get('wa_admin_number')`.
10. **Visitor logging:** `LogVisitor` mencatat setiap request **GET non-admin** (skip `admin/*`), dedup per `(tanggal, path, ip)`, increment `hit_count`. Tujuan selain analitik dasar = `[OPEN]`.
11. **Login:** `POST /login` (`LoginRequest::authenticate`) rate-limit **5 attempt/menit** per `email|ip`; sukses → regenerate session → redirect `admin.dashboard` (atau intended). Gagal → `hit()` limiter + error `auth.failed`.
12. **Logout:** `POST /logout` → `Auth::logout()`, invalidate session, regenerate CSRF token, redirect `login`.
13. **Proteksi admin:** seluruh grup `admin.*` dibungkus middleware `auth`. Guest → redirect `login`. **Tidak ada** role/permission (sengaja).
14. **Pembayaran = manual bulanan.** Tidak ada auto-charge. Jangan bangun auto-billing tanpa konfirmasi.

---

## 8. Routing (ringkasan `routes/web.php`)

| Method | Path               | Name               | Handler                  | Middleware      | Fungsi                                 |
| ------ | ------------------ | ------------------ | ------------------------ | --------------- | -------------------------------------- |
| GET    | `/`                | `home`             | closure → capture referral + `pages.home` | web             | Landing page                           |
| GET    | `/daftar`          | `murid.create`     | `MuridController@create` | web             | Landing + capture referral             |
| POST   | `/daftar`          | `murid.store`      | `MuridController@store`  | `throttle:10,1` | Simpan murid, return JSON              |
| GET    | `/login`           | `login`            | `AuthController@create`  | web             | Form login                             |
| POST   | `/login`           | —                  | `AuthController@store`   | web             | Proses login (rate-limited di request) |
| POST   | `/logout`          | `logout`           | `AuthController@destroy` | web             | Logout                                 |
| GET    | `admin/dashboard`  | `admin.dashboard`  | closure → view           | **auth**        | Dashboard (mock)                       |
| GET    | `admin/transaksi`  | `admin.transaksi`  | closure → view           | **auth**        | Mock                                   |
| GET    | `admin/laporan`    | `admin.laporan`    | closure → view           | **auth**        | Mock                                   |
| GET    | `admin/pengaturan` | `admin.pengaturan` | closure → view           | **auth**        | Mock (referensi WA/Zoom placeholder)   |
| GET    | `admin/murid`      | `admin.murid`      | closure → view           | **auth**        | Mock (belum CRUD)                      |
| GET    | `admin/guru`       | `admin.guru`       | closure → view           | **auth**        | Mock                                   |
| GET    | `admin/jadwal`     | `admin.jadwal`     | closure → view           | **auth**        | Mock                                   |
| GET    | `admin/paket`      | `admin.paket`      | closure → view           | **auth**        | Mock                                   |
| GET    | `admin/referral-agent` | `admin.referral-agent.index` | `ReferralAgentController@index` | **auth** | List Referral Agent |
| POST   | `admin/referral-agent` | `admin.referral-agent.store` | `ReferralAgentController@store` | **auth** | Tambah Referral Agent |
| PUT    | `admin/referral-agent/{referralAgent}` | `admin.referral-agent.update` | `ReferralAgentController@update` | **auth** | Edit Referral Agent |
| PATCH  | `admin/referral-agent/{referralAgent}/toggle-status` | `admin.referral-agent.toggle-status` | `ReferralAgentController@toggleStatus` | **auth** | Toggle Aktif/Nonaktif |

**Catatan:** sebagian besar route admin masih **closure** return view statis — kecuali `admin.referral-agent.*` yang sudah pakai controller nyata. Health check `/up` aktif (dari `bootstrap/app.php`). Exception di-render JSON hanya untuk `api/*` (belum ada route api).

---

## 9. Authentication

- **Tipe:** session-based, guard `web` (driver `session`, provider `users` → model `User`). Built-in Laravel, **bukan Breeze**.
- **Login flow:** form `auth/login` → `POST /login` → `LoginRequest` validasi (`email`, `password`, `remember?`) → `authenticate()` cek rate limit (5/menit per `email|ip`) → `Auth::attempt()` → sukses regenerate session → `redirect()->intended(admin.dashboard)`.
- **Logout flow:** tombol di `layouts/admin.blade.php` (`confirmLogout()` → submit hidden `#logoutForm` POST ke `route('logout')`, `@csrf`) → `Auth::guard('web')->logout()` → invalidate + regenerate token → redirect `login`. **Satu-satunya** implementasi logout ada di layout (sebelumnya tiap 8 halaman admin punya mock palsu; sudah disentralisasi 2026-07-13).
- **Proteksi route:** middleware `auth` membungkus grup `admin.*`. Guest → `login`.
- **Session:** driver `database`, lifetime 120 menit, tidak dienkripsi.
- **Authorization / role / permission:** **BELUM ADA**, sengaja. Tidak ada Policy/Gate/Spatie. Semua user login = akses penuh admin.
- **User provisioning:** hanya via seeder `DatabaseSeeder` (`test@example.com`). Belum ada registrasi user admin publik.

---

## 10. Current Feature Status

**✅ Done**

- Login (auth session nyata, rate-limited) + Logout (tersentralisasi di layout).
- Proteksi route admin (`auth` middleware di semua `admin.*`, guest → login).
- Landing page publik + modal pendaftaran (frontend).
- Pendaftaran murid publik (form → validasi → normalisasi WA → simpan DB → JSON response).
- Referral Agent **backend + UI admin** (model, migration termasuk kolom `email`, `ReferralAgentService`, cookie capture & resolve, generator kode unik, `ReferralAgentController` CRUD + toggle status). Capture referral jalan di `/` dan `/daftar`, param URL `?share_via=KODE`. Migration sudah dijalankan di lokal.
- Visitor logging (`LogVisitor` + `visitor_logs`).
- Key-value settings (`admin_settings` + `AdminSetting::get()`).

**🚧 In Progress**

- Modul admin Murid (view `admin/murid` ada, **belum** controller/CRUD/data).
- Halaman admin lain (guru, jadwal, paket, transaksi, laporan, pengaturan) = view statis, menunggu backend.

**📋 Planned**

- Kode referral custom "vanity string" (prioritas rendah).
- Modul pembayaran (manual bulanan; Midtrans disebut di `decisions.md` tapi belum di kode).

**❌ Not Started**

- Reporting, Notifikasi (WA/email), integrasi Zoom, role/permission system, CRUD Guru/Jadwal/Paket, export/import Excel, audit log.

---

## 11. Last Completed Feature

- **Nama fitur:** Admin Referral Agent (UI admin + penyesuaian backend Referral), termasuk fix pasca-review.
- **Tanggal:** 2026-07-13.
- **Perubahan utama:**
    - Menu sidebar + halaman `admin/referral-agent` (list, tambah/edit via modal, toggle Aktif/Nonaktif, copy kode & link referral).
    - Migration baru: kolom `email` (unique) di `referral_agents` (disiapkan untuk login Agent nanti, belum diimplementasikan) + unique index `whatsapp`.
    - `ReferralAgentService`: tambah konstanta `QUERY_PARAM = 'share_via'` (ganti literal `'ref'`), method baru `buildReferralLink()`, `createAgent()` (auto kode 12 karakter via `generateUniqueCode(12)`), `toggleStatus()`.
    - Capture referral sekarang jalan juga di route `/` (root), tidak cuma `/daftar` — supaya link `domain.com/?share_via=KODE` yang di-copy dari admin beneran ke-track.
    - `ReferralAgentController` (index/store/update/toggleStatus) + `StoreReferralAgentRequest`/`UpdateReferralAgentRequest`.
    - **Fix pasca-review (sama hari):** (1) Tombol Copy Kode/Link ditambah fallback `execCommand('copy')` via textarea sementara — `navigator.clipboard` gagal diam-diam di non-secure context (`http://ngaji-nusa.test` lokal); di VPS dengan HTTPS otomatis pakai clipboard API asli, gak perlu ubah apa-apa lagi. (2) `generateUniqueCode()` diubah dari `strtoupper()` jadi `strtolower()` — kode referral sekarang huruf kecil+angka, biar gak terlalu "ketara" di URL. (3) `layouts/app.blade.php` dapat script kecil yang membersihkan `?share_via=KODE` dari address bar via `history.replaceState()` setelah cookie ke-capture di server — murni kosmetik URL, cookie tetap tersimpan.
- **File utama yang berubah:** migration baru `add_email_and_unique_constraints_to_referral_agents_table`, `app/Models/ReferralAgent.php`, `app/Services/ReferralAgentService.php`, `app/Http/Controllers/ReferralAgentController.php`, `app/Http/Requests/StoreReferralAgentRequest.php`, `app/Http/Requests/UpdateReferralAgentRequest.php`, `routes/web.php`, `resources/views/admin/referral-agent.blade.php` (baru), `resources/views/layouts/admin.blade.php`, `resources/views/layouts/app.blade.php`, `public/css/admin-referral-agent.css` (baru).
- **Dampak:** Fitur Referral Agent sekarang punya UI admin lengkap dan sudah di-test manual oleh owner (copy, generate kode, URL cleanup — semua oke). Query param URL referral berubah dari `?ref=` ke `?share_via=` — link lama berformat `?ref=` berhenti berfungsi (kemungkinan belum ada yang beredar karena UI admin belum pernah ada sebelumnya). **Migration sudah dijalankan manual oleh owner di lokal** — beres.

---

## 12. Next Development Priority

1. **Modul admin Murid nyata** — `MuridController` untuk index (list + `paginate`, eager load `referralAgent`), detail, ubah status. Ganti view mock jadi data DB.
2. **Wire `AdminSettingSeeder`** ke `DatabaseSeeder::run()` supaya `wa_admin_number` ter-seed.
3. **Keputusan status Murid** — definisikan set status lengkap (Aktif/Pending/Nonaktif/…) bersama owner sebelum bangun logika status.
4. **Desain modul pembayaran manual** (verifikasi bulanan, reminder) — sebelum menyentuh integrasi apa pun.
5. **Kode referral vanity string** (prioritas rendah).

---

## 13. Known Issues / Technical Debt

- **`AdminSettingSeeder` belum dipanggil** dari `DatabaseSeeder::run()` → `php artisan db:seed` tidak akan mengisi `wa_admin_number`. `MuridController::store()` mengandalkan setting ini (fallback `null` jika kosong).
- **`StorePostRequest` orphan** — ada di `app/Http/Requests`, `authorize()` return `false`, `rules()` kosong, tidak dipakai controller mana pun. Status tidak jelas → **jangan hapus / jangan bangun di atasnya** tanpa tanya.
- **Route admin masih closure** return view statis untuk sebagian besar halaman — kecuali `admin.referral-agent.*` yang sudah pakai controller nyata.
- **Belum ada test nyata** — hanya `ExampleTest` scaffolding di `tests/Feature` & `tests/Unit`.
- **`AppServiceProvider` kosong** — `register()`/`boot()` belum dipakai.
- **Inkonsistensi versi PHP** — CLAUDE.md/CLAUDE bilang PHP 8.5, `composer.json` pin `^8.3`.
- **Doc legacy stale** — `docs/features.md`, `docs/current_state.md`, `docs/database.md` dll sebagian aspiratif/usang. File ini (`ai-context.md`) adalah successor terverifikasi.
- **Status Murid belum lengkap** — hanya `'Daftar'`; nilai lain disebut di komentar tapi belum ada.
- **`[OPEN]` tujuan visitor logging** selain analitik dasar.
- **`[OPEN]` mekanisme delivery akses Zoom** setelah pembayaran — belum didesain.
- **Harga paket hanya di frontend** (home.blade), tidak ada sumber backend.

---

## 14. Important Files

| File                                       | Fungsi                                                                                                       |
| ------------------------------------------ | ------------------------------------------------------------------------------------------------------------ |
| `routes/web.php`                           | Semua definisi route (publik, auth, admin group)                                                             |
| `bootstrap/app.php`                        | Registrasi middleware global (`LogVisitor`), `trustProxies` Cloudflare, JSON exception `api/*`, health `/up` |
| `app/Http/Controllers/MuridController.php` | Landing + capture referral (`create`), simpan pendaftaran + normalisasi WA (`store`)                         |
| `app/Http/Controllers/AuthController.php`  | Login form/store, logout                                                                                     |
| `app/Http/Controllers/ReferralAgentController.php` | CRUD Referral Agent admin (index/store/update/toggleStatus)                                            |
| `app/Http/Requests/StoreMuridRequest.php`  | Validasi pendaftaran, pesan ID, error selalu JSON 422                                                        |
| `app/Http/Requests/LoginRequest.php`       | Validasi + rate-limit + `authenticate()` login                                                               |
| `app/Http/Requests/StoreReferralAgentRequest.php` / `UpdateReferralAgentRequest.php` | Validasi admin Referral Agent (email/whatsapp unik, status) |
| `app/Services/ReferralAgentService.php`    | Capture/resolve referral cookie, generate kode unik, buildReferralLink, createAgent, toggleStatus            |
| `app/Http/Middleware/LogVisitor.php`       | Catat kunjungan non-admin ke `visitor_logs`                                                                  |
| `app/Models/Murid.php`                     | Model inti + konstanta `LEVEL_OPTIONS`/`PAKET_OPTIONS`/`STATUS_DAFTAR`                                       |
| `app/Models/ReferralAgent.php`             | Model agen + konstanta status/STATUS_OPTIONS + relasi `murid()`                                              |
| `app/Models/AdminSetting.php`              | Key-value settings + helper `get()`                                                                          |
| `app/Models/VisitorLog.php`                | Model log kunjungan                                                                                          |
| `resources/views/pages/home.blade.php`     | Landing page + modal pendaftaran + JS `fetch(/daftar)`                                                       |
| `resources/views/admin/referral-agent.blade.php` | List + modal tambah/edit Referral Agent                                                                |
| `resources/views/layouts/admin.blade.php`  | Shell admin + form logout tersentralisasi + menu Referral Agent                                              |
| `resources/views/layouts/app.blade.php`    | Shell publik + script cleanup URL `?share_via=`                                                              |
| `database/seeders/AdminSettingSeeder.php`  | Seed `wa_admin_number` (belum di-wire)                                                                       |
| `CLAUDE.md`                                | Instruksi wajib AI — jangan diedit tanpa diminta                                                             |
| `PROJECT_MEMORY.md`                        | Audit AI sebelumnya — jangan ditimpa                                                                         |

---

## 15. External Integration

| Integrasi               | Status                    | Bukti                                                                                                                                              |
| ----------------------- | ------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Midtrans** (payment)  | ❌ Planned, belum di kode | Disebut `docs/decisions.md`; tidak ada SDK/config/controller                                                                                       |
| **Zoom API**            | ❌ Placeholder/mock       | Hanya tombol "Konfigurasi/Putuskan" (toast) di `admin/pengaturan.blade.php`                                                                        |
| **WhatsApp Notifikasi** | ❌ Mock UI                | Toggle + template teks di `admin/pengaturan` — tidak ada API. WA dipakai nyata hanya sebagai **link `wa.me` manual** (nomor dari `admin_settings`) |
| **Laravel Breeze**      | ❌ Tidak dipakai          | Auth hand-rolled                                                                                                                                   |
| **Mail**                | ⚠️ Driver `log`           | Email hanya masuk log, tidak terkirim. Stub `config/services.php` (Postmark/Resend/SES/Slack) tanpa kredensial                                     |
| **Cloudflare**          | ✅ Aktif (infra)          | `trustProxies` IP range di `bootstrap/app.php`                                                                                                     |

**Kesimpulan:** tidak ada integrasi pihak ketiga yang benar-benar mengirim/menerima data. Semua "integrasi" di UI admin = dummy.

---

## 16. Development Workflow

**Git:** branch-per-fitur. Branch yang ada: `main`, `dev`, `feature/login`, `feature/registrasi`, `feature/sistem-referral`. Commit style **Conventional Commits** berbahasa Indonesia, contoh: `feat(auth): proteksi route admin dengan middleware auth, benerin tombol logout`, `feat(referral): tambah kolom referral_agent_id (FK) ke tabel murid`. Prefix dipakai: `feat(scope):`.

**Membuat fitur:** pahami request → tanya bila ambigu → jelaskan rencana singkat → ubah **hanya file yang diminta** → ikuti best practice Laravel → self-review → sebut potensi improvement terpisah (`docs/ai_workflow.md`).

**Setup lokal:**

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
```

**Dev (server+queue+log+vite sekaligus):** `composer run dev`
**Test:** `composer run test` / `php artisan test`
**Code style:** `vendor/bin/pint`
**Produksi:** `php artisan optimize && php artisan config:cache && php artisan route:cache && php artisan view:cache`

**Debugging:** `APP_DEBUG=true` lokal; log via `laravel/pail` (`php artisan pail`); mail driver `log`.

**Wrap-up tiap fitur selesai:** ikuti SOP di `docs/workflow-wrapup.md` (analisis perubahan, update dokumentasi terdampak, timestamp presisi detik, pembagian commit per Logical Change, format Conventional Commit, identifikasi manual task, format laporan baku). Kirim hanya file yang berubah (aturan owner). Jika revisi minor 1 file, cukup tampilkan potongan script yang diubah — tidak perlu zip ulang.

---

## 17. Documentation Map (urutan otoritas bila konflik)

1. **Kode nyata** — sumber kebenaran tertinggi. Jika dokumen ≠ kode, kode menang, lalu perbarui dokumen.
2. **`docs/ai-context.md`** (file ini) — SSOT dokumentasi. Menang atas semua `docs/*.md` lain & `CLAUDE.md` untuk deskripsi kondisi project.
3. **`CLAUDE.md`** — aturan & guardrail untuk AI (arsitektur, output rules). Menang untuk "aturan main", bukan untuk "kondisi project". Jangan diedit tanpa diminta.
4. **`docs/workflow-wrapup.md`** — SOP operasional proses wrap-up (rincian dari poin "Wrap-up tiap fitur selesai" di §16). Menang untuk *cara eksekusi* wrap-up.
5. **`PROJECT_MEMORY.md`** — audit AI lama; sebagian sudah usang (mis. klaim Breeze, klaim SQLite lokal, klaim "Role Management done"). Jangan dijadikan acuan status; jangan ditimpa.
6. **`docs/*.md` legacy** (`architecture, conventions, coding_style, database, decisions, deployment, features, current_state, changelog, ai_workflow, todo`) — ringkas/aspiratif, beberapa stale. Rujuk hanya bila selaras dengan file ini.

> Koreksi penting yang sudah dibereskan di file ini: (a) DB lokal **MySQL/MariaDB**, bukan SQLite; (b) Auth = built-in facade, **bukan Breeze**; (c) "Role Management" **belum ada** (bertentangan dengan `current_state.md`); (d) harga paket hanya frontend.

---

## 18. Changelog Ringkas (histori fitur, bukan histori commit)

| Tanggal    | Fitur                     | Ringkasan                                                                                                                      | Dampak                                                                   |
| ---------- | ------------------------- | ------------------------------------------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------ |
| 2026-07-13 | docs: SOP Wrap-up Workflow | Tambah `docs/workflow-wrapup.md` — SOP wajib proses wrap-up (analisis perubahan, update dokumentasi, pembagian commit per Logical Change, format laporan baku). Dirujuk dari §16 | Proses wrap-up ke depan lebih terstandar & konsisten; tidak mengubah kode/fitur apa pun |
| 2026-07-13 | Admin Referral Agent (UI) | Halaman & menu admin `admin/referral-agent` (list, tambah/edit modal, toggle status, copy kode & link). Kolom `email` (unique) ditambah ke `referral_agents`. Param URL referral `?ref=` → `?share_via=` via konstanta. Capture referral jalan juga di `/`. Fix pasca-review: copy pakai fallback `execCommand`, kode referral huruf kecil, URL `?share_via=` dibersihin otomatis pakai `history.replaceState()` | UI Referral Agent lengkap & sudah ditest owner. Link lama format `?ref=` berhenti kerja (belum pernah dipakai publik). Migration sudah dijalankan manual oleh owner |
| 2026-07-13 | Admin Area Authentication | Semua `admin.*` dilindungi `auth`; hapus duplikat `GET /login`; logout disentralisasi ke layout (submit `POST /logout` nyata)  | Area admin tertutup guest; logout fungsional. Belum ada role             |
| 2026-07-12 | Sistem Referral (backend) | `referral_agents` + FK `murid.referral_agent_id`, `ReferralAgentService` (capture/resolve cookie, generate kode), relasi model | Pendaftaran bisa terasosiasi ke agen via `?ref=KODE`. UI admin belum ada |
| 2026-07-12 | Login (auth nyata)        | `AuthController` + `LoginRequest` (rate-limit 5/menit), session regenerate                                                     | Admin bisa login/logout via session                                      |
| 2026-07-12 | Pendaftaran Murid         | `POST /daftar`, `StoreMuridRequest`, normalisasi WA, `admin_settings`, throttle 10/menit                                       | Form publik menyimpan murid ke DB + return JSON                          |
| 2026-07-10 | Visitor Logging           | `LogVisitor` + `visitor_logs`                                                                                                  | Analitik kunjungan dasar per tanggal/path/IP                             |
| v1.0       | Initial Project           | Skeleton Laravel 13                                                                                                            | —                                                                        |

_Setiap fitur baru selesai: tambahkan baris di sini + perbarui §11._

---

## 19. AI Notes (khusus AI assistant)

**Jangan dilakukan:**

- Jangan bangun **auto-billing/auto-charge** — pembayaran manual bulanan.
- Jangan tambah **Repository pattern**, jangan pilih **role/permission system** (Spatie vs custom) — keduanya keputusan tertunda.
- Jangan **refactor logika controller ke Service secara spekulatif** (mis. `normalizeWhatsapp`) tanpa diminta.
- Jangan **rename** method/route/model/kolom/tabel tanpa izin.
- Jangan **ubah schema** `murid`/`admin_settings`/`visitor_logs`/`referral_agents`/`users` tanpa diminta.
- Jangan **terjemahkan** istilah domain Indonesia ke Inggris.
- Jangan **hapus/bangun di atas** `StorePostRequest` (status tidak jelas).
- Jangan **mengarang nilai enum** (status Murid) — konfirmasi owner.
- Jangan **asumsikan** integrasi (Midtrans/Zoom/WA API) sudah aktif — semuanya mock.

**File sensitif / jangan diubah tanpa izin:** `CLAUDE.md`, `PROJECT_MEMORY.md`, migration existing, nama route/method/model, `.env`.

**Keputusan arsitektur (final kecuali owner ubah):** Service layer dipakai; No Repository; Blade (bukan Livewire); Bootstrap 5; Auth built-in facade (bukan Breeze); Midtrans direncanakan (belum di kode).

**Preferensi coding project:** PSR-12, Form Request untuk validasi, Eloquent + eager load (cegah N+1), controller tipis, early return, DI > static, konstanta enum di model.

**Preferensi owner:** komunikasi santai-akrab (rekan sejawat, "gw/elu"), namun tetap profesional/senior. Kirim **hanya file terdampak** saat revisi; revisi minor 1 file cukup tampilkan potongan kode yang diubah, tanpa zip ulang, tanpa unduhan file terbaru kecuali diminta. Catat setiap progres selesai agar bisa diakses lintas obrolan (di file ini).

**Pola implementasi wajib diikuti:**

- Endpoint publik → Form Request → (Service bila perlu) → Model → response JSON untuk `/daftar`.
- Opsi valid (level/paket/status) selalu dari konstanta model, dipakai ulang di validasi.
- Setting global lewat `admin_settings` + `AdminSetting::get()`.
- Referral tidak pernah input manual — selalu via cookie.

---

## 20. Context Summary (baca ini saja untuk paham cepat)

1. NgajiNusa = platform pendaftaran kelas ngaji online (via Zoom) untuk umum.
2. Stack: Laravel 13, PHP (target 8.5, composer pin ^8.3), **MySQL/MariaDB** (lokal Laragon, DB `ngaji-nusa`).
3. Frontend: Blade + Bootstrap 5 + vanilla JS. Tailwind ada sebagai dev tool tapi UI pakai CSS custom.
4. File SQLite ada tapi **tidak dipakai** — koneksi aktif MySQL.
5. Arsitektur: Route → Controller → Service → Model. **No Repository**.
6. Hanya ada 1 Service: `ReferralAgentService`. Normalisasi WA masih di controller (belum dipindah, dibiarkan).
7. Validasi selalu via Form Request; controller tipis.
8. Tabel inti: `murid` (singular), `referral_agents`, `admin_settings`, `visitor_logs`, `users`.
9. `Murid` status baru selalu `'Daftar'` — satu-satunya status yang ada.
10. Level: Hijaiyah/Iqra/Tahsin/Tajwid/Hafalan. Paket: Basic/Pro/Premium/Platinum.
11. Harga paket hanya teks di frontend, **tidak ada di DB**.
12. Nomor WA disimpan format `62xxxx`; dinormalisasi saat store.
13. Pendaftaran publik `POST /daftar` → validasi → simpan → **JSON** (bukan redirect), throttle 10/menit.
14. Referral: `?share_via=KODE` (param URL, konstanta `ReferralAgentService::QUERY_PARAM`) → cookie 30 hari → auto-resolve `referral_agent_id` saat daftar. Capture jalan di `/` & `/daftar`. **UI admin sudah ada** (`admin/referral-agent`, sejak 2026-07-13).
15. Auth: built-in Laravel session (**bukan Breeze**), login rate-limit 5/menit, redirect ke `admin.dashboard`.
16. Semua route `admin.*` dilindungi middleware `auth`; guest → login. **Tidak ada role/permission**.
17. Halaman admin (dashboard, murid, guru, jadwal, paket, transaksi, laporan, pengaturan) masih **view mock statis** — kecuali Referral Agent yang sudah pakai controller nyata.
18. Logout tersentralisasi di `layouts/admin.blade.php` (sebelumnya mock, sudah diperbaiki 2026-07-13).
19. `LogVisitor` mencatat kunjungan GET non-admin ke `visitor_logs` (dedup tanggal+path+IP, increment hit).
20. Settings global = key-value `admin_settings`, akses `AdminSetting::get()`. `wa_admin_number` dipakai di response pendaftaran.
21. `AdminSettingSeeder` **belum di-wire** ke `DatabaseSeeder` → `wa_admin_number` tidak ter-seed otomatis.
22. Tidak ada integrasi eksternal aktif: Midtrans/Zoom/WA API semua **placeholder/mock**. Mail driver `log`.
23. Pembayaran = **manual bulanan**, semi-subscription. Jangan bangun auto-billing.
24. `StorePostRequest` orphan (authorize false, rules kosong) — jangan disentuh tanpa izin.
25. Belum ada test nyata (hanya `ExampleTest`).
26. Git: branch-per-fitur, Conventional Commits bahasa Indonesia (`feat(scope):`).
27. App di belakang Cloudflare (`trustProxies`). Health check `/up` aktif.
28. Session/cache/queue driver = `database`; timezone `Asia/Jakarta`; locale `en`.
29. Keputusan tertunda: set status Murid lengkap, role/permission system, workflow pembayaran, delivery Zoom.
30. Prioritas berikutnya: modul Murid admin nyata → wire seeder → definisi status Murid.
31. SSOT dokumentasi = file ini; kalau konflik, **kode menang**, lalu perbarui file ini.
32. Guardrail utama: no rename, no schema change tanpa izin, no terjemah istilah domain, no refactor spekulatif, no auto-billing.
33. Owner = solo dev, komunikasi santai-akrab tapi profesional; kirim hanya file terdampak saat revisi.
34. Fitur terakhir selesai: Admin Referral Agent — UI admin + `email` di `referral_agents` + param `?share_via=` (2026-07-13), migration sudah dijalankan.

## Server Specs

- VPS: MS 4.2, Biznet Gio
- OS: Ubuntu 26.04
- Resource: 2 vCPU, 4GB RAM, 60GB SSD
- Stack: Nginx, PHP 8.3-FPM, MariaDB

---

_Perbarui file ini setiap kali arsitektur, struktur folder, business rule, integrasi, atau status fitur berubah — terutama §11 (Last Completed Feature) dan §18 (Changelog) setiap fitur selesai._
