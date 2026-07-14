# Workflow Index

> File ini adalah **index** seluruh workflow yang berlaku di project NgajiNusa. Isinya peta/navigasi, bukan SOP rinci — untuk detail langkah eksekusi tiap workflow, rujuk file `workflow-*.md` masing-masing.

---

## 1. Tujuan Workflow

Sistem workflow ada supaya:

- Setiap jenis pekerjaan (fitur baru, bug fix, refactor, rilis, dsb.) punya cara kerja yang konsisten, tidak tergantung ingatan owner untuk mengarahkan tiap kali.
- Claude bisa langsung tahu SOP mana yang harus dipakai berdasarkan konteks pekerjaan, tanpa nebak-nebak atau menunggu instruksi ulang.
- Tanggung jawab tiap tahap kerja (mulai fitur, debug, refactor, rilis, dokumentasi, wrap-up) terpisah jelas, tapi tetap saling melengkapi jadi satu alur kerja utuh.
- Menambah workflow baru di masa depan gampang — cukup tambah file baru dan daftarkan di sini, tanpa bongkar struktur yang sudah ada.

---

## 2. Available Workflows

| Workflow | Status | Fungsi | Kapan Digunakan |
|---|---|---|---|
| `workflow-wrapup.md` | **Aktif** | Menutup sebuah unit pekerjaan yang sudah selesai diimplementasikan: update dokumentasi terkait, susun rencana commit per Logical Change, buat commit message, cek manual task tersisa, tentukan status akhir. | Setiap kali implementasi (fitur/fix/refactor/optimasi/config/docs) selesai dikerjakan, atau saat owner trigger kata seperti "wrap up", "closing", "fitur selesai". |
| `workflow-feature.md` | Belum dibuat | Rencana: memandu proses awal sampai pertengahan pengembangan fitur baru — dari breakdown requirement, desain arsitektur (Controller → Service → Model), sampai implementasi berjalan. | Saat owner mulai minta fitur baru dibangun, sebelum masuk tahap wrap-up. |
| `workflow-debug.md` | Belum dibuat | Rencana: memandu proses investigasi bug — reproduksi masalah, isolasi penyebab, verifikasi fix, tanpa menyentuh area di luar bug yang dilaporkan. | Saat owner melaporkan bug/error atau perilaku yang tidak sesuai ekspektasi. |
| `workflow-refactor.md` | Belum dibuat | Rencana: memandu proses refactor kode tanpa mengubah behavior — termasuk batasan scope, cara verifikasi tidak ada regresi. | Saat owner minta bersihkan/rapikan kode existing tanpa mengubah fungsi. |
| `workflow-release.md` | Belum dibuat | Rencana: memandu proses persiapan rilis — checklist sebelum deploy, urutan migration/build/deploy, rollback plan. | Saat owner bersiap deploy ke server/production. |
| `workflow-documentation.md` | Belum dibuat | Rencana: memandu proses menulis/update dokumentasi project secara mandiri (bukan sebagai bagian dari wrap-up fitur lain). | Saat owner minta dokumentasi baru dibuat atau dirapikan di luar konteks wrap-up fitur. |

Setiap workflow baru yang dibuat wajib didaftarkan di tabel ini beserta status dan fungsinya.

---

## 3. Kapan Workflow Digunakan

Workflow dijalankan situasional, mengikuti fase pekerjaan yang sedang berlangsung — bukan dipilih manual satu-satu oleh owner:

- Mulai bangun fitur → `workflow-feature.md`.
- Ada bug/error dilaporkan → `workflow-debug.md`.
- Minta bersihkan kode tanpa ubah fungsi → `workflow-refactor.md`.
- Bersiap deploy → `workflow-release.md`.
- Butuh dokumentasi mandiri → `workflow-documentation.md`.
- Pekerjaan apa pun sudah selesai diimplementasikan → `workflow-wrapup.md` (selalu jadi penutup).

---

## 4. Cara Claude Menentukan Workflow yang Harus Dijalankan

1. Baca konteks permintaan owner: jenis pekerjaan apa yang sedang diminta atau fase apa yang sedang berjalan.
2. Cocokkan konteks tersebut ke workflow yang relevan di tabel Bagian 2 — pakai deskripsi "Kapan Digunakan" sebagai acuan, bukan hanya keyword literal.
3. Kalau ada trigger eksplisit dari owner (mis. kata "wrap up", "closing") — langsung jalankan workflow terkait tanpa konfirmasi ulang, sesuai aturan di file workflow itu sendiri.
4. Satu sesi kerja bisa melibatkan lebih dari satu workflow secara berurutan (mis. `workflow-feature.md` saat develop, lalu `workflow-wrapup.md` di akhir) — workflow-workflow ini saling melengkapi, bukan saling menggantikan.
5. Jika belum ada file workflow untuk situasi tertentu (status "Belum dibuat"), Claude bekerja mengikuti prinsip umum di `CLAUDE.md` dan `docs/ai_workflow.md` sampai workflow spesifiknya dibuat.

---

## 5. Catatan

- Tiap workflow punya tanggung jawab (scope) masing-masing dan tidak boleh tumpang tindih — kalau ada workflow baru yang isinya mirip/overlap dengan workflow lain, itu tanda perlu direview dulu sebelum ditambahkan.
- File ini **tidak berisi SOP rinci** — SOP lengkap selalu ada di file `workflow-*.md` terkait.
- Update tabel Bagian 2 setiap kali ada workflow baru ditambahkan atau status workflow berubah (dari "Belum dibuat" jadi "Aktif").
</content>
</invoke>
