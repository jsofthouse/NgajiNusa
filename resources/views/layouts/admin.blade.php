<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'NgajiNusa - Dashboard Admin')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    {{-- Setiap halaman admin push CSS masing-masing (lihat catatan di bawah) --}}
    @stack('styles')
</head>
<body>

    {{-- ===== SIDEBAR ===== --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-quran"></i>
            <div>
                <span>NgajiNusa</span>
                <small>Dashboard Admin</small>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-label">Main Menu</li>
            <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.murid') }}" class="{{ request()->routeIs('admin.murid') ? 'active' : '' }}"><i class="fas fa-user-graduate"></i> Murid</a></li>
            <li><a href="{{ route('admin.guru') }}" class="{{ request()->routeIs('admin.guru') ? 'active' : '' }}"><i class="fas fa-chalkboard-teacher"></i> Guru</a></li>
            <li><a href="{{ route('admin.jadwal') }}" class="{{ request()->routeIs('admin.jadwal') ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Jadwal</a></li>
            <li><a href="{{ route('admin.transaksi') }}" class="{{ request()->routeIs('admin.transaksi') ? 'active' : '' }}"><i class="fas fa-credit-card"></i> Transaksi</a></li>
            <li><a href="{{ route('admin.paket') }}" class="{{ request()->routeIs('admin.paket') ? 'active' : '' }}"><i class="fas fa-tags"></i> Paket</a></li>
            <li><a href="{{ route('admin.referral-agent.index') }}" class="{{ request()->routeIs('admin.referral-agent.*') ? 'active' : '' }}"><i class="fas fa-user-tie"></i> Referral Agent</a></li>
            <li class="menu-label">Lainnya</li>
            <li><a href="{{ route('admin.laporan') }}" class="{{ request()->routeIs('admin.laporan') ? 'active' : '' }}"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li><a href="{{ route('admin.pengaturan') }}" class="{{ request()->routeIs('admin.pengaturan') ? 'active' : '' }}"><i class="fas fa-cog"></i> Pengaturan</a></li>
        </ul>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="avatar">{{ auth()->check() ? Str::of(auth()->user()->name)->explode(' ')->map(fn($w) => Str::substr($w, 0, 1))->take(2)->join('') : 'FI' }}</div>
                <div>
                    <div class="name">{{ auth()->user()->name ?? 'Fajarudin Irfan' }}</div>
                    <div class="role">{{ auth()->user()->role ?? 'Super Admin' }}</div>
                </div>
            </div>
            <a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="main-content">
        {{-- Top Bar --}}
        <div class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 id="pageTitle">@yield('page-title', 'Dashboard')</h1>
                <p id="pageSubtitle">@yield('page-subtitle', 'Ringkasan aktivitas dan kinerja NgajiNusa')</p>
            </div>
            <div class="topbar-right">
                @yield('topbar-actions')
            </div>
        </div>

        @yield('content')
    </main>

    {{-- Modal overlay ditempatkan di luar <main> (posisinya fixed/overlay) --}}
    @yield('modals')

    {{-- ===== LOGOUT FORM (hidden, submits real POST /logout) ===== --}}
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <script>
        // ===== LOGOUT ===== (satu-satunya definisi, dipakai semua halaman admin)
        function confirmLogout(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin logout?')) {
                document.getElementById('logoutForm').submit();
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
