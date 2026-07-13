@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-transaksi.css') }}?v={{ @filemtime(public_path('css/admin-transaksi.css')) ?: '1' }}" />
@endpush

@section('title', 'NgajiNusa - Manajemen Transaksi')

@section('page-title', 'Manajemen Transaksi')
@section('page-subtitle', 'Kelola dan verifikasi pembayaran pendaftaran murid')

@section('content')

        <!-- ===== DASHBOARD SUMMARY ===== -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock" style="color:var(--secondary);"></i></div>
                <div class="stat-number" id="statMenungguPembayaran">{{ $summary['menunggu_pembayaran'] }}</div>
                <div class="stat-label">Total Menunggu Pembayaran</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-spinner" style="color:var(--blue);"></i></div>
                <div class="stat-number" id="statMenungguVerifikasi">{{ $summary['menunggu_verifikasi'] }}</div>
                <div class="stat-label">Total Menunggu Verifikasi</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle" style="color:var(--primary-light);"></i></div>
                <div class="stat-number" id="statBerhasil">{{ $summary['berhasil'] }}</div>
                <div class="stat-label">Total Pembayaran Berhasil</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-coins" style="color:var(--primary);"></i></div>
                <div class="stat-number" id="statPendapatanBulanIni">Rp {{ number_format($summary['pendapatan_bulan_ini'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Pendapatan Bulan Ini</div>
            </div>
        </div>

        <!-- ===== TAB FILTER ===== -->
        <div class="trans-tabs" id="transaksiTabs">
            <button class="{{ $filters['status'] === 'semua' ? 'active' : '' }}" data-status="semua">
                <i class="fas fa-list"></i> Semua
                <span class="badge">{{ $counts['semua'] }}</span>
            </button>
            <button class="{{ $filters['status'] === 'menunggu_pembayaran' ? 'active' : '' }}" data-status="menunggu_pembayaran">
                <i class="fas fa-clock"></i> Menunggu Pembayaran
                <span class="badge">{{ $counts['menunggu_pembayaran'] }}</span>
            </button>
            <button class="{{ $filters['status'] === 'menunggu_verifikasi' ? 'active' : '' }}" data-status="menunggu_verifikasi">
                <i class="fas fa-spinner"></i> Menunggu Verifikasi
                <span class="badge">{{ $counts['menunggu_verifikasi'] }}</span>
            </button>
            <button class="{{ $filters['status'] === 'berhasil' ? 'active' : '' }}" data-status="berhasil">
                <i class="fas fa-check"></i> Berhasil
                <span class="badge">{{ $counts['berhasil'] }}</span>
            </button>
            <button class="{{ $filters['status'] === 'ditolak' ? 'active' : '' }}" data-status="ditolak">
                <i class="fas fa-times"></i> Ditolak
                <span class="badge">{{ $counts['ditolak'] }}</span>
            </button>
        </div>

        <!-- ===== FILTER BAR ===== -->
        <div class="filter-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari invoice, nama murid, atau nomor WA..." value="{{ $filters['search'] }}" />
            </div>
            <select id="filterPaket">
                <option value="">Semua Paket</option>
                @foreach (\App\Models\Murid::PAKET_OPTIONS as $paket)
                    <option value="{{ $paket }}" {{ $filters['paket'] === $paket ? 'selected' : '' }}>{{ $paket }}</option>
                @endforeach
            </select>
            <select id="filterMetode">
                <option value="">Semua Metode</option>
                @foreach (\App\Models\Transaksi::METODE_OPTIONS as $metode)
                    <option value="{{ $metode }}" {{ $filters['metode_pembayaran'] === $metode ? 'selected' : '' }}>{{ \App\Models\Transaksi::METODE_LABELS[$metode] }}</option>
                @endforeach
            </select>
            <input type="date" id="filterDateFrom" title="Dari tanggal" value="{{ $filters['date_from'] }}" />
            <input type="date" id="filterDateTo" title="Sampai tanggal" value="{{ $filters['date_to'] }}" />
        </div>

        <!-- ===== TABEL TRANSAKSI ===== -->
        <div id="transaksiListContainer">
            @include('admin.partials.transaksi-list', ['transaksiList' => $transaksiList])
        </div>
@endsection

@section('modals')
    <!-- ===== MODAL DETAIL TRANSAKSI ===== -->
    <div class="modal-overlay" id="detailModalOverlay">
        <div class="modal modal-lg">
            <button class="close-modal" onclick="closeDetailModal()"><i class="fas fa-times"></i></button>

            <div class="invoice-header">
                <div>
                    <div class="title"><i class="fas fa-file-invoice" style="margin-right:8px;"></i> Detail Transaksi</div>
                    <div class="sub" id="detailInvoiceNumber">-</div>
                </div>
                <span class="status-badge" id="detailStatusBadge">-</span>
            </div>

            <h4 class="detail-section-title"><i class="fas fa-user"></i> Informasi Murid</h4>
            <div class="detail-list">
                <div class="detail-row"><span>Nama</span><strong id="detailNamaMurid">-</strong></div>
                <div class="detail-row"><span>Nomor WhatsApp</span><strong id="detailWhatsappMurid">-</strong></div>
                <div class="detail-row"><span>Email</span><strong id="detailEmailMurid">-</strong></div>
                <div class="detail-row"><span>Paket</span><strong id="detailPaketMurid">-</strong></div>
            </div>

            <h4 class="detail-section-title"><i class="fas fa-receipt"></i> Informasi Transaksi</h4>
            <div class="detail-list">
                <div class="detail-row"><span>Invoice</span><strong id="detailInvoice">-</strong></div>
                <div class="detail-row"><span>Nominal</span><strong id="detailNominal">-</strong></div>
                <div class="detail-row"><span>Metode Pembayaran</span><strong id="detailMetode">-</strong></div>
                <div class="detail-row"><span>Status</span><strong id="detailStatusText">-</strong></div>
                <div class="detail-row"><span>Waktu Daftar</span><strong id="detailWaktuDaftar">-</strong></div>
                <div class="detail-row"><span>Waktu Verifikasi</span><strong id="detailWaktuVerifikasi">-</strong></div>
                <div class="detail-row"><span>Admin Verifikator</span><strong id="detailAdminVerifikator">-</strong></div>
            </div>

            <h4 class="detail-section-title"><i class="fas fa-sticky-note"></i> Catatan Internal Admin</h4>
            <p class="detail-hint">Catatan ini cuma bisa dilihat admin, tidak terlihat oleh murid.</p>
            <textarea id="detailCatatanInput" class="catatan-textarea" rows="3" placeholder="Belum ada catatan..."></textarea>
            <div class="catatan-actions">
                <button type="button" class="btn-secondary" id="btnSimpanCatatan" onclick="saveCatatan()">
                    <i class="fas fa-save"></i> Simpan Catatan
                </button>
            </div>

            <!-- ===== BUKTI TRANSFER (hanya tampil kalau sudah berhasil) ===== -->
            <div id="buktiTransferSection" style="display:none;">
                <h4 class="detail-section-title"><i class="fas fa-image"></i> Bukti Transfer</h4>
                <div class="bukti-preview-box">
                    <img id="buktiPreviewImg" src="" alt="Bukti Transfer" />
                </div>
                <div class="bukti-actions">
                    <a href="#" id="btnLihatBukti" target="_blank" class="btn-secondary">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                    <a href="#" id="btnDownloadBukti" class="btn-secondary">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            </div>

            <h4 class="detail-section-title"><i class="fas fa-history"></i> Histori Aktivitas</h4>
            <div id="activityTimeline" class="activity-timeline"></div>

            <!-- ===== AKSI TRANSAKSI ===== -->
            <div class="detail-final-actions" id="detailFinalActions" style="display:none;">
                <button type="button" class="btn-danger" onclick="rejectCurrentTransaksi()">
                    <i class="fas fa-times"></i> Tolak
                </button>
                <button type="button" class="btn-primary" onclick="openVerifyModalFromDetail()">
                    <i class="fas fa-check"></i> Verifikasi Pembayaran
                </button>
            </div>
        </div>
    </div>

    <!-- ===== MODAL VERIFIKASI PEMBAYARAN ===== -->
    <div class="modal-overlay" id="verifyModalOverlay">
        <div class="modal">
            <button class="close-modal" onclick="closeVerifyModal()"><i class="fas fa-times"></i></button>
            <h3><i class="fas fa-check-circle" style="color:var(--primary);"></i> Verifikasi Pembayaran</h3>
            <p class="sub">Unggah ulang bukti transfer sebagai dokumentasi verifikasi.</p>

            <div class="detail-list" style="margin-bottom:16px;">
                <div class="detail-row"><span>Invoice</span><strong id="verifyInvoice">-</strong></div>
                <div class="detail-row"><span>Nama Murid</span><strong id="verifyNama">-</strong></div>
                <div class="detail-row"><span>Paket</span><strong id="verifyPaket">-</strong></div>
                <div class="detail-row"><span>Nominal</span><strong id="verifyNominal">-</strong></div>
            </div>

            <form id="verifyForm">
                <div class="form-group">
                    <label>Upload Bukti Transfer <span style="color:var(--danger);">*</span></label>
                    <input type="file" id="verifyBuktiInput" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" required />
                    <img id="verifyPreviewImg" style="display:none;" alt="Preview bukti transfer" />
                </div>
                <div class="form-group">
                    <label>Catatan Internal Admin (opsional)</label>
                    <textarea id="verifyCatatanInput" rows="3" placeholder="Catatan tambahan..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeVerifyModal()">Batal</button>
                    <button type="submit" class="btn-submit" id="btnVerifySubmit"><i class="fas fa-save"></i> Simpan &amp; Verifikasi</button>
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
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        if (window.innerWidth <= 992) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        }
    });

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const transaksiUrl = "{{ url('admin/transaksi') }}";

    const STATUS_LABELS = @json(\App\Models\Transaksi::STATUS_LABELS);
    const METODE_LABELS = @json(\App\Models\Transaksi::METODE_LABELS);

    // ===== LIST: FILTER + SEARCH + PAGINATION (async) =====
    let currentPage = {{ $transaksiList->currentPage() }};
    let currentStatus = @json($filters['status']);
    let currentSearch = @json($filters['search']);
    let searchDebounce = null;

    const listContainer = document.getElementById('transaksiListContainer');
    const searchInput = document.getElementById('searchInput');
    const filterPaket = document.getElementById('filterPaket');
    const filterMetode = document.getElementById('filterMetode');
    const filterDateFrom = document.getElementById('filterDateFrom');
    const filterDateTo = document.getElementById('filterDateTo');
    const tabButtons = document.querySelectorAll('#transaksiTabs button');

    function updateSummaryCards(summary) {
        document.getElementById('statMenungguPembayaran').textContent = summary.menunggu_pembayaran;
        document.getElementById('statMenungguVerifikasi').textContent = summary.menunggu_verifikasi;
        document.getElementById('statBerhasil').textContent = summary.berhasil;
        document.getElementById('statPendapatanBulanIni').textContent = 'Rp ' + Number(summary.pendapatan_bulan_ini).toLocaleString('id-ID');
    }

    function updateTabCounts(counts) {
        tabButtons.forEach(btn => {
            const badge = btn.querySelector('.badge');
            if (badge) badge.textContent = counts[btn.dataset.status] ?? 0;
        });
    }

    function loadList(page = 1) {
        currentPage = page;
        listContainer.style.opacity = '0.5';
        listContainer.style.pointerEvents = 'none';

        const url = new URL(transaksiUrl, window.location.origin);
        if (currentSearch) url.searchParams.set('search', currentSearch);
        url.searchParams.set('status', currentStatus);
        if (filterPaket.value) url.searchParams.set('paket', filterPaket.value);
        if (filterMetode.value) url.searchParams.set('metode_pembayaran', filterMetode.value);
        if (filterDateFrom.value) url.searchParams.set('date_from', filterDateFrom.value);
        if (filterDateTo.value) url.searchParams.set('date_to', filterDateTo.value);
        url.searchParams.set('page', page);

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                listContainer.innerHTML = data.html;
                updateSummaryCards(data.summary);
                updateTabCounts(data.counts);
            })
            .catch(() => showToast('Gagal memuat data transaksi.', 'error'))
            .finally(() => {
                listContainer.style.opacity = '1';
                listContainer.style.pointerEvents = '';
            });
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => {
            currentSearch = this.value.trim();
            loadList(1);
        }, 400);
    });

    [filterPaket, filterMetode, filterDateFrom, filterDateTo].forEach(el => {
        el.addEventListener('change', () => loadList(1));
    });

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            tabButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentStatus = this.dataset.status;
            loadList(1);
        });
    });

    // Event delegation utk tombol pagination (tabel di-render ulang tiap loadList)
    listContainer.addEventListener('click', function (e) {
        const btn = e.target.closest('button[data-page]');
        if (!btn || btn.disabled) return;
        loadList(parseInt(btn.dataset.page, 10));
    });

    // ===== MODAL DETAIL =====
    const detailModalOverlay = document.getElementById('detailModalOverlay');
    const detailFinalActions = document.getElementById('detailFinalActions');
    const detailCatatanInput = document.getElementById('detailCatatanInput');
    let currentDetail = null; // { id, invoice_number, nama, paket, nominal, status }

    function setDetailFields(d) {
        document.getElementById('detailInvoiceNumber').textContent = d.invoice_number ?? '-';
        document.getElementById('detailStatusBadge').textContent = d.status_label ?? '-';
        document.getElementById('detailStatusBadge').className = 'status-badge ' + (d.status ?? '');

        document.getElementById('detailNamaMurid').textContent = d.murid?.nama ?? '-';
        document.getElementById('detailWhatsappMurid').textContent = d.murid?.whatsapp ?? '-';
        document.getElementById('detailEmailMurid').textContent = d.murid?.email ?? '-';
        document.getElementById('detailPaketMurid').textContent = d.murid?.paket ?? '-';

        document.getElementById('detailInvoice').textContent = d.invoice_number ?? '-';
        document.getElementById('detailNominal').textContent = d.nominal != null ? ('Rp ' + Number(d.nominal).toLocaleString('id-ID')) : '-';
        document.getElementById('detailMetode').textContent = d.metode_pembayaran_label ?? '-';
        document.getElementById('detailStatusText').textContent = d.status_label ?? '-';
        document.getElementById('detailWaktuDaftar').textContent = d.created_at ?? '-';
        document.getElementById('detailWaktuVerifikasi').textContent = d.verified_at ?? '-';
        document.getElementById('detailAdminVerifikator').textContent = d.verified_by ?? '-';

        detailCatatanInput.value = d.catatan_internal ?? '';

        const buktiSection = document.getElementById('buktiTransferSection');
        if (d.has_bukti_transfer) {
            buktiSection.style.display = '';
            const previewUrl = `${transaksiUrl}/${d.id}/bukti-transfer`;
            document.getElementById('buktiPreviewImg').src = previewUrl;
            document.getElementById('btnLihatBukti').href = previewUrl;
            document.getElementById('btnDownloadBukti').href = `${previewUrl}/download`;
        } else {
            buktiSection.style.display = 'none';
        }

        const activityTimeline = document.getElementById('activityTimeline');
        activityTimeline.innerHTML = '';
        (d.activities ?? []).forEach(a => {
            const item = document.createElement('div');
            item.className = 'activity-item';
            item.innerHTML = `
                <div class="activity-dot"></div>
                <div class="activity-content">
                    <div class="activity-desc">${a.description}</div>
                    <div class="activity-meta">${a.causer} &middot; ${a.created_at ?? '-'}</div>
                </div>
            `;
            activityTimeline.appendChild(item);
        });
        if ((d.activities ?? []).length === 0) {
            activityTimeline.innerHTML = '<p class="detail-hint">Belum ada aktivitas.</p>';
        }

        // Aksi verifikasi/tolak cuma muncul kalau status masih bisa diproses
        const canProcess = d.status === 'menunggu_pembayaran' || d.status === 'menunggu_verifikasi';
        detailFinalActions.style.display = canProcess ? 'flex' : 'none';

        currentDetail = d;
    }

    function openDetailModal(id) {
        setDetailFields({});
        detailModalOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';

        fetch(`${transaksiUrl}/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(res => {
                if (!res.ok) throw new Error();
                return res.json();
            })
            .then(data => {
                setDetailFields(data.data);
                // Indikator "baru" di baris tabel langsung hilang tanpa reload penuh
                document.querySelectorAll(`#transaksiTable button[onclick="openDetailModal(${id})"]`)
                    .forEach(btn => {
                        const dot = btn.closest('tr')?.querySelector('.new-dot');
                        if (dot) dot.remove();
                    });
            })
            .catch(() => {
                showToast('Gagal memuat detail transaksi.', 'error');
                closeDetailModal();
            });
    }

    function closeDetailModal() {
        detailModalOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    detailModalOverlay.addEventListener('click', function (e) {
        if (e.target === this) closeDetailModal();
    });

    // ===== CATATAN INTERNAL =====
    function saveCatatan() {
        if (!currentDetail) return;
        const btn = document.getElementById('btnSimpanCatatan');
        btn.disabled = true;

        fetch(`${transaksiUrl}/${currentDetail.id}/catatan`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ catatan_internal: detailCatatanInput.value }),
        })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) {
                    showToast(data.message || 'Gagal menyimpan catatan.', 'error');
                    return;
                }
                showToast(data.message || 'Catatan berhasil disimpan.');
            })
            .catch(() => showToast('Terjadi kesalahan jaringan, coba lagi.', 'error'))
            .finally(() => { btn.disabled = false; });
    }

    // ===== TOLAK TRANSAKSI =====
    function rejectCurrentTransaksi() {
        if (!currentDetail) return;
        if (!confirm(`Yakin ingin menolak transaksi ${currentDetail.invoice_number}?`)) return;

        fetch(`${transaksiUrl}/${currentDetail.id}/reject`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) {
                    showToast(data.message || 'Gagal menolak transaksi.', 'error');
                    return;
                }
                showToast(data.message || 'Transaksi berhasil ditolak.');
                closeDetailModal();
                loadList(currentPage);
            })
            .catch(() => showToast('Terjadi kesalahan jaringan, coba lagi.', 'error'));
    }

    // ===== MODAL VERIFIKASI =====
    const verifyModalOverlay = document.getElementById('verifyModalOverlay');
    const verifyForm = document.getElementById('verifyForm');
    const verifyBuktiInput = document.getElementById('verifyBuktiInput');
    const verifyPreviewImg = document.getElementById('verifyPreviewImg');
    const btnVerifySubmit = document.getElementById('btnVerifySubmit');

    function openVerifyModalFromDetail() {
        if (!currentDetail) return;
        document.getElementById('verifyInvoice').textContent = currentDetail.invoice_number ?? '-';
        document.getElementById('verifyNama').textContent = currentDetail.murid?.nama ?? '-';
        document.getElementById('verifyPaket').textContent = currentDetail.murid?.paket ?? currentDetail.paket ?? '-';
        document.getElementById('verifyNominal').textContent = currentDetail.nominal != null ? ('Rp ' + Number(currentDetail.nominal).toLocaleString('id-ID')) : '-';

        verifyForm.reset();
        verifyPreviewImg.style.display = 'none';
        closeDetailModal();
        verifyModalOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeVerifyModal() {
        verifyModalOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    verifyModalOverlay.addEventListener('click', function (e) {
        if (e.target === this) closeVerifyModal();
    });

    verifyBuktiInput.addEventListener('change', function () {
        const file = this.files?.[0];
        if (!file) {
            verifyPreviewImg.style.display = 'none';
            return;
        }
        verifyPreviewImg.src = URL.createObjectURL(file);
        verifyPreviewImg.style.display = 'block';
    });

    verifyForm.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!currentDetail) return;

        const formData = new FormData();
        formData.append('bukti_transfer', verifyBuktiInput.files[0]);
        formData.append('catatan_internal', document.getElementById('verifyCatatanInput').value);

        btnVerifySubmit.disabled = true;
        const originalHtml = btnVerifySubmit.innerHTML;
        btnVerifySubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        fetch(`${transaksiUrl}/${currentDetail.id}/verify`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
        })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) {
                    showToast(data.message || (data.errors ? Object.values(data.errors)[0][0] : 'Terjadi kesalahan, coba lagi.'), 'error');
                    return;
                }
                showToast(data.message || 'Pembayaran berhasil diverifikasi.');
                closeVerifyModal();
                loadList(currentPage);
            })
            .catch(() => showToast('Terjadi kesalahan jaringan, coba lagi.', 'error'))
            .finally(() => {
                btnVerifySubmit.disabled = false;
                btnVerifySubmit.innerHTML = originalHtml;
            });
    });

    // ===== ESCAPE KEY =====
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeDetailModal();
            closeVerifyModal();
        }
    });

    // ===== TOAST =====
    function showToast(message, type = 'success') {
        const existing = document.querySelector('.toast-custom');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'toast-custom' + (type === 'error' ? ' error' : '');
        const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
        toast.innerHTML = `<i class="fas ${icon}"></i><span>${message}</span>`;

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
