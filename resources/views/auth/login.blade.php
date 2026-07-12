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
                <button type="button" class="role-btn active" data-role="admin" onclick="selectRole(this, 'admin')">
                    <i class="fas fa-user-shield"></i>
                    Admin
                </button>
                <button type="button" class="role-btn" data-role="guru" onclick="selectRole(this, 'guru')">
                    <i class="fas fa-chalkboard-teacher"></i>
                    Guru
                </button>
                <button type="button" class="role-btn" data-role="murid" onclick="selectRole(this, 'murid')">
                    <i class="fas fa-user-graduate"></i>
                    Murid
                </button>
                <button type="button" class="role-btn" data-role="orangtua" onclick="selectRole(this, 'orangtua')">
                    <i class="fas fa-users"></i>
                    Orang Tua
                </button>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert" style="margin-bottom:16px;">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label>Email atau Username <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="loginEmail" name="email" placeholder="Masukkan email Anda"
                            value="{{ old('email') }}" required />
                    </div>
                </div>
                <div class="form-group">
                    <label>Password <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="loginPassword" name="password" placeholder="Masukkan password"
                            required />
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
                        Ingat saya
                    </label>
                    <a href="#" class="forgot-link" onclick="openForgotPassword(event)">Lupa password?</a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>
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
                Kembali ke <a href="#" onclick="closeForgotPassword();return false;"
                    style="color:var(--primary);font-weight:600;">Login</a>
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
                admin: {
                    title: 'Masuk sebagai Admin',
                    sub: 'Kelola aplikasi dan semua data'
                },
                guru: {
                    title: 'Masuk sebagai Guru',
                    sub: 'Kelola jadwal dan laporan sesi'
                },
                murid: {
                    title: 'Masuk sebagai Murid',
                    sub: 'Lihat progres dan jadwal belajar'
                },
                orangtua: {
                    title: 'Masuk sebagai Orang Tua',
                    sub: 'Pantau progres anak Anda'
                }
            };

            document.getElementById('formTitle').textContent = titles[role].title;
            document.getElementById('formSubtitle').textContent = titles[role].sub;

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

        // ===== ENTER KEY SUPPORT (modal) =====
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
