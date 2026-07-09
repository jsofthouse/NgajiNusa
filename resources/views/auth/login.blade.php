@extends('layouts.auth')

@section('title', 'NgajiNusa - Login & Autentikasi')

@section('content')

    <!-- ===== AUTH CONTAINER ===== -->
    <div class="auth-container">
        <!-- Left Panel -->
        <div class="auth-brand">
            <div class="brand-content">
                <div class="logo">
                    <i class="fas fa-quran"></i>
                    <span>NgajiNusa</span>
                </div>
                <h2>Belajar Ngaji <br />Online Terpercaya</h2>
                <p>Platform belajar mengaji dengan guru bersertifikat dan metode terstruktur.</p>
                <div class="features">
                    <div class="item">
                        <i class="fas fa-check-circle"></i>
                        <span>Guru bersertifikat</span>
                    </div>
                    <div class="item">
                        <i class="fas fa-check-circle"></i>
                        <span>Jadwal fleksibel</span>
                    </div>
                    <div class="item">
                        <i class="fas fa-check-circle"></i>
                        <span>Integrasi Zoom</span>
                    </div>
                    <div class="item">
                        <i class="fas fa-check-circle"></i>
                        <span>Pantau progres belajar</span>
                    </div>
                </div>
                <div class="footer-text">© 2026 NgajiNusa. All rights reserved.</div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="auth-form">
            <div class="form-header">
                <h3 id="formTitle">Masuk ke Akun</h3>
                <p id="formSubtitle">Pilih peran dan masuk ke dashboard Anda</p>
            </div>

            <!-- Role Selector -->
            <div class="role-selector" id="roleSelector">
                <button class="role-btn active" data-role="admin" onclick="selectRole(this, 'admin')">
                    <i class="fas fa-user-shield"></i>
                    Admin
                </button>
                <button class="role-btn" data-role="guru" onclick="selectRole(this, 'guru')">
                    <i class="fas fa-chalkboard-teacher"></i>
                    Guru
                </button>
                <button class="role-btn" data-role="murid" onclick="selectRole(this, 'murid')">
                    <i class="fas fa-user-graduate"></i>
                    Murid
                </button>
                <button class="role-btn" data-role="orangtua" onclick="selectRole(this, 'orangtua')">
                    <i class="fas fa-users"></i>
                    Orang Tua
                </button>
            </div>

            <!-- Login Form -->
            <form id="loginForm" onsubmit="handleLogin(event)">
                <div class="form-group">
                    <label>Email atau Username <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="loginEmail" placeholder="Masukkan email Anda" value="admin@ngajinusa.com" required />
                    </div>
                </div>
                <div class="form-group">
                    <label>Password <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="loginPassword" placeholder="Masukkan password" value="password123" required />
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember">
                        <input type="checkbox" checked /> Ingat saya
                    </label>
                    <a href="#" class="forgot-link" onclick="openForgotPassword(event)">Lupa password?</a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            <div class="auth-divider">atau masuk dengan</div>

            <div class="social-login">
                <button class="google" onclick="showToast('Login dengan Google')">
                    <i class="fab fa-google"></i> Google
                </button>
                <button onclick="showToast('Login dengan WhatsApp')">
                    <i class="fab fa-whatsapp" style="color:#25d366;"></i> WhatsApp
                </button>
            </div>

            <div class="auth-footer">
                Belum punya akun? <a href="#" onclick="openRegister(event)">Daftar sekarang</a>
            </div>
        </div>
    </div>

    <!-- ===== FORGOT PASSWORD MODAL ===== -->
    <div class="modal-overlay" id="forgotModal">
        <div class="modal">
            <button class="close-modal" onclick="closeForgotPassword()"><i class="fas fa-times"></i></button>
            <div class="modal-icon"><i class="fas fa-key"></i></div>
            <h3>Lupa Password?</h3>
            <p class="sub">Masukkan email Anda, kami akan kirimkan link reset password.</p>
            <form onsubmit="handleForgotPassword(event)">
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" placeholder="Masukkan email Anda" required />
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Kirim Link Reset
                </button>
            </form>
            <div style="text-align:center;margin-top:14px;font-size:0.85rem;color:var(--text-gray);">
                Kembali ke <a href="#" onclick="closeForgotPassword();return false;" style="color:var(--primary);font-weight:600;">Login</a>
            </div>
        </div>
    </div>

    <!-- ===== REGISTER MODAL ===== -->
    <div class="modal-overlay register-modal" id="registerModal">
        <div class="modal">
            <button class="close-modal" onclick="closeRegister()"><i class="fas fa-times"></i></button>
            <div class="modal-icon" style="font-size:2.4rem;"><i class="fas fa-user-plus"></i></div>
            <h3>Daftar Akun</h3>
            <p class="sub">Isi data diri untuk mulai belajar ngaji online.</p>

            <form onsubmit="handleRegister(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" placeholder="Nama lengkap" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Usia <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="number" placeholder="Usia" required />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" placeholder="email@example.com" required />
                    </div>
                </div>
                <div class="form-group">
                    <label>Nomor WhatsApp <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fab fa-whatsapp"></i>
                        <input type="tel" placeholder="0812-3456-7890" required />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Level Belajar <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-graduation-cap"></i>
                            <select style="width:100%;padding:12px 16px 12px 44px;border:2px solid #e0e8e0;border-radius:var(--radius-sm);font-size:0.95rem;font-family:inherit;background:var(--white);appearance:none;">
                                <option value="">Pilih level...</option>
                                <option>Hijaiyah (Dasar)</option>
                                <option>Iqra 1-6</option>
                                <option>Tahsin</option>
                                <option>Tajwid</option>
                                <option>Hafalan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Pilih Paket <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-tags"></i>
                            <select style="width:100%;padding:12px 16px 12px 44px;border:2px solid #e0e8e0;border-radius:var(--radius-sm);font-size:0.95rem;font-family:inherit;background:var(--white);appearance:none;">
                                <option>Basic - Rp 250K/bulan</option>
                                <option selected>Pro - Rp 450K/bulan ⭐</option>
                                <option>Premium - Rp 800K/bulan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" placeholder="Minimal 6 karakter" required />
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Daftar Sekarang
                </button>
            </form>

            <div style="text-align:center;margin-top:14px;font-size:0.85rem;color:var(--text-gray);">
                Sudah punya akun? <a href="#" onclick="closeRegister();return false;" style="color:var(--primary);font-weight:600;">Login</a>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
        // ===== ROLE SELECTOR =====
        let selectedRole = 'admin';

        function selectRole(el, role) {
            document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
            el.classList.add('active');
            selectedRole = role;

            const titles = {
                admin: { title: 'Masuk sebagai Admin', sub: 'Kelola aplikasi dan semua data' },
                guru: { title: 'Masuk sebagai Guru', sub: 'Kelola jadwal dan laporan sesi' },
                murid: { title: 'Masuk sebagai Murid', sub: 'Lihat progres dan jadwal belajar' },
                orangtua: { title: 'Masuk sebagai Orang Tua', sub: 'Pantau progres anak Anda' }
            };

            document.getElementById('formTitle').textContent = titles[role].title;
            document.getElementById('formSubtitle').textContent = titles[role].sub;

            // Auto-fill email based on role
            const emails = {
                admin: 'admin@ngajinusa.com',
                guru: 'guru@ngajinusa.com',
                murid: 'murid@ngajinusa.com',
                orangtua: 'orangtua@ngajinusa.com'
            };
            document.getElementById('loginEmail').value = emails[role];

            showToast(`Mode: ${titles[role].title}`);
        }

        // ===== TOGGLE PASSWORD =====
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('loginPassword');
            const icon = document.getElementById('passwordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        // ===== LOGIN HANDLER =====
        function handleLogin(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            if (!email || !password) {
                showToast('Mohon isi email dan password!', 'error');
                return;
            }

            // Simulate login
            showToast(`Login berhasil! Selamat datang ${email}`);

            // Redirect based on role (simulated)
            setTimeout(() => {
                const roleNames = {
                    admin: 'Dashboard Admin',
                    guru: 'Dashboard Guru',
                    murid: 'Dashboard Murid',
                    orangtua: 'Dashboard Orang Tua'
                };
                showToast(`Dialihkan ke ${roleNames[selectedRole]}`);
            }, 1500);
        }

        // ===== FORGOT PASSWORD =====
        function openForgotPassword(e) {
            e.preventDefault();
            document.getElementById('forgotModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeForgotPassword() {
            document.getElementById('forgotModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        function handleForgotPassword(e) {
            e.preventDefault();
            showToast('Link reset password telah dikirim ke email Anda!');
            closeForgotPassword();
        }

        // ===== REGISTER =====
        function openRegister(e) {
            e.preventDefault();
            document.getElementById('registerModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeRegister() {
            document.getElementById('registerModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        function handleRegister(e) {
            e.preventDefault();
            showToast('Pendaftaran berhasil! Silakan cek email untuk verifikasi.');
            closeRegister();
        }

        // ===== CLOSE MODALS ON BACKDROP =====
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('open');
                    document.body.style.overflow = '';
                }
            });
        });

        // ===== ESCAPE KEY =====
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.open').forEach(modal => {
                    modal.classList.remove('open');
                    document.body.style.overflow = '';
                });
            }
        });

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

        // ===== ENTER KEY SUPPORT =====
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const activeModal = document.querySelector('.modal-overlay.open');
                if (activeModal) {
                    const form = activeModal.querySelector('form');
                    if (form) {
                        e.preventDefault();
                        form.dispatchEvent(new Event('submit'));
                    }
                }
            }
        });
</script>
@endpush
