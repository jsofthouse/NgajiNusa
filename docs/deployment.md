# Deployment

Server

Ubuntu 26.04

Web Server

Nginx 1.28.3

PHP

PHP 8.5-FPM

Database

MariaDB 11.8.6

Commands

composer install

php artisan migrate

php artisan optimize

php artisan config:cache

php artisan route:cache

php artisan view:cache

Monitoring (Netdata)

VPS: 2 vCPU, RAM 4GB, SSD 60GB.

Netdata Community v2.10.0-771-nightly — monitoring utama project, final validation selesai 2026-07-15.

Dashboard diakses via `https://monitor.ngajinusa.com`. Reverse proxy pakai Nginx, WebSocket proxy aktif. Netdata cuma bind ke `127.0.0.1:19999` — port 19999 tidak diekspos ke internet.

Historical metrics dituning 2026-07-15 di `/etc/netdata/netdata.conf` `[db]`:

- update every = 2 (default 1) — turunin beban CPU/disk write
- dbengine page cache size = 128MiB (default 32MiB) — query histori lebih responsif
- dbengine tier 0 retention size/time = 1024MiB / 14d (default, gak diubah)
- dbengine tier 1 retention size/time = 2048MiB / 6mo (default 1024MiB/3mo)
- dbengine tier 2 retention size/time = 4096MiB / 3y (default 1024MiB/2y)

Total disk maks Netdata ~7GB (dari 52GB free saat itu). Sisa setting (`storage tiers=3`, `page type=gorilla`, `use direct io=yes`) dibiarin default. Ringkasan final baseline: historical metrics jalan di atas dbengine dengan quota 1GB (tier 0, resolusi utama).

Belum dibahas: ML anomaly detection (aktif default, makan CPU terus-menerus) — belum di-disable, nunggu keputusan.

Collector (verified 2026-07-15)

- Nginx collector: VERIFIED.
- PHP-FPM collector: VERIFIED.
- MariaDB collector: VERIFIED (41 charts).

Security (final baseline 2026-07-15)

- SSL aktif.
- Cloudflare Real IP aktif.
- WebSocket proxy aktif.
- Security headers aktif.
- Method whitelist aktif.
- Dotfile blocking aktif.
- Cloudflare Access membatasi akses dashboard hanya untuk email yang diizinkan.
- Port 19999 tidak diekspos ke internet (bind `127.0.0.1` + reverse proxy Nginx).

Bug Fix — PHP-FPM collector gagal (2026-07-15)

Root cause: request collector `go.d/phpfpm` ke `127.0.0.1` gak mengirim Host header, jadi begitu server block `monitor.ngajinusa.com` ditambahin, Nginx fallback ke virtual host yang salah.

Fix:

- Tambah header `Host: ngajinusa.com` di job `go.d/phpfpm.conf`.
- Restart service Netdata (`systemctl restart netdata`) — reload aja gak cukup buat perubahan config collector ini.

Lesson Learned:

- Tiap nambah server block Nginx baru, wajib validasi ulang collector yang akses localhost tanpa Host header — urutan server block bisa ngubah routing fallback.
- Reload Netdata gak cukup buat perubahan config collector tertentu, pakai restart.

Operational Notes

- Alarm `net_drops.eth0` yang muncul saat audit berasal dari traffic scan/brute-force yang diblokir Fail2Ban, bukan bottleneck server.

Backlog

- Backup konfigurasi Netdata.
- Konfigurasi health.d alert (Telegram).
- Monitor pertumbuhan dbengine quota 1GB.
- Review exposure SSH sesuai rencana.
- Pastikan DNS `monitor.ngajinusa.com` tetap Proxied di Cloudflare.
