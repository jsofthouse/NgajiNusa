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
  admin/                      dashboard, guru, jadwal, paket, laporan, pengaturan (view mock/statis); murid & transaksi (CRUD nyata, lihat §11)
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
| deleted_at | timestamp nullable | Soft delete (migration `add_soft_deletes_to_murid_table`, 2026-07-13). Model `Murid` pakai trait `SoftDeletes` |
| timestamps | | |

Index: `status`, `email`. Fillable termasuk semua kolom di atas + `referral_agent_id`.

**`referral_agents`**
`id, nama, email(150, unique), whatsapp(20, unique), kode(50, unique), status(20) default 'Aktif' (index), timestamps`. Relasi `hasMany(Murid)`. Kolom `email` disiapkan untuk fitur login Agent di masa depan (belum diimplementasikan).

**`admin_settings`** (key-value store)
`id, key(100, unique), value(255), timestamps`. Diakses via `AdminSetting::get($key, $default)`.

**`transaksi`** (transaksi pembayaran pendaftaran, terpisah dari `murid` — sengaja, requirement eksplisit)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint PK | |
| invoice_number | string(20), unique, nullable | Format `INV-00000001`, di-generate otomatis dari `id` setelah insert (`Transaksi::booted()`, event `created` + `saveQuietly()`) |
| murid_id | bigint FK | → `murid.id`, `cascadeOnDelete()` |
| jenis | string(30) | Default `pendaftaran_baru` (satu-satunya dipakai fase ini). `perpanjangan`/`upgrade_paket` disiapkan sbg dokumentasi, **belum diimplementasikan** |
| paket | string(50) | Snapshot nama paket saat transaksi dibuat (independen dari `murid.paket` kalau berubah nanti) |
| nominal | unsignedBigInteger | Rupiah tanpa desimal. Diisi otomatis dari mapping harga di `TransaksiService::PAKET_PRICES` (Basic 300rb/Pro 550rb/Premium 800rb/Platinum 1,2jt — sesuai landing page, dikonfirmasi owner 2026-07-13 karena belum ada tabel harga backend) |
| metode_pembayaran | string(30) | Default `transfer_manual` (satu-satunya dipakai fase ini). `midtrans`/`xendit`/`qris` disiapkan sbg dokumentasi, **belum diimplementasikan** |
| status | string(30) | `menunggu_pembayaran` (default) / `menunggu_verifikasi` / `berhasil` / `ditolak` |
| bukti_original_filename, bukti_stored_filename, bukti_mime_type, bukti_file_size, bukti_path | nullable | Metadata bukti transfer, diisi saat verifikasi. File fisik disimpan di disk `local` (`storage/app/private/payment-proofs/{tahun}/{bulan}/{uuid}.{ext}`), nama file UUID (bukan nama murid/invoice) |
| catatan_internal | text, nullable | Cuma admin yang bisa lihat, beda dari histori aktivitas, bisa diedit kapan saja |
| opened_at | timestamp, nullable | Kapan admin pertama kali buka Detail Transaksi (dipakai indikator "transaksi baru") |
| verified_at | timestamp, nullable | |
| verified_by | bigint FK nullable | → `users.id`, `nullOnDelete()` |
| gateway_provider, gateway_transaction_id, gateway_payload | nullable | **Future-proof, belum dipakai sama sekali** — placeholder integrasi payment gateway |
| timestamps | | |

Index: `status`, `murid_id`. Model `Transaksi` juga menyimpan konstanta `STATUS_OPTIONS`/`STATUS_LABELS`, `METODE_OPTIONS`/`METODE_LABELS`, `JENIS_OPTIONS`.

**`transaksi_activities`** (audit trail transaksi, readonly)
`id, transaksi_id (FK → transaksi, cascadeOnDelete), type (string(50), free-form: created/opened/note_updated/proof_uploaded/verified/rejected), description (string, teks Indonesia siap tampil), causer_id (FK → users.id nullable, null = sistem), metadata (json nullable), created_at (tanpa updated_at — immutable)`. Model `TransaksiActivity` set `$timestamps = false`.

**`visitor_logs`**
`id, visit_date(date, index), path(255), ip_address(45, nullable), hit_count(unsigned int, default 1), timestamps`. **Unique** pada `(visit_date, path, ip_address)`. Ditulis oleh `LogVisitor` (increment `hit_count`).

Plus tabel default framework: `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`.

### Enum / konstanta penting (di Model, bukan DB enum)

