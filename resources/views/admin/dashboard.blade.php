@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}" />
@endpush

@section('title', 'NgajiNusa - Dashboard Admin')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas dan kinerja NgajiNusa')

@section('topbar-actions')
                <div class="search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari..." id="searchInput" oninput="filterTable()" />
                </div>
                <button class="notif-btn">
                    <i class="fas fa-bell"></i>
                    <span class="badge">3</span>
                </button>
@endsection

@section('content')
        <!-- Stats -->
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-users"></i></div>
                <div class="stat-number" id="totalMurid">142</div>
                <div class="stat-label">Total Murid</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 12% dari bulan lalu</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-number" id="totalGuru">12</div>
                <div class="stat-label">Guru Aktif</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 2 guru baru</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-number" id="totalPendapatan">Rp 32,4Jt</div>
                <div class="stat-label">Pendapatan Bulan Ini</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 8% dari bulan lalu</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-clock"></i></div>
                <div class="stat-number" id="totalSesi">86</div>
                <div class="stat-label">Sesi Bulan Ini</div>
                <div class="stat-change down"><i class="fas fa-arrow-down"></i> 3% dari bulan lalu</div>
            </div>
        </div>

@include('admin.partials.section-tabs')

            <div class="data-panel">
                <div class="panel-header">
                    <h3><i class="fas fa-list" style="color:var(--primary);"></i> Aktivitas Terbaru</h3>
                    <span style="font-size:0.85rem;color:var(--text-light);">Menampilkan 5 terakhir</span>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Murid</th>
                                <th>Aktivitas</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="avatar-sm green">FA</span> Fatimah A.</td>
                                <td>Menyelesaikan sesi Tahsin</td>
                                <td><span class="status-badge completed">Selesai</span></td>
                                <td>04 Jul 2026</td>
                            </tr>
                            <tr>
                                <td><span class="avatar-sm orange">MR</span> Muhammad R.</td>
                                <td>Mendaftar paket Pro</td>
                                <td><span class="status-badge pending">Pending</span></td>
                                <td>04 Jul 2026</td>
                            </tr>
                            <tr>
                                <td><span class="avatar-sm blue">ZN</span> Zahra N.</td>
                                <td>Mengikuti sesi Tajwid</td>
                                <td><span class="status-badge active">Aktif</span></td>
                                <td>03 Jul 2026</td>
                            </tr>
                            <tr>
                                <td><span class="avatar-sm green">AS</span> Adam S.</td>
                                <td>Pembayaran paket Premium</td>
                                <td><span class="status-badge completed">Selesai</span></td>
                                <td>03 Jul 2026</td>
                            </tr>
                            <tr>
                                <td><span class="avatar-sm orange">NA</span> Nisa A.</td>
                                <td>Menunda sesi Hafalan</td>
                                <td><span class="status-badge cancelled">Dibatalkan</span></td>
                                <td>02 Jul 2026</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
@endsection

