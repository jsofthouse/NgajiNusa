@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-pengaturan.css') }}" />
@endpush

@section('title', 'NgajiNusa - Pengaturan Sistem')

@section('page-title', '⚙️ Pengaturan Sistem')
@section('page-subtitle', 'Konfigurasi aplikasi, integrasi, dan manajemen admin')

@section('topbar-actions')
                <button class="btn-secondary" onclick="showToast('Pengaturan berhasil direset ke default!')">
                    <i class="fas fa-undo"></i> Reset Default
                </button>
                <button class="btn-primary" onclick="saveAllSettings()">
                    <i class="fas fa-save"></i> Simpan Semua
                </button>
@endsection

@section('content')
        <!-- Settings Tabs -->
        <div class="settings-tabs">
            <button class="active" data-tab="umum"><i class="fas fa-globe"></i> <span>Umum</span></button>
            <button data-tab="admin"><i class="fas fa-user-shield"></i> <span>Admin</span></button>
            <button data-tab="notifikasi"><i class="fas fa-bell"></i> <span>Notifikasi</span></button>
            <button data-tab="integrasi"><i class="fas fa-plug"></i> <span>Integrasi</span></button>
            <button data-tab="keamanan"><i class="fas fa-lock"></i> <span>Keamanan</span></button>
            <button data-tab="tema"><i class="fas fa-palette"></i> <span>Tema</span></button>
        </div>

        <!-- ===== TAB: UMUM ===== -->
        <div class="tab-content active" id="tab-umum">
            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle"></i> Informasi Aplikasi</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Aplikasi <span class="required">*</span></label>
                            <input type="text" value="NgajiNusa" />
                        </div>
                        <div class="form-group">
                            <label>Versi Aplikasi</label>
                            <input type="text" value="1.0.0" disabled style="background:#f5f8f6;" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Singkat</label>
                        <textarea rows="3">Platform belajar ngaji online dengan guru bersertifikat dan metode terstruktur.</textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Domain Utama</label>
                            <input type="text" value="https://ngajinusa.com" />
                        </div>
                        <div class="form-group">
                            <label>Timezone</label>
                            <select>
                                <option selected>Asia/Jakarta (WIB)</option>
                                <option>Asia/Makassar (WITA)</option>
                                <option>Asia/Jayapura (WIT)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Format Tanggal</label>
                            <select>
                                <option selected>DD/MM/YYYY</option>
                                <option>MM/DD/YYYY</option>
                                <option>YYYY-MM-DD</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mata Uang</label>
                            <select>
                                <option selected>IDR - Rupiah (Rp)</option>
                                <option>USD - Dollar ($)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-package"></i> Konfigurasi Paket</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Paket Default</label>
                            <select>
                                <option>Basic</option>
                                <option selected>Pro</option>
                                <option>Premium</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Masa Berlaku Paket (hari)</label>
                            <input type="number" value="30" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Maksimal Sesi per Hari</label>
                        <input type="number" value="4" />
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TAB: ADMIN ===== -->
        <div class="tab-content" id="tab-admin">
            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-users-cog"></i> Manajemen Admin</h3>
                    <button class="btn-primary" style="padding:8px 18px;font-size:0.8rem;" onclick="showToast('Form tambah admin dibuka')">
                        <i class="fas fa-plus"></i> Tambah Admin
                    </button>
                </div>
                <div class="card-body">
                    <div class="admin-item">
                        <div class="admin-info">
                            <div class="avatar-sm green">FI</div>
                            <div>
                                <div class="admin-name">Fajarudin Irfan</div>
                                <div class="admin-role">Super Admin · fajarudin@ngajinusa.com</div>
                            </div>
                        </div>
                        <div class="admin-actions">
                            <span class="status-badge" style="background:rgba(34,180,85,0.12);color:var(--primary-light);padding:4px 12px;border-radius:50px;font-size:0.7rem;font-weight:600;">Aktif</span>
                            <button onclick="showToast('Edit admin')"><i class="fas fa-edit"></i></button>
                        </div>
                    </div>
                    <div class="admin-item">
                        <div class="admin-info">
                            <div class="avatar-sm orange">IA</div>
                            <div>
                                <div class="admin-name">Ichsan Ali</div>
                                <div class="admin-role">Admin Operasional · ichsan@ngajinusa.com</div>
                            </div>
                        </div>
                        <div class="admin-actions">
                            <span class="status-badge" style="background:rgba(34,180,85,0.12);color:var(--primary-light);padding:4px 12px;border-radius:50px;font-size:0.7rem;font-weight:600;">Aktif</span>
                            <button onclick="showToast('Edit admin')"><i class="fas fa-edit"></i></button>
                            <button class="danger" onclick="showToast('Admin dinonaktifkan')"><i class="fas fa-user-slash"></i></button>
                        </div>
                    </div>
                    <div class="admin-item">
                        <div class="admin-info">
                            <div class="avatar-sm blue">RN</div>
                            <div>
                                <div class="admin-name">Rudi M. Noer</div>
                                <div class="admin-role">Admin Keuangan · rudi@ngajinusa.com</div>
                            </div>
                        </div>
                        <div class="admin-actions">
                            <span class="status-badge" style="background:rgba(245,166,35,0.12);color:var(--secondary);padding:4px 12px;border-radius:50px;font-size:0.7rem;font-weight:600;">Pending</span>
                            <button onclick="showToast('Edit admin')"><i class="fas fa-edit"></i></button>
                            <button onclick="showToast('Admin diaktifkan')" style="background:var(--primary);color:white;"><i class="fas fa-check"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-key"></i> Hak Akses Role</h3>
                </div>
                <div class="card-body">
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Super Admin</div>
                            <div class="desc">Akses penuh ke semua fitur dan konfigurasi</div>
                        </div>
                        <span style="font-size:0.8rem;color:var(--text-light);">Tidak dapat diubah</span>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Admin Operasional</div>
                            <div class="desc">Kelola murid, guru, jadwal, dan transaksi</div>
                        </div>
                        <div class="toggle active" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Admin Keuangan</div>
                            <div class="desc">Akses ke laporan keuangan dan transaksi</div>
                        </div>
                        <div class="toggle active" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Guru / Staff</div>
                            <div class="desc">Akses terbatas: jadwal, laporan sesi, profil</div>
                        </div>
                        <div class="toggle active" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Customer / Murid</div>
                            <div class="desc">Akses: dashboard pribadi, progres, transaksi</div>
                        </div>
                        <div class="toggle active" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TAB: NOTIFIKASI ===== -->
        <div class="tab-content" id="tab-notifikasi">
            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-envelope"></i> Email Notifikasi</h3>
                </div>
                <div class="card-body">
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Konfirmasi Pendaftaran</div>
                            <div class="desc">Kirim email saat murid baru mendaftar</div>
                        </div>
                        <div class="toggle active" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Notifikasi Pembayaran</div>
                            <div class="desc">Kirim email saat pembayaran diterima</div>
                        </div>
                        <div class="toggle active" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Pengingat Jadwal</div>
                            <div class="desc">Kirim pengingat 24 jam sebelum sesi</div>
                        </div>
                        <div class="toggle active" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Laporan Bulanan</div>
                            <div class="desc">Kirim laporan ringkasan ke admin</div>
                        </div>
                        <div class="toggle" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:16px;">
                        <label>Email Pengirim (SMTP)</label>
                        <div class="input-group">
                            <input type="email" value="noreply@ngajinusa.com" />
                            <button onclick="showToast('Email SMTP dikonfigurasi!')">Konfigurasi</button>
                        </div>
                        <div class="help-text">Gunakan SMTP server untuk mengirim email notifikasi</div>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fab fa-whatsapp"></i> WhatsApp Notifikasi</h3>
                </div>
                <div class="card-body">
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Aktifkan WhatsApp Notifikasi</div>
                            <div class="desc">Kirim notifikasi via WhatsApp ke customer</div>
                        </div>
                        <div class="toggle active" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nomor WhatsApp Bisnis</label>
                        <div class="input-group">
                            <input type="text" value="0821-1219-3352" />
                            <button onclick="showToast('Nomor WA diverifikasi!')">Verifikasi</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Template Pesan Pengingat</label>
                        <textarea rows="3">Assalamu'alaikum {nama}, jadwal ngaji Anda hari ini pukul {jam} dengan guru {guru}. Link Zoom: {link}</textarea>
                        <div class="help-text">Gunakan variabel: {nama}, {jam}, {guru}, {link}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TAB: INTEGRASI ===== -->
        <div class="tab-content" id="tab-integrasi">
            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-plug"></i> Integrasi API</h3>
                    <span style="font-size:0.8rem;color:var(--text-light);">Kelola koneksi ke layanan pihak ketiga</span>
                </div>
                <div class="card-body">
                    <!-- Zoom -->
                    <div class="api-card">
                        <div class="api-info">
                            <div class="api-icon zoom"><i class="fas fa-video"></i></div>
                            <div>
                                <div class="api-name">Zoom API</div>
                                <div class="api-status connected">✓ Terhubung</div>
                            </div>
                        </div>
                        <div class="api-actions">
                            <button class="btn-configure" onclick="showToast('Konfigurasi Zoom dibuka')">Konfigurasi</button>
                            <button class="btn-disconnect" onclick="showToast('Zoom API diputus!')">Putuskan</button>
                        </div>
                    </div>

                    <!-- WhatsApp -->
                    <div class="api-card">
                        <div class="api-info">
                            <div class="api-icon whatsapp"><i class="fab fa-whatsapp"></i></div>
                            <div>
                                <div class="api-name">WhatsApp Business API</div>
                                <div class="api-status connected">✓ Terhubung</div>
                            </div>
                        </div>
                        <div class="api-actions">
                            <button class="btn-configure" onclick="showToast('Konfigurasi WhatsApp dibuka')">Konfigurasi</button>
                            <button class="btn-disconnect" onclick="showToast('WhatsApp API diputus!')">Putuskan</button>
                        </div>
                    </div>

                    <!-- Payment Gateway -->
                    <div class="api-card">
                        <div class="api-info">
                            <div class="api-icon payment"><i class="fas fa-credit-card"></i></div>
                            <div>
                                <div class="api-name">Payment Gateway (Midtrans)</div>
                                <div class="api-status connected">✓ Terhubung</div>
                            </div>
                        </div>
                        <div class="api-actions">
                            <button class="btn-configure" onclick="showToast('Konfigurasi Payment Gateway dibuka')">Konfigurasi</button>
                            <button class="btn-disconnect" onclick="showToast('Payment Gateway diputus!')">Putuskan</button>
                        </div>
                    </div>

                    <!-- Google Calendar -->
                    <div class="api-card">
                        <div class="api-info">
                            <div class="api-icon email"><i class="fas fa-calendar-alt"></i></div>
                            <div>
                                <div class="api-name">Google Calendar API</div>
                                <div class="api-status disconnected">✗ Belum Terhubung</div>
                            </div>
                        </div>
                        <div class="api-actions">
                            <button class="btn-connect" onclick="showToast('Google Calendar API terhubung!')">Hubungkan</button>
                            <button class="btn-configure" onclick="showToast('Konfigurasi Google Calendar dibuka')">Konfigurasi</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-qrcode"></i> Payment Gateway Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Merchant ID</label>
                            <input type="text" value="G123456789" />
                        </div>
                        <div class="form-group">
                            <label>Client Key</label>
                            <input type="text" value="SB-Mid-client-xxxxxxxx" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Server Key</label>
                            <input type="password" value="SB-Mid-server-xxxxxxxx" />
                        </div>
                        <div class="form-group">
                            <label>Environment</label>
                            <select>
                                <option selected>Sandbox</option>
                                <option>Production</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Metode Pembayaran Aktif</label>
                        <div style="display:flex;gap:16px;flex-wrap:wrap;padding-top:4px;">
                            <label style="display:flex;align-items:center;gap:6px;font-weight:400;font-size:0.9rem;">
                                <input type="checkbox" checked /> QRIS
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;font-weight:400;font-size:0.9rem;">
                                <input type="checkbox" checked /> Transfer Bank
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;font-weight:400;font-size:0.9rem;">
                                <input type="checkbox" checked /> E-Wallet
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;font-weight:400;font-size:0.9rem;">
                                <input type="checkbox" /> Kartu Kredit
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TAB: KEAMANAN ===== -->
        <div class="tab-content" id="tab-keamanan">
            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-shield-alt"></i> Keamanan & Privasi</h3>
                </div>
                <div class="card-body">
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">SSL/HTTPS Wajib</div>
                            <div class="desc">Akses aplikasi hanya melalui HTTPS</div>
                        </div>
                        <div class="toggle active" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Two-Factor Authentication (2FA)</div>
                            <div class="desc">Verifikasi dua langkah untuk login admin</div>
                        </div>
                        <div class="toggle" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Backup Database Otomatis</div>
                            <div class="desc">Backup data setiap hari secara otomatis</div>
                        </div>
                        <div class="toggle active" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="title">Log Aktivitas Admin</div>
                            <div class="desc">Catat semua aktivitas admin untuk audit</div>
                        </div>
                        <div class="toggle active" onclick="toggleSwitch(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:16px;">
                        <label>Masa Berlaku Session (jam)</label>
                        <input type="number" value="24" />
                        <div class="help-text">Waktu session akan berakhir otomatis</div>
                    </div>
                    <div class="form-group">
                        <label>IP Whitelist (pisahkan dengan koma)</label>
                        <input type="text" placeholder="192.168.1.1, 10.0.0.1" value="" />
                        <div class="help-text">Hanya IP yang terdaftar yang dapat mengakses admin</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TAB: TEMA ===== -->
        <div class="tab-content" id="tab-tema">
            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-palette"></i> Tema & Tampilan</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tema Utama</label>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;padding:8px 0;">
                            <div onclick="showToast('Tema Hijau dipilih')" style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#0a7e3b,#22b455);border:3px solid var(--primary);cursor:pointer;"></div>
                            <div onclick="showToast('Tema Biru dipilih')" style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#2980b9,#3498db);border:3px solid transparent;cursor:pointer;"></div>
                            <div onclick="showToast('Tema Ungu dipilih')" style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#8e44ad,#9b59b6);border:3px solid transparent;cursor:pointer;"></div>
                            <div onclick="showToast('Tema Oranye dipilih')" style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#e67e22,#f5a623);border:3px solid transparent;cursor:pointer;"></div>
                            <div onclick="showToast('Tema Hitam dipilih')" style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#2c3e50,#1a2a2a);border:3px solid transparent;cursor:pointer;"></div>
                        </div>
                        <div class="help-text">Klik untuk memilih warna tema utama</div>
                    </div>
                    <div class="form-group">
                        <label>Mode Tampilan</label>
                        <select>
                            <option selected>Light Mode</option>
                            <option>Dark Mode</option>
                            <option>System Default</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Logo Aplikasi</label>
                        <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                            <div style="width:80px;height:80px;background:linear-gradient(135deg,var(--primary-light),var(--primary));border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:2rem;color:white;font-weight:800;">N</div>
                            <button class="btn-secondary" style="padding:8px 20px;" onclick="showToast('Pilih file logo')">
                                <i class="fas fa-upload"></i> Upload Logo
                            </button>
                            <button class="btn-secondary" style="padding:8px 20px;" onclick="showToast('Logo direset ke default')">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                        <div class="help-text">Rekomendasi ukuran: 200x200px, format PNG/SVG</div>
                    </div>
                    <div class="form-group">
                        <label>Favicon</label>
                        <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                            <div style="width:40px;height:40px;background:linear-gradient(135deg,var(--primary-light),var(--primary));border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;color:white;font-weight:800;">N</div>
                            <button class="btn-secondary" style="padding:8px 20px;" onclick="showToast('Pilih file favicon')">
                                <i class="fas fa-upload"></i> Upload Favicon
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== SAVE BUTTONS ===== -->
        <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:12px;border-top:1px solid #e8eee8;">
            <button class="btn-cancel" onclick="showToast('Perubahan dibatalkan')">Batal</button>
            <button class="btn-save" onclick="saveAllSettings()">
                <i class="fas fa-save"></i> Simpan Semua Pengaturan
            </button>
        </div>
