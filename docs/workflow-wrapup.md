# Workflow Wrap-up

> SOP wajib untuk proses **wrap-up** setelah sebuah implementasi selesai di project NgajiNusa. File ini adalah rincian operasional dari poin "Wrap-up tiap fitur selesai" di `docs/ai-context.md` §16 — bila ada perbedaan detail, file ini yang dipakai untuk *cara eksekusi* wrap-up, sedangkan `ai-context.md` tetap SSOT untuk *kondisi project*.
>
> Berlaku untuk semua jenis pekerjaan: fitur baru, bug fixing, refactoring, optimasi, perubahan konfigurasi, dan perubahan dokumentasi.

---

## 1. Tujuan Workflow

Menstandarkan langkah penutupan (wrap-up) setiap pekerjaan agar:

- Dokumentasi project (`docs/ai-context.md` dan file terkait) selalu sinkron dengan kode nyata, tanpa owner perlu mengingatkan berulang kali.
- Setiap perubahan punya jejak waktu yang presisi (hingga detik) untuk keperluan audit dan histori.
- Commit yang dihasilkan rapi, granular per Logical Change, dan mudah ditelusuri (`git log`, `git blame`) di kemudian hari.
- Pesan commit konsisten mengikuti Conventional Commits berbahasa Indonesia sesuai konvensi yang sudah berjalan di project ini.
- Pekerjaan manual yang belum tuntas (migration, build, deploy, cache, testing, dll) selalu terlihat jelas, tidak terkubur di tengah percakapan.
- Setiap sesi kerja menghasilkan output yang konsisten formatnya, tanpa perlu instruksi ulang dari owner.

---

## 2. Kapan Workflow Digunakan

Jalankan seluruh workflow ini setiap kali sebuah unit pekerjaan **selesai diimplementasikan**, mencakup:

- Fitur baru
- Bug fixing
- Refactoring
- Optimasi (performa, query, dsb.)
- Perubahan konfigurasi (`.env.example`, `config/*.php`, `composer.json`, `package.json`, dsb.)
- Perubahan dokumentasi (edit/tambah file di `docs/`, `README.md`, dsb.)

**Trigger dari owner.** Kata/frasa berikut — dalam bentuk apa pun, termasuk campuran Indonesia/Inggris — dianggap sebagai perintah eksplisit untuk menjalankan seluruh workflow ini, tanpa perlu konfirmasi ulang:

- "wrap up" / "wrap-up"
- "closing"
- "finish feature"
- "fitur selesai"
- "implementasi selesai"

Begitu salah satu trigger ini muncul, langsung eksekusi Bagian 3 secara lengkap dan sajikan hasil sesuai format Bagian 6. Jangan menyingkat prosesnya menjadi ringkasan santai.

---

## 3. Langkah-langkah Wrap-up

Jalankan berurutan. Jangan melompati langkah meskipun perubahan terlihat kecil.

### 3.1 Analisis Perubahan

1. Cek status kerja: `git status`, `git diff` (atau `git diff --staged` bila sudah di-stage).
2. Identifikasi **semua** file yang berubah: baru, dimodifikasi, dihapus, di-rename.
3. Untuk tiap file, tentukan kategori perubahan: fitur baru, bug fix, refactor, optimasi, config, atau dokumentasi.
4. Tandai apakah ada perubahan yang menyentuh area sensitif menurut `ai-context.md` §19 (schema DB, rename method/route/model, `CLAUDE.md`, `PROJECT_MEMORY.md`, `.env`) — jika ya, **stop dan konfirmasi ke owner dulu** sebelum lanjut ke commit, karena area ini butuh izin eksplisit.
5. Rangkum inti perubahan dalam 1–3 kalimat per file/kelompok file — ini jadi bahan untuk langkah berikutnya.

### 3.2 Update Dokumentasi yang Relevan

Cek dokumentasi mana yang wajib diperbarui berdasarkan jenis perubahan. Update **hanya** dokumen yang benar-benar terdampak — jangan menyentuh dokumen yang tidak relevan.

| Jenis Perubahan | Dokumen yang Wajib Dicek |
|---|---|
| Fitur baru | `docs/ai-context.md` §11 (Last Completed Feature) **wajib**, §18 (Changelog Ringkas) **wajib**, §10 (Current Feature Status) bila status fitur berubah, §8 (Routing) bila ada route baru, §6 (Database) bila ada schema baru |
| Bug fixing | `docs/ai-context.md` §13 (Known Issues) — hapus/perbarui entri yang sudah tidak berlaku, §18 (Changelog Ringkas) |
| Refactoring | `docs/ai-context.md` §3 (Architecture) bila pola berubah, §13 (Known Issues) bila menghapus technical debt, §18 (Changelog Ringkas) |
| Optimasi | `docs/ai-context.md` §13 (Known Issues) bila terkait, §18 (Changelog Ringkas) |
| Perubahan konfigurasi | `docs/ai-context.md` §2 (Technology Stack) bila dependency/driver berubah, `docs/deployment.md` bila menyangkut server/production |
| Perubahan dokumentasi | Tidak perlu update dokumen lain di luar yang diedit — cukup catat di §18 (Changelog Ringkas) sebagai entri "docs" |