@section('modals')
    <!-- ===== MODAL ===== -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <button class="close-modal" onclick="closeModal()"><i class="fas fa-times"></i></button>
            <h3 id="modalTitle">Tambah Data</h3>
            <p class="sub" id="modalSub">Isi data di bawah ini.</p>
            <form id="modalForm" onsubmit="saveData(event)">
                <div id="modalFields">
                    <!-- Dynamic fields will be injected here -->
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
        // ===== SIDEBAR TOGGLE =====
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });

        // Close sidebar on outside click (mobile)
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });

        // ===== SEARCH / FILTER =====
        function filterTable() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.main-content tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        // ===== MODAL FUNCTIONS =====
        const modalOverlay = document.getElementById('modalOverlay');
        const modalTitle = document.getElementById('modalTitle');
        const modalSub = document.getElementById('modalSub');
        const modalFields = document.getElementById('modalFields');

        let modalMode = 'add'; // 'add' or 'edit'
        let modalType = '';

        function openAddModal(type = 'murid') {
            modalMode = 'add';
            modalType = type;
            modalTitle.textContent = `Tambah ${getTypeLabel(type)}`;
            modalSub.textContent = `Isi data ${getTypeLabel(type)} baru.`;
            renderModalFields(type);
            modalOverlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function openEditModal(type, name) {
            modalMode = 'edit';
            modalType = type;
            modalTitle.textContent = `Edit ${getTypeLabel(type)}`;
            modalSub.textContent = `Edit data ${getTypeLabel(type)}: ${name}`;
            renderModalFields(type, true);
            modalOverlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function getTypeLabel(type) {
            const labels = {
                murid: 'Murid',
                guru: 'Guru',
                jadwal: 'Jadwal',
                transaksi: 'Transaksi',
                paket: 'Paket'
            };
            return labels[type] || 'Data';
        }

        function renderModalFields(type, isEdit = false) {
            let html = '';
            switch (type) {
                case 'murid':
                    html = `
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" placeholder="Nama murid" ${isEdit ? 'value="Fatimah A."' : ''} required />
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" placeholder="email@example.com" ${isEdit ? 'value="fatimah@email.com"' : ''} required />
                        </div>
                        <div class="form-group">
                            <label>Nomor WhatsApp</label>
                            <input type="tel" placeholder="0812-3456-7890" ${isEdit ? 'value="0812-3456-7890"' : ''} required />
                        </div>
                        <div class="form-group">
                            <label>Level Belajar</label>
                            <select>
                                <option ${isEdit ? 'selected' : ''}>Hijaiyah</option>
                                <option ${isEdit ? '' : 'selected'}>Iqra</option>
                                <option>Tahsin</option>
                                <option>Tajwid</option>
                                <option>Hafalan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Paket</label>
                            <select>
                                <option ${isEdit ? 'selected' : ''}>Basic</option>
                                <option ${isEdit ? '' : 'selected'}>Pro</option>
                                <option>Premium</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select>
                                <option ${isEdit ? 'selected' : ''}>Aktif</option>
                                <option>Pending</option>
                                <option>Nonaktif</option>
                            </select>
                        </div>
                    `;
                    break;
                case 'guru':
                    html = `
                        <div class="form-group">
                            <label>Nama Guru</label>
                            <input type="text" placeholder="Nama guru" ${isEdit ? 'value="Ust. Ahmad"' : ''} required />
                        </div>
                        <div class="form-group">
                            <label>Keahlian</label>
                            <input type="text" placeholder="Tahsin, Tajwid" ${isEdit ? 'value="Tahsin, Tajwid"' : ''} required />
                        </div>
                        <div class="form-group">
                            <label>Sertifikasi</label>
                            <select>
                                <option ${isEdit ? 'selected' : ''}>Bersertifikat</option>
                                <option>Dalam Proses</option>
                                <option>Belum</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select>
                                <option ${isEdit ? 'selected' : ''}>Aktif</option>
                                <option>Libur</option>
                                <option>Nonaktif</option>
                            </select>
                        </div>
                    `;
                    break;
                case 'jadwal':
                    html = `
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" ${isEdit ? 'value="2026-07-05"' : ''} required />
                        </div>
                        <div class="form-group">
                            <label>Jam Mulai</label>
                            <input type="time" ${isEdit ? 'value="09:00"' : ''} required />
                        </div>
                        <div class="form-group">
                            <label>Jam Selesai</label>
                            <input type="time" ${isEdit ? 'value="10:00"' : ''} required />
                        </div>
                        <div class="form-group">
                            <label>Murid</label>
                            <select>
                                <option ${isEdit ? 'selected' : ''}>Fatimah A.</option>
                                <option>Muhammad R.</option>
                                <option>Zahra N.</option>
                                <option>Adam S.</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Guru</label>
                            <select>
                                <option ${isEdit ? 'selected' : ''}>Ust. Ahmad</option>
                                <option>Ustzh. Hani</option>
                                <option>Ust. Fauzi</option>
                                <option>Ustzh. Rina</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select>
                                <option ${isEdit ? 'selected' : ''}>Terjadwal</option>
                                <option>Berlangsung</option>
                                <option>Selesai</option>
                                <option>Batal</option>
                                <option>Reschedule</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Link Zoom</label>
                            <input type="url" placeholder="https://zoom.us/j/..." ${isEdit ? 'value="https://zoom.us/j/123456789"' : ''} />
                        </div>
                    `;
                    break;
                case 'paket':
                    html = `
                        <div class="form-group">
                            <label>Nama Paket</label>
                            <input type="text" placeholder="Nama paket" ${isEdit ? 'value="Pro"' : ''} required />
                        </div>
                        <div class="form-group">
                            <label>Jumlah Sesi</label>
                            <input type="number" placeholder="8" ${isEdit ? 'value="8"' : ''} required />
                        </div>
                        <div class="form-group">
                            <label>Harga (Rp)</label>
                            <input type="number" placeholder="450000" ${isEdit ? 'value="450000"' : ''} required />
                        </div>
                        <div class="form-group">
                            <label>Durasi per Sesi (menit)</label>
                            <input type="number" placeholder="60" ${isEdit ? 'value="60"' : ''} required />
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select>
                                <option ${isEdit ? 'selected' : ''}>Aktif</option>
                                <option>Nonaktif</option>
                            </select>
                        </div>
                    `;
                    break;
                default:
                    html = `<p>Form tidak tersedia.</p>`;
            }
            modalFields.innerHTML = html;
        }

        function closeModal() {
            modalOverlay.classList.remove('open');
            document.body.style.overflow = '';
        }

        // Close modal on backdrop click
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        // ===== SAVE DATA =====
        function saveData(e) {
            e.preventDefault();
            const action = modalMode === 'add' ? 'ditambahkan' : 'diperbarui';
            const type = getTypeLabel(modalType);
            showToast(`${type} berhasil ${action}!`);
            closeModal();
        }

        // ===== DELETE CONFIRM =====
        function confirmDelete(name) {
            if (confirm(`Apakah Anda yakin ingin menghapus "${name}"?`)) {
                showToast(`"${name}" berhasil dihapus!`);
            }
        }

        // ===== EXPORT =====
        function exportData() {
            showToast('Data berhasil diekspor ke Excel!');
        }

        // ===== LOGOUT =====
        function confirmLogout(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin logout?')) {
                showToast('Anda telah logout.');
                // Redirect to landing page
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
                <i class="fas fa-check-circle" style="color:var(--primary-light);"></i>
                <span>${message}</span>
            `;
            Object.assign(toast.style, {
                position: 'fixed',
                bottom: '30px',
                right: '30px',
                background: '#1a2a2a',
                color: '#ffffff',
                padding: '16px 28px',
                borderRadius: '10px',
                boxShadow: '0 12px 40px rgba(0,0,0,0.2)',
                zIndex: '3000',
                display: 'flex',
                alignItems: 'center',
                gap: '12px',
                fontWeight: '500',
                maxWidth: '400px',
                borderLeft: '4px solid #22b455',
                fontFamily: "'Inter', sans-serif",
                fontSize: '0.95rem',
                animation: 'slideUp 0.3s ease',
            });

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(20px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // Add toast animation
        const styleSheet = document.createElement('style');
        styleSheet.textContent = `
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(styleSheet);

        // ===== KEYBOARD SHORTCUT: Escape to close modal =====
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (modalOverlay.classList.contains('open')) closeModal();
            }
        });
</script>
@endpush