@endsection

@push('scripts')
<script>
        // ===== SIDEBAR TOGGLE =====
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });

        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });

        // ===== TABS =====
        const tabButtons = document.querySelectorAll('.settings-tabs button');
        const tabContents = {
            umum: document.getElementById('tab-umum'),
            admin: document.getElementById('tab-admin'),
            notifikasi: document.getElementById('tab-notifikasi'),
            integrasi: document.getElementById('tab-integrasi'),
            keamanan: document.getElementById('tab-keamanan'),
            tema: document.getElementById('tab-tema'),
        };

        tabButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                tabButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                Object.keys(tabContents).forEach(key => {
                    tabContents[key].classList.remove('active');
                });

                const tabId = this.dataset.tab;
                if (tabContents[tabId]) {
                    tabContents[tabId].classList.add('active');
                }
            });
        });

        // ===== TOGGLE SWITCH =====
        function toggleSwitch(el) {
            el.classList.toggle('active');
            const isActive = el.classList.contains('active');
            const text = isActive ? 'diaktifkan' : 'dinonaktifkan';
            showToast(`Pengaturan ${text}`);
        }

        // ===== SAVE ALL SETTINGS =====
        function saveAllSettings() {
            showToast('Semua pengaturan berhasil disimpan!');
        }

        // ===== LOGOUT =====
        function confirmLogout(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin logout?')) {
                showToast('Anda telah logout.');
                window.location.href = '#';
            }
        }

        // ===== TOAST =====
        function showToast(message, type = 'success') {
            const existing = document.querySelector('.toast-custom');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.className = 'toast-custom';
            if (type === 'error') toast.classList.add('error');

            const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
            toast.innerHTML = `
                <i class="fas ${icon}"></i>
                <span>${message}</span>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(20px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // ===== ESCAPE KEY =====
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                // Close any open modals if needed
            }
        });
</script>
@endpush