Aturan tambahan:

- **Jangan pernah edit** `CLAUDE.md` atau `PROJECT_MEMORY.md` sebagai bagian dari wrap-up otomatis — keduanya butuh izin eksplisit di luar workflow ini.
- Kirim/tampilkan **hanya bagian dokumen yang berubah**, bukan seluruh file, kecuali diminta lain (sesuai preferensi owner di `ai-context.md` §19).
- Untuk revisi minor 1 file dokumentasi, cukup tampilkan potongan (diff/snippet) bagian yang diubah — tidak perlu regenerasi/zip ulang seluruh dokumentasi.

### 3.3 Penambahan Timestamp hingga Detik

Setiap wrap-up **wajib** dicap waktu dengan presisi detik — lihat format di Bagian 5. Timestamp ini dipakai untuk:

- Entri baru di `ai-context.md` §11 dan §18 (kolom tanggal boleh tetap `YYYY-MM-DD` di tabel changelog, tapi laporan wrap-up sendiri harus mencantumkan jam:menit:detik).
- Header laporan Wrap-up Report (lihat Bagian 6).

### 3.4 Analisis Pembagian Commit Berdasarkan Logical Change

1. Kelompokkan file yang berubah berdasarkan **satu tujuan/maksud yang sama** (Logical Change), bukan berdasarkan direktori atau tipe file.
2. Contoh pengelompokan yang benar: migration + model + service + controller + route + view untuk satu fitur = **satu commit**, meskipun terdiri dari 6 file. Sebaliknya, fix bug tak terkait di file lain = commit terpisah meskipun cuma 1 baris.
3. Jika satu sesi kerja mencakup lebih dari satu Logical Change (mis. fitur baru + bug fix tidak terkait yang kebetulan ditemukan di tengah jalan), pisahkan jadi commit-commit berbeda — jangan digabung demi kepraktisan.
4. Urutkan commit secara logis (mis. migration/schema dulu, baru service/controller, baru view) agar histori tetap bisa di-bisect dan dipahami.
5. Dokumentasi yang diupdate di langkah 3.2 masuk ke commit Logical Change yang paling relevan (biasanya commit terakhir dari kelompok itu), **bukan** commit dokumentasi terpisah — kecuali wrap-up ini murni untuk perubahan dokumentasi saja.

### 3.5 Pembuatan Conventional Commit Message

Format: `type(scope): deskripsi singkat dalam Bahasa Indonesia`

Tabel pemetaan jenis perubahan → `type`:

| Jenis Perubahan | Conventional Commit Type |
|---|---|
| Fitur baru | `feat` |
| Bug fixing | `fix` |
| Refactoring (tanpa mengubah behavior) | `refactor` |
| Optimasi performa | `perf` |
| Perubahan konfigurasi/tooling/dependency | `chore` |
| Perubahan dokumentasi | `docs` |
| Perubahan gaya kode tanpa efek logic (formatting, Pint, dsb.) | `style` |
| Menambah/mengubah test | `test` |

Aturan penulisan:

- `scope` = area/domain terdampak, snake/kebab pendek: `auth`, `murid`, `referral`, `admin`, `docs`, dsb. — mengikuti pola yang sudah dipakai di `docs/changelog.md` (`feat(auth): ...`, `feat(referral): ...`).
- Deskripsi ditulis Bahasa Indonesia, gaya santai-teknis (bukan formal kaku), sesuai gaya commit yang sudah ada di project — contoh: `feat(auth): proteksi route admin dengan middleware auth, benerin tombol logout`.
- Satu baris judul singkat dan jelas. Body tambahan (opsional, dipisah baris kosong) boleh dipakai untuk menjelaskan alasan/dampak bila commit cukup kompleks.
- Jangan mengarang `scope` yang tidak mencerminkan area kode yang benar-benar berubah.

### 3.6 Identifikasi Remaining Manual Tasks

Selalu cek dan sebutkan **secara eksplisit** bila ada pekerjaan manual yang belum dilakukan AI/sandbox dan perlu dikerjakan owner sendiri, termasuk namun tidak terbatas pada:

- Migration yang belum dijalankan (`php artisan migrate`)
- Seeder yang belum di-wire/dijalankan (`php artisan db:seed`)
- Build asset (`npm run build`) yang belum dieksekusi
- Cache yang perlu di-clear/rebuild (`php artisan config:clear`, `optimize`, dsb.)
- Testing manual yang perlu dilakukan owner (karena sandbox AI tidak selalu punya akses PHP/DB/browser lokal)
- Deployment ke server/VPS
- Perubahan `.env` yang perlu disesuaikan manual (kredensial, key baru, dsb.)
- Verifikasi visual/manual di browser untuk perubahan UI