- `Murid::LEVEL_OPTIONS` = `['Hijaiyah', 'Iqra', 'Tahsin', 'Tajwid', 'Hafalan']`
- `Murid::PAKET_OPTIONS` = `['Basic', 'Pro', 'Premium', 'Platinum']`
- `Murid::STATUS_DAFTAR` = `'Daftar'`, `Murid::STATUS_AKTIF` = `'Aktif'` (ditambah 2026-07-13 bersamaan modul Transaksi, dikonfirmasi owner — auto-set saat `TransaksiService::verifyPayment()` berhasil). Status lain (Pending, Nonaktif, dst) masih **belum ada** → jangan mengarang nilai status baru tanpa konfirmasi owner.
- `ReferralAgent::STATUS_ACTIVE` = `'Aktif'`, `ReferralAgent::STATUS_INACTIVE` = `'Nonaktif'`, `ReferralAgent::STATUS_OPTIONS` = `[STATUS_ACTIVE, STATUS_INACTIVE]` (dipakai ulang di Form Request).

### Relasi

- `Murid belongsTo ReferralAgent` (`referral_agent_id`, nullable).
- `ReferralAgent hasMany Murid` (via method `murid()`).
- `Murid hasMany Transaksi` (via method `transaksi()`). `Transaksi belongsTo Murid`, `Transaksi belongsTo User` (via `verifiedBy()`), `Transaksi hasMany TransaksiActivity` (via `activities()`, `latest('created_at')`).

### Business constraint

- `referral_agents.kode` unik, `email` unik, `whatsapp` unik. FK `murid.referral_agent_id` set null bila agen dihapus.
- `visitor_logs` unik per `(tanggal, path, ip)` → 1 IP menambah `hit_count`, bukan baris baru.
- `admin_settings.key` unik.
- `transaksi.murid_id` cascade delete (transaksi ikut terhapus kalau murid dihapus permanen — soft delete murid tidak memicu ini). `transaksi.invoice_number` unik (nullable sesaat sebelum di-generate).

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
15. **Transaksi otomatis (baru, 2026-07-13):** setiap `POST /daftar` (pendaftaran publik) otomatis bikin 1 baris `Transaksi` via `TransaksiService::createFromMurid()` — status awal `menunggu_pembayaran`, nominal dari mapping harga paket, metode default `transfer_manual`. **Cuma jalan di flow publik** — murid yang ditambah admin manual dari Manajemen Murid *belum* dapat transaksi otomatis (lihat `docs/todo.md`).
16. **Verifikasi pembayaran:** admin upload ulang bukti transfer via modal Verifikasi (`AdminTransaksiController@verify`) → status jadi `berhasil`, `verified_at`/`verified_by` tercatat, **murid otomatis jadi `Murid::STATUS_AKTIF`**. Admin juga bisa **menolak** transaksi (`@reject`) → status `ditolak` (aksi simetris, gak ada di requirement awal tapi dibutuhkan supaya tab filter "Ditolak" bisa tercapai).
17. **Indikator transaksi baru:** kolom `opened_at` null = belum pernah dibuka admin (tampil bulatan merah di invoice). Otomatis ke-set begitu admin buka Detail Transaksi pertama kali (`TransaksiService::markOpened()`), dicatat juga di histori aktivitas.
18. **Catatan internal admin** (`transaksi.catatan_internal`) bisa diedit kapan saja dari Detail Transaksi, terpisah dari histori aktivitas (audit trail readonly di `transaksi_activities`).

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
| GET    | `admin/laporan`    | `admin.laporan`    | closure → view           | **auth**        | Mock                                   |
| GET    | `admin/pengaturan` | `admin.pengaturan` | closure → view           | **auth**        | Mock (referensi WA/Zoom placeholder)   |
| GET    | `admin/murid/export` | `admin.murid.export` | `AdminMuridController@export` | **auth** | Export seluruh data murid ke CSV (tanpa filter) |
| GET    | `admin/murid`      | `admin.murid.index`| `AdminMuridController@index` | **auth**   | List murid (pagination + search, AJAX-aware via `expectsJson()`) |
| POST   | `admin/murid`      | `admin.murid.store`| `AdminMuridController@store` | **auth**   | Tambah murid (AJAX/JSON)                |
| GET    | `admin/murid/{murid}` | `admin.murid.show` | `AdminMuridController@show` | **auth**  | Detail murid (JSON, dipakai modal detail) |
| PUT    | `admin/murid/{murid}` | `admin.murid.update` | `AdminMuridController@update` | **auth** | Edit murid (AJAX/JSON)                  |
| DELETE | `admin/murid/{murid}` | `admin.murid.destroy` | `AdminMuridController@destroy` | **auth** | Soft delete murid (AJAX/JSON)            |
| GET    | `admin/guru`       | `admin.guru`       | closure → view           | **auth**        | Mock                                   |
| GET    | `admin/jadwal`     | `admin.jadwal`     | closure → view           | **auth**        | Mock                                   |
| GET    | `admin/paket`      | `admin.paket`      | closure → view           | **auth**        | Mock                                   |
| GET    | `admin/referral-agent` | `admin.referral-agent.index` | `ReferralAgentController@index` | **auth** | List Referral Agent |
| POST   | `admin/referral-agent` | `admin.referral-agent.store` | `ReferralAgentController@store` | **auth** | Tambah Referral Agent |
| PUT    | `admin/referral-agent/{referralAgent}` | `admin.referral-agent.update` | `ReferralAgentController@update` | **auth** | Edit Referral Agent |
| PATCH  | `admin/referral-agent/{referralAgent}/toggle-status` | `admin.referral-agent.toggle-status` | `ReferralAgentController@toggleStatus` | **auth** | Toggle Aktif/Nonaktif |
| GET    | `admin/transaksi` | `admin.transaksi.index` | `AdminTransaksiController@index` | **auth** | List transaksi (filter status/paket/metode/tanggal + search + pagination, dashboard summary, AJAX-aware) |
| GET    | `admin/transaksi/{transaksi}` | `admin.transaksi.show` | `AdminTransaksiController@show` | **auth** | Detail transaksi (JSON) — otomatis `markOpened()` |
| POST   | `admin/transaksi/{transaksi}/verify` | `admin.transaksi.verify` | `AdminTransaksiController@verify` | **auth** | Verifikasi pembayaran (multipart: bukti_transfer + catatan_internal) |
| POST   | `admin/transaksi/{transaksi}/reject` | `admin.transaksi.reject` | `AdminTransaksiController@reject` | **auth** | Tolak transaksi |
| PATCH  | `admin/transaksi/{transaksi}/catatan` | `admin.transaksi.catatan` | `AdminTransaksiController@updateCatatan` | **auth** | Update catatan internal admin |
| GET    | `admin/transaksi/{transaksi}/bukti-transfer` | `admin.transaksi.bukti.preview` | `AdminTransaksiController@previewBukti` | **auth** | Stream bukti transfer inline (preview/"Lihat") |
| GET    | `admin/transaksi/{transaksi}/bukti-transfer/download` | `admin.transaksi.bukti.download` | `AdminTransaksiController@downloadBukti` | **auth** | Download bukti transfer (attachment, nama file asli) |

