@extends('layouts.app')

@section('title', 'NgajiNusa - Belajar Ngaji Online')

@section('content')

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="#" class="nav-logo">
                <i class="fas fa-quran"></i> Ngaji<span>Nusa</span>
            </a>
            <ul class="nav-links" id="navLinks">
                <li><a href="#fitur">Fitur</a></li>
                <li><a href="#paket">Paket</a></li>
                <li><a href="#dashboard">Dashboard</a></li>
                <li><a href="#testimoni">Testimoni</a></li>
                <li><a href="#" class="btn-primary" onclick="openRegister()"><i class="fas fa-user-plus"></i> Daftar</a></li>
            </ul>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- ===== HERO ===== -->
    <section class="hero" id="beranda">
        <div class="container">
            <div class="hero-content">
                <span class="badge" style="display:inline-block;background:rgba(10,126,59,0.1);color:var(--primary);padding:6px 18px;border-radius:50px;font-size:0.8rem;font-weight:600;margin-bottom:16px;">
                    <i class="fas fa-graduation-cap"></i> Kursus Online Terbaik
                </span>
                <h1>
                    Belajar Ngaji<br />
                    <span class="highlight">Mudah & Menyenangkan</span>
                </h1>
                <p>
                    Temukan guru ngaji terbaik, jadwal fleksibel, dan pantau progres
                    belajar Anda secara real-time. Mulai perjalanan mengaji dari mana saja.
                </p>
                <div class="hero-buttons">
                    <a href="#" class="btn-primary" onclick="openRegister()">
                        <i class="fas fa-play"></i> Mulai Belajar
                    </a>
                    <a href="#paket" class="btn-secondary">
                        Lihat Paket
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat">
                        <div class="number">1.000+</div>
                        <div class="label">Murid Aktif</div>
                    </div>
                    <div class="stat">
                        <div class="number">50+</div>
                        <div class="label">Guru Bersertifikasi</div>
                    </div>
                    <div class="stat">
                        <div class="number">4.9</div>
                        <div class="label">Rating Rata-rata</div>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="illustration">
                    <i class="fas fa-mosque"></i>
                    <h3>NgajiNusa</h3>
                    <p>Belajar Ngaji Online via Zoom</p>
                    <div style="margin-top:16px;display:flex;gap:12px;">
                        <span style="background:rgba(255,255,255,0.15);padding:4px 16px;border-radius:50px;font-size:0.75rem;">
                            <i class="fas fa-check"></i> Tahsin
                        </span>
                        <span style="background:rgba(255,255,255,0.15);padding:4px 16px;border-radius:50px;font-size:0.75rem;">
                            <i class="fas fa-check"></i> Tajwid
                        </span>
                        <span style="background:rgba(255,255,255,0.15);padding:4px 16px;border-radius:50px;font-size:0.75rem;">
                            <i class="fas fa-check"></i> Hafalan
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FITUR ===== -->
    <section class="features" id="fitur">
        <div class="container">
            <div class="section-header">
                <span class="badge"><i class="fas fa-star"></i> Kenapa NgajiNusa</span>
                <h2>Fitur Unggulan Kami</h2>
                <p>Semua yang Anda butuhkan untuk belajar mengaji secara efektif dan menyenangkan.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="icon"><i class="fas fa-user-graduate"></i></div>
                    <h4>Guru Bersertifikat</h4>
                    <p>Belajar dengan ustadz/ustadzah berpengalaman dan bersertifikasi.</p>
                </div>
                <div class="feature-card">
                    <div class="icon"><i class="fas fa-video"></i></div>
                    <h4>Integrasi Zoom</h4>
                    <p>Link meeting otomatis dibuat untuk setiap sesi belajar.</p>
                </div>
                <div class="feature-card">
                    <div class="icon"><i class="fas fa-calendar-check"></i></div>
                    <h4>Jadwal Fleksibel</h4>
                    <p>Atur jadwal belajar sesuai kesepakatan dengan guru.</p>
                </div>
                <div class="feature-card">
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                    <h4>Pantau Progres</h4>
                    <p>Lihat laporan perkembangan belajar setiap sesi.</p>
                </div>
                <div class="feature-card">
                    <div class="icon"><i class="fas fa-credit-card"></i></div>
                    <h4>Bayar Mudah</h4>
                    <p>Pembayaran via QRIS, transfer, atau e-wallet.</p>
                </div>
                <div class="feature-card">
                    <div class="icon"><i class="fas fa-certificate"></i></div>
                    <h4>Sertifikat</h4>
                    <p>Dapatkan sertifikat setelah menyelesaikan level tertentu.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== PAKET ===== -->
    <section class="pricing" id="paket">
        <div class="container">
            <div class="section-header">
                <span class="badge"><i class="fas fa-tags"></i> Pilihan Paket</span>
                <h2>Pilih Paket Belajar</h2>
                <p>Sesuaikan dengan kebutuhan dan target belajar Anda.</p>
            </div>
            <div class="pricing-grid">
                <!-- Paket 1 -->
                <div class="pricing-card">
                    <h4>Basic</h4>
                    <div class="price">Rp 250K <span>/ bulan</span></div>
                    <div class="sub">4 sesi per bulan</div>
                    <ul>
                        <li><i class="fas fa-check"></i> 4x pertemuan 60 menit</li>
                        <li><i class="fas fa-check"></i> Guru bersertifikat</li>
                        <li><i class="fas fa-check"></i> Laporan progres</li>
                        <li><i class="fas fa-check"></i> Link Zoom otomatis</li>
                    </ul>
                    <a href="#" class="btn-primary" onclick="openRegister()">Pilih Paket</a>
                </div>

                <!-- Paket 2 (Popular) -->
                <div class="pricing-card popular">
                    <span class="popular-badge">⭐ POPULER</span>
                    <h4>Pro</h4>
                    <div class="price">Rp 450K <span>/ bulan</span></div>
                    <div class="sub">8 sesi per bulan</div>
                    <ul>
                        <li><i class="fas fa-check"></i> 8x pertemuan 60 menit</li>
                        <li><i class="fas fa-check"></i> Guru senior bersertifikat</li>
                        <li><i class="fas fa-check"></i> Laporan progres detail</li>
                        <li><i class="fas fa-check"></i> Jadwal fleksibel</li>
                        <li><i class="fas fa-check"></i> Notifikasi WA</li>
                        <li><i class="fas fa-check"></i> Sertifikat level</li>
                    </ul>
                    <a href="#" class="btn-primary" onclick="openRegister()">Pilih Paket</a>
                </div>

                <!-- Paket 3 -->
                <div class="pricing-card">
                    <h4>Premium</h4>
                    <div class="price">Rp 800K <span>/ bulan</span></div>
                    <div class="sub">12 sesi + konsultasi</div>
                    <ul>
                        <li><i class="fas fa-check"></i> 12x pertemuan 60 menit</li>
                        <li><i class="fas fa-check"></i> Guru pilihan (tahsin/tajwid)</li>
                        <li><i class="fas fa-check"></i> Konsultasi mingguan</li>
                        <li><i class="fas fa-check"></i> Rekomendasi belajar</li>
                        <li><i class="fas fa-check"></i> Sertifikat + Piagam</li>
                        <li><i class="fas fa-check"></i> Prioritas jadwal</li>
                    </ul>
                    <a href="#" class="btn-primary" onclick="openRegister()">Pilih Paket</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== DASHBOARD PREVIEW ===== -->
    <section class="dashboard-preview" id="dashboard">
        <div class="container">
            <div class="section-header">
                <span class="badge"><i class="fas fa-chart-pie"></i> Dashboard</span>
                <h2>Pantau Semua dari Satu Tempat</h2>
                <p>Dashboard admin, guru, dan murid untuk memudahkan pengelolaan.</p>
            </div>
            <div class="dashboard-mock">
                <div class="mock-header">
                    <div class="user">
                        <div class="avatar">FI</div>
                        <div>
                            <strong>Fajarudin Irfan</strong>
                            <div style="font-size:0.8rem;opacity:0.5;">Super Admin</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:12px;">
                        <span style="background:rgba(255,255,255,0.06);padding:6px 14px;border-radius:50px;font-size:0.75rem;">
                            <i class="fas fa-bell"></i> 3
                        </span>
                        <span style="background:rgba(255,255,255,0.06);padding:6px 14px;border-radius:50px;font-size:0.75rem;">
                            <i class="fas fa-calendar"></i> Hari Ini
                        </span>
                    </div>
                </div>

                <div class="mock-stats">
                    <div class="mock-stat">
                        <div class="value">142</div>
                        <div class="label">Total Murid</div>
                    </div>
                    <div class="mock-stat">
                        <div class="value">12</div>
                        <div class="label">Guru Aktif</div>
                    </div>
                    <div class="mock-stat">
                        <div class="value">Rp 32,4Jt</div>
                        <div class="label">Pendapatan Bulan Ini</div>
                    </div>
                    <div class="mock-stat">
                        <div class="value">96%</div>
                        <div class="label">Kepuasan Murid</div>
                    </div>
                </div>

                <div class="mock-table">
                    <div class="row header">
                        <span>Murid</span>
                        <span class="hide-mobile">Guru</span>
                        <span>Status</span>
                        <span>Paket</span>
                    </div>
                    <div class="row">
                        <span>Fatimah A.</span>
                        <span class="hide-mobile">Ust. Ahmad</span>
                        <span><span class="status-badge active">Aktif</span></span>
                        <span>Pro</span>
                    </div>
                    <div class="row">
                        <span>Muhammad R.</span>
                        <span class="hide-mobile">Ustzh. Hani</span>
                        <span><span class="status-badge pending">Pending</span></span>
                        <span>Basic</span>
                    </div>
                    <div class="row">
                        <span>Zahra N.</span>
                        <span class="hide-mobile">Ust. Fauzi</span>
                        <span><span class="status-badge completed">Selesai</span></span>
                        <span>Premium</span>
                    </div>
                    <div class="row">
                        <span>Adam S.</span>
                        <span class="hide-mobile">Ustzh. Rina</span>
                        <span><span class="status-badge active">Aktif</span></span>
                        <span>Pro</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== TESTIMONIAL ===== -->
    <section class="testimonials" id="testimoni">
        <div class="container">
            <div class="section-header">
                <span class="badge"><i class="fas fa-quote-left"></i> Testimoni</span>
                <h2>Apa Kata Mereka</h2>
                <p>Pengalaman nyata dari para murid yang telah belajar di NgajiNusa.</p>
            </div>
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <blockquote>
                        "Belajar mengaji jadi lebih mudah dan terstruktur. Gurunya sabar banget, progres saya terpantau jelas."
                    </blockquote>
                    <div class="author">
                        <div class="avatar">FA</div>
                        <div>
                            <div class="name">Fatimah Azzahra</div>
                            <div class="role">Murid, Paket Pro</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <blockquote>
                        "Saya bisa belajar kapan saja, di mana saja. Fitur Zoom dan jadwal fleksibel sangat membantu."
                    </blockquote>
                    <div class="author">
                        <div class="avatar">AR</div>
                        <div>
                            <div class="name">Ahmad Rizki</div>
                            <div class="role">Murid, Paket Premium</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <blockquote>
                        "Anak saya jadi lebih semangat mengaji. Sistem pembayaran dan laporan progresnya rapi."
                    </blockquote>
                    <div class="author">
                        <div class="avatar">SN</div>
                        <div>
                            <div class="name">Siti Nurhaliza</div>
                            <div class="role">Orang Tua Murid</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="cta">
        <div class="container">
            <h2>Siap Memulai Perjalanan Ngaji?</h2>
            <p>Daftar sekarang dan dapatkan guru ngaji terbaik yang siap membimbing Anda.</p>
            <a href="#" class="btn-primary" onclick="openRegister()">
                <i class="fas fa-user-plus"></i> Daftar Sekarang
            </a>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="container">
            <div>
                <div class="brand"><i class="fas fa-quran"></i> NgajiNusa</div>
                <p style="max-width:260px;font-size:0.9rem;line-height:1.7;">
                    Platform belajar ngaji online terpercaya dengan guru bersertifikat dan metode terstruktur.
                </p>
                <div style="display:flex;gap:12px;margin-top:12px;font-size:1.2rem;">
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div>
                <h4>Layanan</h4>
                <a href="#">Tahsin</a>
                <a href="#">Tajwid</a>
                <a href="#">Hafalan</a>
                <a href="#">Iqra</a>
            </div>
            <div>
                <h4>Perusahaan</h4>
                <a href="#">Tentang Kami</a>
                <a href="#">Karir</a>
                <a href="#">Kontak</a>
            </div>
            <div>
                <h4>Bantuan</h4>
                <a href="#">FAQ</a>
                <a href="#">Syarat & Ketentuan</a>
                <a href="#">Kebijakan Privasi</a>
            </div>
            <div class="footer-bottom">
                &copy; 2026 NgajiNusa. All rights reserved. Dibuat dengan <i class="fas fa-heart" style="color:#f5a623;"></i> untuk umat.
            </div>
        </div>
    </footer>

    <!-- ===== MODAL REGISTER ===== -->
    <div id="registerModal" style="display:none;position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,0.5);backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:20px;">
        <div style="background:var(--white);max-width:520px;width:100%;border-radius:var(--radius);padding:40px 32px;box-shadow:0 40px 80px rgba(0,0,0,0.3);position:relative;max-height:90vh;overflow-y:auto;">
            <button onclick="closeRegister()" style="position:absolute;top:16px;right:20px;font-size:1.4rem;background:none;color:var(--text-light);">
                <i class="fas fa-times"></i>
            </button>
            <h3 style="font-size:1.6rem;font-weight:800;margin-bottom:8px;">
                <i class="fas fa-user-plus" style="color:var(--primary);"></i> Daftar Akun
            </h3>
            <p style="color:var(--text-gray);margin-bottom:24px;font-size:0.95rem;">Isi data diri untuk memulai belajar ngaji online.</p>

            <form id="registerForm" onsubmit="handleRegister(event)">
                <div style="margin-bottom:16px;">
                    <label style="font-weight:600;font-size:0.9rem;display:block;margin-bottom:4px;">Nama Lengkap</label>
                    <input type="text" id="regName" placeholder="Misal: Ahmad Fauzi" required
                           style="width:100%;padding:14px 16px;border:2px solid #e0e8e0;border-radius:var(--radius-sm);font-size:1rem;transition:var(--transition);">
                </div>
                <div style="margin-bottom:16px;">
                    <label style="font-weight:600;font-size:0.9rem;display:block;margin-bottom:4px;">Email</label>
                    <input type="email" id="regEmail" placeholder="email@example.com" required
                           style="width:100%;padding:14px 16px;border:2px solid #e0e8e0;border-radius:var(--radius-sm);font-size:1rem;transition:var(--transition);">
                </div>
                <div style="margin-bottom:16px;">
                    <label style="font-weight:600;font-size:0.9rem;display:block;margin-bottom:4px;">Nomor WhatsApp</label>
                    <input type="tel" id="regPhone" placeholder="0812-3456-7890" required
                           style="width:100%;padding:14px 16px;border:2px solid #e0e8e0;border-radius:var(--radius-sm);font-size:1rem;transition:var(--transition);">
                </div>
                <div style="margin-bottom:16px;">
                    <label style="font-weight:600;font-size:0.9rem;display:block;margin-bottom:4px;">Level Belajar</label>
                    <select id="regLevel" required
                            style="width:100%;padding:14px 16px;border:2px solid #e0e8e0;border-radius:var(--radius-sm);font-size:1rem;background:var(--white);">
                        <option value="">Pilih level...</option>
                        <option value="Hijaiyah">Hijaiyah (Dasar)</option>
                        <option value="Iqra">Iqra 1-6</option>
                        <option value="Tahsin">Tahsin</option>
                        <option value="Tajwid">Tajwid</option>
                        <option value="Hafalan">Hafalan</option>
                    </select>
                </div>
                <div style="margin-bottom:24px;">
                    <label style="font-weight:600;font-size:0.9rem;display:block;margin-bottom:4px;">Pilih Paket</label>
                    <select id="regPackage" required
                            style="width:100%;padding:14px 16px;border:2px solid #e0e8e0;border-radius:var(--radius-sm);font-size:1rem;background:var(--white);">
                        <option value="Basic">Basic - Rp 250K/bulan (4 sesi)</option>
                        <option value="Pro" selected>Pro - Rp 450K/bulan (8 sesi) ⭐</option>
                        <option value="Premium">Premium - Rp 800K/bulan (12 sesi)</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:16px;">
                    <i class="fas fa-paper-plane"></i> Daftar Sekarang
                </button>
            </form>

            <div id="registerSuccess" style="display:none;text-align:center;padding:20px 0;">
                <div style="font-size:3rem;color:var(--primary);margin-bottom:12px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4 style="font-size:1.2rem;font-weight:700;">Pendaftaran Berhasil!</h4>
                <p style="color:var(--text-gray);">Admin akan menghubungi Anda dalam 1x24 jam untuk verifikasi.</p>
                <button onclick="closeRegister()" class="btn-primary" style="margin-top:16px;">Tutup</button>
            </div>
        </div>
    </div>

    <!-- ===== TOAST NOTIFICATION ===== -->
    <div id="toast" style="position:fixed;bottom:30px;right:30px;background:var(--text-dark);color:var(--white);padding:16px 28px;border-radius:var(--radius-sm);box-shadow:0 12px 40px rgba(0,0,0,0.2);z-index:3000;display:none;align-items:center;gap:12px;font-weight:500;max-width:400px;border-left:4px solid var(--primary-light);">
        <i class="fas fa-check-circle" style="color:var(--primary-light);"></i>
        <span id="toastMessage">Berhasil!</span>
    </div>