Jika **tidak ada** manual task tersisa, tulis eksplisit "Tidak ada." — jangan dikosongkan begitu saja, supaya jelas itu memang sudah dicek, bukan lupa dicantumkan.

### 3.7 Final Status

Tutup wrap-up dengan status akhir yang jelas dan tidak ambigu, salah satu dari:

- **Selesai & siap commit** — semua langkah di atas sudah dilakukan, tidak ada blocker.
- **Selesai, menunggu manual task** — kode sudah beres, tapi ada tindakan manual (lihat 3.6) yang harus dilakukan owner sebelum fitur benar-benar aktif.
- **Sebagian selesai** — jelaskan bagian apa yang belum, dan kenapa (mis. menunggu keputusan owner soal status Murid, role/permission, dsb).

---

## 4. Aturan Commit

- **1 commit = 1 Logical Change.** Sebuah commit harus bisa dijelaskan dalam satu kalimat "commit ini melakukan X" tanpa kata "dan" yang menyambung dua tujuan berbeda.
- **Jangan membagi commit berdasarkan jumlah file.** Commit besar (banyak file) untuk satu Logical Change itu wajar dan benar. Commit kecil (1 file) yang sebenarnya bagian dari Logical Change lain itu salah — gabungkan ke commit yang tepat.
- Jangan mencampur `feat` dan `fix` yang tidak berhubungan dalam satu commit, meskipun ditemukan di sesi kerja yang sama.
- Perubahan dokumentasi yang menyertai sebuah fitur/fix ikut masuk ke commit Logical Change tersebut (lihat 3.4 poin 5), bukan commit `docs` terpisah — kecuali wrap-up murni untuk dokumentasi.
- Commit tidak boleh menyertakan file yang tidak diminta/tidak terkait Logical Change tersebut (selaras dengan aturan "ubah hanya file yang diminta" di `docs/ai_workflow.md`).

---

## 5. Format Timestamp

Gunakan salah satu dari dua format berikut, konsisten di seluruh laporan wrap-up:

- **ISO-8601:** `2026-07-13T15:48:42+07:00`
- **Format lokal:** `YYYY-MM-DD HH:mm:ss WIB` — contoh: `2026-07-13 15:48:42 WIB`

Ketentuan:

- Timezone selalu **Asia/Jakarta (WIB)**, sesuai `APP_TIMEZONE` project.
- Presisi **wajib hingga detik** — jangan dibulatkan ke menit.
- Timestamp diambil pada saat wrap-up dieksekusi (bukan waktu mulai pekerjaan).

---

## 6. Standar Output Wrap-up Report

Setiap kali workflow ini dijalankan, output **wajib** dalam format laporan lengkap berikut — bukan ringkasan singkat berupa satu-dua paragraf. Semua bagian harus diisi (isi eksplisit "Tidak ada." bila memang kosong, jangan dihilangkan).

```markdown
# Wrap-up Report

## Documentation Updated
[Daftar file dokumentasi yang diupdate + bagian/section spesifik yang berubah.
Jika tidak ada dokumentasi yang perlu diupdate, tulis "Tidak ada." beserta alasannya.]

## Timestamp
[Timestamp wrap-up sesuai format Bagian 5]

## Commit Plan
[Daftar Logical Change yang teridentifikasi, masing-masing dengan:
- Nama Logical Change
- File-file yang termasuk
- Alasan singkat kenapa dikelompokkan jadi satu commit]

## Commit Messages
[Daftar pesan commit final sesuai Conventional Commits Bahasa Indonesia,
satu per Logical Change, siap pakai (bisa langsung di-copy ke `git commit -m`)]

## Remaining Manual Tasks
[Daftar tugas manual yang belum dikerjakan, sesuai Bagian 3.6.
Tulis "Tidak ada." jika memang tidak ada.]

## Final Status
[Salah satu dari tiga status di Bagian 3.7, disertai penjelasan singkat]
```

Ketentuan tambahan:

- Header wajib persis seperti di atas (`##`), urutannya tidak boleh ditukar.
- Boleh menambah sub-detail di dalam tiap section bila diperlukan, tapi keenam section wajib tetap ada.
- Laporan ini disampaikan **setiap kali** trigger di Bagian 2 muncul, tanpa perlu owner meminta detail tambahan.

---

_File ini adalah SOP proses, bukan catatan kondisi project — untuk kondisi project terkini selalu rujuk `docs/ai-context.md`. Perbarui file ini hanya bila owner mengubah cara kerja wrap-up (bukan setiap fitur selesai)._
