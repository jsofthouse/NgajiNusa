@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-laporan.css') }}" />
@endpush

@section('title', 'NgajiNusa - Laporan & Analitik')

@section('page-title', '📊 Laporan & Analitik')
@section('page-subtitle', 'Analisis kinerja dan perkembangan NgajiNusa')

@section('topbar-actions')
                <div class="filter-group">
                    <i class="fas fa-calendar-alt"></i>
                    <select>
                        <option>Bulan Ini</option>
                        <option>Bulan Lalu</option>
                        <option>3 Bulan Terakhir</option>
                        <option>Tahun Ini</option>
                        <option>Kustom</option>
                    </select>
                </div>
                <button class="btn-secondary" onclick="showToast('Laporan berhasil diekspor ke Excel!')">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button class="btn-secondary" onclick="showToast('Laporan berhasil diekspor ke PDF!')">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <button class="btn-primary" onclick="showToast('Laporan berhasil diperbarui!')">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
@endsection

@section('content')
        <!-- Period Filter -->
        <div class="period-filter">
            <button class="active" onclick="setPeriod(this, 'hari')">Hari Ini</button>
            <button onclick="setPeriod(this, 'minggu')">Minggu Ini</button>
            <button onclick="setPeriod(this, 'bulan')">Bulan Ini</button>
            <button onclick="setPeriod(this, '3bulan')">3 Bulan</button>
            <button onclick="setPeriod(this, 'tahun')">Tahun Ini</button>
            <button onclick="setPeriod(this, 'kustom')">Kustom</button>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-users"></i></div>
                <div class="stat-number">142</div>
                <div class="stat-label">Total Murid</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 12% dari bulan lalu</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-number">12</div>
                <div class="stat-label">Guru Aktif</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 2 guru baru</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-number">Rp 32,4Jt</div>
                <div class="stat-label">Pendapatan Bulan Ini</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 8% dari bulan lalu</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-clock"></i></div>
                <div class="stat-number">86</div>
                <div class="stat-label">Total Sesi</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 5% dari bulan lalu</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-star"></i></div>
                <div class="stat-number">4.9</div>
                <div class="stat-label">Rating Rata-rata</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 0.2 poin</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-percent"></i></div>
                <div class="stat-number">92%</div>
                <div class="stat-label">Tingkat Kehadiran</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 3% dari bulan lalu</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <!-- Bar Chart -->
            <div class="chart-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-column"></i> Sesi per Bulan</h3>
                    <div class="legend">
                        <span><span class="dot" style="background:var(--primary);"></span> Selesai</span>
                        <span><span class="dot" style="background:var(--secondary);"></span> Terjadwal</span>
                        <span><span class="dot" style="background:var(--blue);"></span> Batal</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="bar-chart">
                        <div class="bar-item">
                            <div class="value">28</div>
                            <div class="bar" style="height:80px;"></div>
                            <div class="label">Jan</div>
                        </div>
                        <div class="bar-item">
                            <div class="value">32</div>
                            <div class="bar" style="height:92px;"></div>
                            <div class="label">Feb</div>
                        </div>
                        <div class="bar-item">
                            <div class="value">35</div>
                            <div class="bar" style="height:100px;"></div>
                            <div class="label">Mar</div>
                        </div>
                        <div class="bar-item">
                            <div class="value">30</div>
                            <div class="bar orange" style="height:86px;"></div>
                            <div class="label">Apr</div>
                        </div>
                        <div class="bar-item">
                            <div class="value">40</div>
                            <div class="bar blue" style="height:114px;"></div>
                            <div class="label">Mei</div>
                        </div>
                        <div class="bar-item">
                            <div class="value">45</div>
                            <div class="bar purple" style="height:128px;"></div>
                            <div class="label">Jun</div>
                        </div>
                        <div class="bar-item">
                            <div class="value">38</div>
                            <div class="bar" style="height:108px;"></div>
                            <div class="label">Jul</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donut Chart -->
            <div class="chart-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie"></i> Distribusi Paket</h3>
                </div>
                <div class="card-body">
                    <div class="donut-container">
                        <div class="donut"></div>
                        <div class="donut-legend">
                            <div class="item"><span class="dot" style="background:var(--primary);"></span> Basic 45%</div>
                            <div class="item"><span class="dot" style="background:var(--secondary);"></span> Pro 25%</div>
                            <div class="item"><span class="dot" style="background:var(--blue);"></span> Premium 18%</div>
                            <div class="item"><span class="dot" style="background:var(--purple);"></span> Trial 12%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid 2: Performance & Revenue -->
        <div class="grid-2">
            <!-- Guru Performance -->
            <div class="chart-card">
                <div class="card-header">
                    <h3><i class="fas fa-trophy"></i> Kinerja Guru Terbaik</h3>
                    <span style="font-size:0.8rem;color:var(--text-light);">Rating tertinggi</span>
                </div>
                <div class="card-body" style="padding:0;">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Guru</th>
                                    <th>Sesi</th>
                                    <th>Rating</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Ust. Ahmad</td>
                                    <td>48</td>
                                    <td>⭐ 4.9</td>
                                    <td><span class="trend-up"><i class="fas fa-arrow-up"></i> +2%</span></td>
                                </tr>
                                <tr>
                                    <td>Ustzh. Rina</td>
                                    <td>42</td>
                                    <td>⭐ 4.8</td>
                                    <td><span class="trend-up"><i class="fas fa-arrow-up"></i> +5%</span></td>
                                </tr>
                                <tr>
                                    <td>Ustzh. Hani</td>
                                    <td>36</td>
                                    <td>⭐ 4.7</td>
                                    <td><span class="trend-up"><i class="fas fa-arrow-up"></i> +3%</span></td>
                                </tr>
                                <tr>
                                    <td>Ust. Fauzi</td>
                                    <td>28</td>
                                    <td>⭐ 4.5</td>
                                    <td><span class="trend-down"><i class="fas fa-arrow-down"></i> -1%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Revenue Breakdown -->
            <div class="chart-card">
                <div class="card-header">
                    <h3><i class="fas fa-coins"></i> Pendapatan per Paket</h3>
                    <span style="font-size:0.8rem;color:var(--text-light);">Bulan Ini</span>
                </div>
                <div class="card-body" style="padding:0;">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Paket</th>
                                    <th>Jumlah</th>
                                    <th>Pendapatan</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Premium</td>
                                    <td>12</td>
                                    <td>Rp 9.600.000</td>
                                    <td><span class="trend-up">30%</span></td>
                                </tr>
                                <tr>
                                    <td>Pro</td>
                                    <td>32</td>
                                    <td>Rp 14.400.000</td>
                                    <td><span class="trend-up">44%</span></td>
                                </tr>
                                <tr>
                                    <td>Basic</td>
                                    <td>28</td>
                                    <td>Rp 7.000.000</td>
                                    <td><span class="trend-up">22%</span></td>
                                </tr>
                                <tr>
                                    <td>Trial</td>
                                    <td>8</td>
                                    <td>Rp 1.200.000</td>
                                    <td><span class="trend-down">4%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="chart-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Transaksi Terbaru</h3>
                <div>
                    <button class="btn-secondary" style="padding:6px 16px;font-size:0.75rem;" onclick="showToast('Semua transaksi ditampilkan!')">
                        Lihat Semua
                    </button>
                </div>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Murid</th>
                                <th>Paket</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#INV-049</td>
                                <td>Fatimah A.</td>
                                <td>Pro</td>
                                <td>Rp 450.000</td>
                                <td><span class="status-badge pending">Menunggu</span></td>
                                <td>10 Jul 2026</td>
                            </tr>
                            <tr>
                                <td>#INV-048</td>
                                <td>Adam S.</td>
                                <td>Premium</td>
                                <td>Rp 800.000</td>
                                <td><span class="status-badge completed">Lunas</span></td>
                                <td>09 Jul 2026</td>
                            </tr>
                            <tr>
                                <td>#INV-047</td>
                                <td>Zahra N.</td>
                                <td>Basic</td>
                                <td>Rp 250.000</td>
                                <td><span class="status-badge completed">Lunas</span></td>
                                <td>08 Jul 2026</td>
                            </tr>
                            <tr>
                                <td>#INV-046</td>
                                <td>Muhammad R.</td>
                                <td>Pro</td>
                                <td>Rp 450.000</td>
                                <td><span class="status-badge active">Diproses</span></td>
                                <td>07 Jul 2026</td>
                            </tr>
                            <tr>
                                <td>#INV-045</td>
                                <td>Nisa A.</td>
                                <td>Premium</td>
                                <td>Rp 800.000</td>
                                <td><span class="status-badge completed">Lunas</span></td>
                                <td>06 Jul 2026</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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

        // ===== PERIOD FILTER =====
        function setPeriod(btn, period) {
            document.querySelectorAll('.period-filter button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            showToast(`Menampilkan data: ${btn.textContent}`);
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
        function showToast(message) {
            const existing = document.querySelector('.toast-custom');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.className = 'toast-custom';
            toast.innerHTML = `
                <i class="fas fa-check-circle" style="color:#22b455;"></i>
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
</script>
@endpush