@endsection

@push('scripts')
<script>
        // ===== NAVBAR TOGGLE =====
        document.getElementById('navToggle').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('open');
        });

        // Close nav on link click (mobile)
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('navLinks').classList.remove('open');
            });
        });

        // ===== MODAL =====
        function openRegister() {
            document.getElementById('registerModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            // Reset form
            document.getElementById('registerForm').style.display = 'block';
            document.getElementById('registerSuccess').style.display = 'none';
            document.getElementById('registerForm').reset();
        }

        function closeRegister() {
            document.getElementById('registerModal').style.display = 'none';
            document.body.style.overflow = '';
        }

        // Close modal on backdrop click
        document.getElementById('registerModal').addEventListener('click', function(e) {
            if (e.target === this) closeRegister();
        });

        // ===== REGISTER HANDLER =====
        function handleRegister(e) {
            e.preventDefault();

            const name = document.getElementById('regName').value.trim();
            const email = document.getElementById('regEmail').value.trim();
            const phone = document.getElementById('regPhone').value.trim();
            const level = document.getElementById('regLevel').value;
            const pkg = document.getElementById('regPackage').value;

            if (!name || !email || !phone || !level || !pkg) {
                showToast('Mohon lengkapi semua data!', 'error');
                return;
            }

            // Simulate success
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('registerSuccess').style.display = 'block';

            // Log data (for demo)
            console.log('Registrasi:', { name, email, phone, level, pkg });

            showToast(`Pendaftaran ${name} berhasil! Admin akan menghubungi Anda.`);
        }

        // ===== TOAST =====
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMessage');
            toastMsg.textContent = message;
            toast.style.display = 'flex';

            if (type === 'error') {
                toast.style.borderLeftColor = '#e74c3c';
                toast.querySelector('i').style.color = '#e74c3c';
                toast.querySelector('i').className = 'fas fa-exclamation-circle';
            } else {
                toast.style.borderLeftColor = 'var(--primary-light)';
                toast.querySelector('i').style.color = 'var(--primary-light)';
                toast.querySelector('i').className = 'fas fa-check-circle';
            }

            clearTimeout(toast._timer);
            toast._timer = setTimeout(() => {
                toast.style.display = 'none';
            }, 4000);
        }

        // ===== SCROLL ANIMATION (simple) =====
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // ===== NAVBAR SHADOW ON SCROLL =====
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 20) {
                navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.06)';
            } else {
                navbar.style.boxShadow = 'none';
            }
        });
</script>
@endpush