**Catatan:** sebagian besar route admin masih **closure** return view statis — kecuali `admin.referral-agent.*`, `admin.murid.*`, dan `admin.transaksi.*` yang sudah pakai controller nyata. Route name `admin.murid` (lama, closure) berubah jadi `admin.murid.index`, dan `admin.transaksi` (lama, closure) berubah jadi `admin.transaksi.index` — konsekuensi RESTful CRUD, sidebar & `section-tabs` partial sudah disesuaikan di masing-masing. `admin/murid/export` didaftarkan **sebelum** `admin/murid/{murid}` supaya tidak ketangkep sebagai route model binding. Health check `/up` aktif (dari `bootstrap/app.php`). Exception di-render JSON hanya untuk `api/*` (belum ada route api) — konsekuensinya endpoint admin/murid/* dan admin/transaksi/* yang error di luar validasi (404/500) balikin HTML, bukan JSON; ditangani di JS via generic catch/toast.

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
- **Admin Murid (CRUD nyata, AJAX)** — list (pagination + search server-side + total + empty state), tambah/edit via modal (validasi Form Request, normalisasi WA identik pendaftaran publik), detail via modal (fetch JSON, style sama Referral Agent), soft delete + toast + reload async, export CSV seluruh data. `AdminMuridController`, `MuridService`, `StoreAdminMuridRequest`/`UpdateAdminMuridRequest`. Migration `add_soft_deletes_to_murid_table` **belum dijalankan di lokal** (lihat §11).
- Visitor logging (`LogVisitor` + `visitor_logs`).
- Key-value settings (`admin_settings` + `AdminSetting::get()`).
- **Admin Transaksi / Manajemen Transaksi (CRUD nyata, AJAX)** — verifikasi manual pembayaran transfer bank. Auto-create transaksi saat pendaftaran publik, dashboard summary (4 kartu), tab filter status + filter bar (search/paket/metode/rentang tanggal), indikator transaksi belum dibuka, modal Detail (info murid+transaksi, catatan internal editable, bukti transfer preview/lihat/download, histori aktivitas/audit trail), modal Verifikasi (upload bukti transfer wajib + catatan opsional → status Berhasil + murid otomatis Aktif), aksi Tolak. Skema future-proof utk payment gateway (belum diimplementasikan). Lihat §11 untuk detail lengkap.

**🚧 In Progress**

- Halaman admin lain (guru, jadwal, paket, laporan, pengaturan) = view statis, menunggu backend.

**📋 Planned**

- Kode referral custom "vanity string" (prioritas rendah).
- Integrasi payment gateway (Midtrans/Xendit/dll) — skema DB `transaksi` sudah future-proof (`gateway_provider`/`gateway_transaction_id`/`gateway_payload`), tapi belum ada SDK/config/controller apa pun.
- Auto-create transaksi utk murid yang ditambah admin manual (lihat `docs/todo.md`).

**❌ Not Started**

- Reporting, Notifikasi (WA/email), integrasi Zoom, role/permission system, CRUD Guru/Jadwal/Paket, export/import Excel, audit log.

---

## 11. Last Completed Feature

- **Nama fitur:** Admin Transaksi (Manajemen Transaksi) — modul baru verifikasi manual pembayaran pendaftaran murid (transfer bank), menggantikan view mock `admin/transaksi.blade.php`.
- **Tanggal:** 2026-07-13.
- **Keputusan owner sebelum implementasi (dikonfirmasi via tanya jawab):**
    1. Murid otomatis jadi `Murid::STATUS_AKTIF` (baru) setelah transaksi diverifikasi berhasil.
    2. Nominal transaksi otomatis diambil dari mapping harga paket sesuai landing page (`TransaksiService::PAKET_PRICES`), bukan input manual admin.
    3. Auto-create transaksi **cuma** di flow pendaftaran publik (`POST /daftar`) — murid yang ditambah admin manual belum dapat transaksi otomatis (dicatat di `docs/todo.md`).
- **Perubahan utama:**
    - **Database:** tabel baru `transaksi` (terpisah dari `murid`, sesuai requirement) & `transaksi_activities` (audit trail). Skema dibuat future-proof utk payment gateway: kolom `jenis`/`metode_pembayaran` sbg string enum (bukan DB enum, gampang tambah nilai baru), kolom `gateway_provider`/`gateway_transaction_id`/`gateway_payload` disiapkan tapi **belum dipakai**. Lihat §6 utk detail kolom lengkap.
    - **Invoice number:** format `INV-00000001`, di-generate otomatis dari `id` setelah insert lewat `Transaksi::booted()` (event `created` + `saveQuietly()` biar gak infinite loop).
    - **Auto-create transaksi:** `TransaksiService::createFromMurid()` dipanggil dari `MuridController@store` (ditambah param `TransaksiService`, **tidak** merefactor logic normalisasi WA yang sudah ada) — status awal `menunggu_pembayaran`.
    - **List & filter:** `AdminTransaksiController@index` — search (invoice/nama murid/nomor WA), filter status/paket/metode/rentang tanggal, pagination, AJAX-aware (`expectsJson()`, pola sama Admin Murid). Dashboard summary (4 kartu: Menunggu Pembayaran, Menunggu Verifikasi, Berhasil, Pendapatan Bulan Ini — dihitung dari `verified_at` bulan berjalan) & badge angka tab filter selalu ikut ke-refresh tiap reload AJAX.
    - **Indikator transaksi baru:** kolom `opened_at` (null = belum dibuka), bulatan merah di kolom Invoice, hilang otomatis (tanpa reload penuh, langsung dimanipulasi via JS) begitu admin buka Detail Transaksi pertama kali (`TransaksiService::markOpened()`).
    - **Detail Transaksi:** modal (bukan halaman baru) — Informasi Murid, Informasi Transaksi, Catatan Internal Admin (textarea + tombol simpan, endpoint `PATCH .../catatan`, terpisah dari alur verifikasi), section Bukti Transfer (preview `<img>` + tombol Lihat/Download, cuma tampil kalau status Berhasil), Histori Aktivitas (timeline readonly dari `transaksi_activities`).
    - **Verifikasi Pembayaran:** modal terpisah (dibuka dari tombol di Detail Transaksi) — readonly Invoice/Nama/Paket/Nominal, form Upload Bukti Transfer (wajib, image jpg/jpeg/png/webp max 2MB — `VerifyTransaksiRequest`) + Catatan Internal (opsional). Sukses → status `berhasil`, `verified_at`/`verified_by` tercatat, **murid auto-Aktif**.
    - **Tolak transaksi:** aksi tambahan (`@reject`, tombol di Detail Transaksi, confirm-based) — **tidak ada di requirement awal**, ditambahkan supaya tab filter "Ditolak" (yang eksplisit diminta) benar-benar bisa tercapai statusnya.
    - **Bukti transfer:** disimpan disk `local` (private, `storage/app/private/payment-proofs/{tahun}/{bulan}/{uuid}.{ext}`), nama file UUID (bukan nama murid/invoice). Preview/download lewat `Storage::response()`/`Storage::download()` (kontrol akses via `auth` middleware, bukan symlink publik).
    - **Konsekuensi routing:** route `admin.transaksi` (closure lama) diganti jadi RESTful `admin.transaksi.index/show/verify/reject/catatan/bukti.preview/bukti.download` — pola sama seperti rename `admin.murid` sebelumnya. Sidebar & `admin/partials/section-tabs.blade.php` disesuaikan.
- **File utama yang dibuat/berubah:** migration `create_transaksi_table` & `create_transaksi_activities_table`, `app/Models/Transaksi.php` & `TransaksiActivity.php` (baru), `app/Models/Murid.php` (tambah `STATUS_AKTIF` + relasi `transaksi()`), `app/Services/TransaksiService.php` (baru), `app/Http/Requests/VerifyTransaksiRequest.php` & `UpdateTransaksiCatatanRequest.php` (baru), `app/Http/Controllers/AdminTransaksiController.php` (baru), `app/Http/Controllers/MuridController.php` (inject `TransaksiService`), `routes/web.php`, `resources/views/admin/transaksi.blade.php` (full rewrite dari mock), `resources/views/admin/partials/transaksi-list.blade.php` (baru), `public/css/admin-transaksi.css`, `resources/views/layouts/admin.blade.php`, `resources/views/admin/partials/section-tabs.blade.php`, `docs/todo.md`.
- **Dampak:** Owner sekarang bisa verifikasi pembayaran transfer manual langsung dari admin (sebelumnya cuma view mock dummy). Migration `create_transaksi_table` & `create_transaksi_activities_table` sudah dijalankan, testing fungsional di lokal sudah oke, commit sudah dibuat, dan **sudah deploy ke VPS produksi & dikonfirmasi jalan aman** (2026-07-13).
- **Known limitation / catatan:** nominal otomatis mengandalkan mapping harga hardcoded (`TransaksiService::PAKET_PRICES`) karena belum ada tabel harga backend — kalau harga di landing page berubah, mapping ini harus di-update manual juga. Payment gateway, QRIS, Virtual Account, multiple bukti transfer, reminder pembayaran, follow up WhatsApp otomatis, nomor referensi bank, dan refund **sengaja belum diimplementasikan** (sesuai scope requirement), tapi skema DB sudah future-proof untuk itu.
- **Revisi 1 (sama hari, pasca-testing owner):** 2 bug fix:
    1. CSS `admin-transaksi.css` ternyata belum punya rule generik `.modal h3` / `.modal p.sub` (modal lama/mock selalu pakai inline style, jadi gak ketahuan) — bikin modal Verifikasi Pembayaran tampil berantakan (judul & subjudul gak ke-style). Ditambahkan, disamakan dengan pola `admin-dashboard.css`.
    2. Link CSS `admin-transaksi.css` ditambah query string versi (`?v={{ filemtime(...) }}`) di `admin/transaksi.blade.php` — cache-busting, supaya browser/Cloudflare gak nyangkut ke versi CSS lama (kemungkinan penyebab indikator titik merah gak muncul saat testing pertama).
    - Sudah diverifikasi ulang: `markOpened()` cuma dipanggil dari `AdminTransaksiController@show`, yang cuma di-hit lewat tombol Detail (bukan pas load halaman list) — kalau titik merah masih belum muncul setelah hard refresh, kemungkinan besar transaksi yang ditest emang udah pernah dibuka sebelumnya, bukan bug.

### Riwayat sebelumnya

- **Admin Murid** (2026-07-13, sesi sebelumnya): CRUD nyata (list+search+pagination, tambah/edit/detail modal, soft delete, export CSV) menggantikan view mock (lihat §18 Changelog untuk detail lengkap — dipindah dari sini supaya §11 hanya menyimpan fitur terakhir).
- **Admin Referral Agent** (2026-07-13, sesi sebelumnya): UI admin + backend Referral (lihat §18 Changelog untuk detail lengkap).

---

## 12. Next Development Priority

1. **Wire `AdminSettingSeeder`** ke `DatabaseSeeder::run()` supaya `wa_admin_number` ter-seed.
2. **Keputusan status Murid** — definisikan set status lengkap (Aktif/Pending/Nonaktif/…) bersama owner sebelum bangun logika status/transisi Daftar → Aktif.
3. **Export Murid berdasarkan filter/search** + **Restore data soft delete** (prioritas tinggi, lihat `docs/todo.md`).
4. **Desain modul pembayaran manual** (verifikasi bulanan, reminder) — sebelum menyentuh integrasi apa pun.
5. **Kode referral vanity string** (prioritas rendah).

---

## 13. Known Issues / Technical Debt

- **`AdminSettingSeeder` belum dipanggil** dari `DatabaseSeeder::run()` → `php artisan db:seed` tidak akan mengisi `wa_admin_number`. `MuridController::store()` mengandalkan setting ini (fallback `null` jika kosong).
- **`StorePostRequest` orphan** — ada di `app/Http/Requests`, `authorize()` return `false`, `rules()` kosong, tidak dipakai controller mana pun. Status tidak jelas → **jangan hapus / jangan bangun di atasnya** tanpa tanya.
- **Route admin masih closure** return view statis untuk sebagian besar halaman — kecuali `admin.referral-agent.*` dan `admin.murid.*` yang sudah pakai controller nyata.
- **Endpoint `admin/murid/*` balikin HTML (bukan JSON)** untuk error di luar validasi (404 model not found, 500, dsb) karena JSON exception rendering di `bootstrap/app.php` cuma di-scope ke `api/*`. Sudah ditangani generik di JS (fallback toast error), tapi pesan errornya jadi generik, bukan detail dari server.
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
| `app/Http/Controllers/AdminMuridController.php` | CRUD Murid admin (index/store/show/update/destroy/export), thin — delegasi ke `MuridService`             |
| `app/Http/Controllers/AdminTransaksiController.php` | CRUD Transaksi admin (index/show/verify/reject/updateCatatan/previewBukti/downloadBukti), thin — delegasi ke `TransaksiService` |
| `app/Http/Requests/StoreMuridRequest.php`  | Validasi pendaftaran, pesan ID, error selalu JSON 422                                                        |
| `app/Http/Requests/LoginRequest.php`       | Validasi + rate-limit + `authenticate()` login                                                               |
| `app/Http/Requests/StoreReferralAgentRequest.php` / `UpdateReferralAgentRequest.php` | Validasi admin Referral Agent (email/whatsapp unik, status) |
| `app/Http/Requests/StoreAdminMuridRequest.php` / `UpdateAdminMuridRequest.php` | Validasi admin Murid (email/whatsapp unik, normalisasi WA di `prepareForValidation()`, error selalu JSON 422) |
| `app/Http/Requests/VerifyTransaksiRequest.php` / `UpdateTransaksiCatatanRequest.php` | Validasi verifikasi pembayaran (bukti_transfer wajib image max 2MB) & update catatan internal, error selalu JSON 422 |
| `app/Services/ReferralAgentService.php`    | Capture/resolve referral cookie, generate kode unik, buildReferralLink, createAgent, toggleStatus            |
| `app/Services/MuridService.php`            | Business logic Murid admin: paginate+search, createMurid, updateMurid, normalizeWhatsapp, allForExport (CSV) |
| `app/Services/TransaksiService.php`        | Business logic Transaksi: createFromMurid (auto invoice+nominal), paginate+filter, dashboardSummary, statusCounts, markOpened, verifyPayment, reject, updateCatatan, logActivity |
| `app/Http/Middleware/LogVisitor.php`       | Catat kunjungan non-admin ke `visitor_logs`                                                                  |
| `app/Models/Murid.php`                     | Model inti + konstanta `LEVEL_OPTIONS`/`PAKET_OPTIONS`/`STATUS_DAFTAR`/`STATUS_AKTIF` (baru) + trait `SoftDeletes` + relasi `transaksi()` |
| `app/Models/ReferralAgent.php`             | Model agen + konstanta status/STATUS_OPTIONS + relasi `murid()`                                              |
| `app/Models/AdminSetting.php`              | Key-value settings + helper `get()`                                                                          |
| `app/Models/VisitorLog.php`                | Model log kunjungan                                                                                          |
| `app/Models/Transaksi.php`                 | Model transaksi + konstanta STATUS/METODE/JENIS OPTIONS & LABELS, `booted()` generate invoice_number, relasi `murid()`/`verifiedBy()`/`activities()` |
| `app/Models/TransaksiActivity.php`         | Model audit trail transaksi (readonly, `$timestamps = false`), relasi `transaksi()`/`causer()`               |
| `resources/views/pages/home.blade.php`     | Landing page + modal pendaftaran + JS `fetch(/daftar)`                                                       |
| `resources/views/admin/referral-agent.blade.php` | List + modal tambah/edit Referral Agent                                                                |
| `resources/views/admin/murid.blade.php`    | List + modal tambah/edit + modal detail Murid, AJAX (search/pagination/reload), toast                        |
| `resources/views/admin/partials/murid-list.blade.php` | Partial tabel+pagination Murid, dipakai render awal & fragment AJAX                                |
| `resources/views/admin/transaksi.blade.php` | Dashboard summary + tab filter + filter bar + tabel + modal Detail/Verifikasi Transaksi, AJAX (full rewrite dari mock, 2026-07-13) |
| `resources/views/admin/partials/transaksi-list.blade.php` | Partial tabel+pagination Transaksi + indikator "belum dibuka", dipakai render awal & fragment AJAX  |
| `resources/views/layouts/admin.blade.php`  | Shell admin + form logout tersentralisasi + menu Referral Agent/Murid + meta `csrf-token` (baru 2026-07-13)  |
| `resources/views/layouts/app.blade.php`    | Shell publik + script cleanup URL `?share_via=`                                                              |
| `database/seeders/AdminSettingSeeder.php`  | Seed `wa_admin_number` (belum di-wire)                                                                       |
| `CLAUDE.md`                                | Instruksi wajib AI — jangan diedit tanpa diminta                                                             |
| `PROJECT_MEMORY.md`                        | Audit AI sebelumnya — jangan ditimpa                                                                         |

---

## 15. External Integration

| Integrasi               | Status                    | Bukti                                                                                                                                              |
| ----------------------- | ------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Midtrans** (payment)  | ❌ Planned, belum di kode | Disebut `docs/decisions.md`; tidak ada SDK/config/controller. Skema `transaksi` sudah future-proof (`gateway_provider`/`gateway_transaction_id`/`gateway_payload`, kolom `metode_pembayaran` gampang tambah nilai baru) sejak modul Transaksi (2026-07-13), tapi belum dipakai sama sekali |
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
| 2026-07-13 | Admin Transaksi (Manajemen Transaksi) | Modul baru verifikasi manual pembayaran (transfer bank), menggantikan view mock `admin/transaksi.blade.php`. Tabel baru `transaksi` + `transaksi_activities` (audit trail), skema future-proof utk payment gateway (`gateway_provider`/`gateway_transaction_id`/`gateway_payload`, belum dipakai). Invoice auto-generate `INV-00000001` dari id. Transaksi auto-dibuat saat pendaftaran publik (`TransaksiService::createFromMurid()`, nominal dari mapping harga paket landing page, dikonfirmasi owner). Dashboard summary 4 kartu + tab filter status + filter bar (search/paket/metode/tanggal), indikator transaksi belum dibuka (`opened_at`), modal Detail (info murid+transaksi, catatan internal editable, bukti transfer preview/lihat/download, histori aktivitas), modal Verifikasi (upload bukti wajib + catatan opsional → status Berhasil + murid auto jadi `Murid::STATUS_AKTIF` baru, dikonfirmasi owner), aksi Tolak (tambahan di luar requirement awal, supaya tab "Ditolak" bisa tercapai). Bukti transfer disimpan disk `local` privat, nama file UUID. File baru: `Transaksi`, `TransaksiActivity`, `TransaksiService`, `AdminTransaksiController`, `VerifyTransaksiRequest`/`UpdateTransaksiCatatanRequest`, `admin/partials/transaksi-list.blade.php`. Route `admin.transaksi` (closure) → `admin.transaksi.index` (RESTful + sub-routes), sidebar & section-tabs disesuaikan. Auto-create transaksi utk murid tambah manual admin masuk `docs/todo.md` (belum di fase ini). | Admin bisa verifikasi pembayaran transfer manual nyata dari dashboard (sebelumnya mock dummy). Migration `create_transaksi_table` & `create_transaksi_activities_table` **sudah dijalankan** owner — testing fungsional masih menyusul. Murid punya status baru `Aktif`. Route name lama `admin.transaksi` (closure mock) tidak ada lagi. |
| 2026-07-13 | Admin Murid (CRUD nyata, AJAX) | Modul admin Murid diubah dari view mock jadi CRUD fungsional: list (pagination+search server-side+total+empty state, eager load referralAgent), tambah/edit via modal (status otomatis Daftar, referral kosong, normalisasi WA identik publik, unsaved-changes confirm), detail via modal (fetch JSON, style Referral Agent), soft delete (`SoftDeletes` + migration baru) + toast + reload async, export CSV seluruh data (tombol disabled kalau kosong). Controller/Service/Request baru: `AdminMuridController`, `MuridService`, `StoreAdminMuridRequest`/`UpdateAdminMuridRequest`. Route `admin.murid` (closure) → `admin.murid.index` (RESTful), sidebar & section-tabs disesuaikan. Tambah meta `csrf-token` di layout admin. | Owner sekarang bisa kelola data murid nyata dari admin (sebelumnya cuma dummy). **Migration `add_soft_deletes_to_murid_table` perlu `php artisan migrate` manual** sebelum modul ini jalan. Route name lama `admin.murid` (closure mock) tidak ada lagi, diganti `admin.murid.index`. |
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
9. `Murid` status baru selalu `'Daftar'`. Status `'Aktif'` (baru, 2026-07-13) auto-diset saat transaksi pembayaran diverifikasi berhasil — status lain (Pending/Nonaktif/dst) masih belum ada.
10. Level: Hijaiyah/Iqra/Tahsin/Tajwid/Hafalan. Paket: Basic/Pro/Premium/Platinum.
11. Harga paket hanya teks di frontend, **tidak ada di DB**.
12. Nomor WA disimpan format `62xxxx`; dinormalisasi saat store.
13. Pendaftaran publik `POST /daftar` → validasi → simpan → **JSON** (bukan redirect), throttle 10/menit.
14. Referral: `?share_via=KODE` (param URL, konstanta `ReferralAgentService::QUERY_PARAM`) → cookie 30 hari → auto-resolve `referral_agent_id` saat daftar. Capture jalan di `/` & `/daftar`. **UI admin sudah ada** (`admin/referral-agent`, sejak 2026-07-13).
15. Auth: built-in Laravel session (**bukan Breeze**), login rate-limit 5/menit, redirect ke `admin.dashboard`.
16. Semua route `admin.*` dilindungi middleware `auth`; guest → login. **Tidak ada role/permission**.
17. Halaman admin (dashboard, guru, jadwal, paket, laporan, pengaturan) masih **view mock statis** — kecuali Referral Agent, Murid, dan **Transaksi** (semua sejak 2026-07-13) yang sudah pakai controller nyata.
18. Logout tersentralisasi di `layouts/admin.blade.php` (sebelumnya mock, sudah diperbaiki 2026-07-13).
19. `LogVisitor` mencatat kunjungan GET non-admin ke `visitor_logs` (dedup tanggal+path+IP, increment hit).
20. Settings global = key-value `admin_settings`, akses `AdminSetting::get()`. `wa_admin_number` dipakai di response pendaftaran.
21. `AdminSettingSeeder` **belum di-wire** ke `DatabaseSeeder` → `wa_admin_number` tidak ter-seed otomatis.
22. Tidak ada integrasi eksternal aktif: Midtrans/Zoom/WA API semua **placeholder/mock**. Mail driver `log`.
23. Pembayaran = **manual bulanan**, semi-subscription. Jangan bangun auto-billing. Verifikasi pembayaran (transfer bank manual) sekarang nyata lewat modul Transaksi (2026-07-13) — payment gateway masih belum diimplementasikan, cuma skema DB yang future-proof.
24. `StorePostRequest` orphan (authorize false, rules kosong) — jangan disentuh tanpa izin.
25. Belum ada test nyata (hanya `ExampleTest`).
26. Git: branch-per-fitur, Conventional Commits bahasa Indonesia (`feat(scope):`).
27. App di belakang Cloudflare (`trustProxies`). Health check `/up` aktif.
28. Session/cache/queue driver = `database`; timezone `Asia/Jakarta`; locale `en`.
29. Keputusan tertunda: set status Murid lengkap (Pending/Nonaktif/dst, di luar Daftar/Aktif yang sudah ada), role/permission system, delivery Zoom, auto-create transaksi utk murid tambah manual admin.
30. Prioritas berikutnya: wire seeder `AdminSettingSeeder` → export by filter & restore soft delete → integrasi payment gateway (opsional, tunggu keputusan owner).
31. SSOT dokumentasi = file ini; kalau konflik, **kode menang**, lalu perbarui file ini.
32. Guardrail utama: no rename, no schema change tanpa izin, no terjemah istilah domain, no refactor spekulatif, no auto-billing.
33. Owner = solo dev, komunikasi santai-akrab tapi profesional; kirim hanya file terdampak saat revisi.
34. Fitur terakhir selesai: Admin Transaksi (Manajemen Transaksi) — CRUD verifikasi pembayaran nyata (dashboard summary, tab filter, filter bar, indikator transaksi baru, modal Detail + Verifikasi + Tolak, audit trail) (2026-07-13). Migration dijalankan, testing lokal oke, **sudah commit & deploy ke VPS produksi, dikonfirmasi jalan aman**.
35. Tabel baru: `transaksi` (invoice auto-generate, nominal dari mapping harga paket, future-proof utk payment gateway) & `transaksi_activities` (audit trail readonly). Murid dapat status baru `Aktif` (auto-set saat verifikasi berhasil).

## Server Specs

- VPS: MS 4.2, Biznet Gio
- OS: Ubuntu 26.04
- Resource: 2 vCPU, 4GB RAM, 60GB SSD
- Stack: Nginx, PHP 8.3-FPM, MariaDB

---

_Perbarui file ini setiap kali arsitektur, struktur folder, business rule, integrasi, atau status fitur berubah — terutama §11 (Last Completed Feature) dan §18 (Changelog) setiap fitur selesai._
