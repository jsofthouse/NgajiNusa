@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}" />
<link rel="stylesheet" href="{{ asset('css/admin-murid.css') }}" />
@endpush

@section('title', 'NgajiNusa - Manajemen Murid')

@section('page-title', 'Manajemen Murid')
@section('page-subtitle', 'Kelola data murid dan status belajar')

@section('topbar-actions')
                <div class="search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari nama, email, atau WhatsApp..." id="searchInput" value="{{ $search }}" />
                </div>
                <button class="notif-btn" onclick="openAddModal()" style="background:var(--primary);color:var(--white);border-color:var(--primary);width:auto;padding:0 20px;border-radius:50px;">
                    <i class="fas fa-plus"></i> Tambah Murid
                </button>
@endsection

@section('content')

            <div class="data-panel">
                <div class="panel-header">
                    <h3>
                        <i class="fas fa-user-graduate" style="color:var(--primary);"></i> Daftar Murid
                        <span class="panel-total" id="muridTotalBadge">({{ $muridList->total() }})</span>
                    </h3>
                    <div class="actions">
                        <button class="btn-export" id="btnExport" onclick="exportMurid()" {{ $muridList->total() === 0 ? 'disabled' : '' }}>
                            <i class="fas fa-file-csv"></i> Export CSV
                        </button>
                        <button class="btn-add" onclick="openAddModal()"><i class="fas fa-plus"></i> Tambah</button>
                    </div>
                </div>
                <div id="muridListContainer">
                    @include('admin.partials.murid-list', ['muridList' => $muridList])
                </div>
            </div>
@endsection

@section('modals')
    <!-- ===== MODAL TAMBAH/EDIT MURID ===== -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <button class="close-modal" onclick="closeModal()"><i class="fas fa-times"></i></button>
            <h3 id="modalTitle">Tambah Murid</h3>
            <p class="sub" id="modalSub">Isi data murid baru. Status pendaftaran otomatis "Daftar".</p>
            <form id="modalForm">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" id="field_nama" placeholder="Nama murid" required />
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="field_email" placeholder="email@example.com" required />
                </div>
                <div class="form-group">
                    <label>Nomor WhatsApp</label>
                    <input type="tel" id="field_whatsapp" placeholder="08xxxxxxxxxx" required />
                </div>
                <div class="form-group">
                    <label>Level Belajar</label>
                    <select id="field_level_belajar">
                        @foreach (\App\Models\Murid::LEVEL_OPTIONS as $level)
                            <option value="{{ $level }}">{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Paket</label>
                    <select id="field_paket">
                        @foreach (\App\Models\Murid::PAKET_OPTIONS as $paket)
                            <option value="{{ $paket }}">{{ $paket }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-submit" id="btnSubmit"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== MODAL DETAIL MURID ===== -->
    <div class="modal-overlay" id="detailModalOverlay">
        <div class="modal">
            <button class="close-modal" onclick="closeDetailModal()"><i class="fas fa-times"></i></button>
            <h3>Detail Murid</h3>
            <p class="sub">Informasi lengkap data murid.</p>
            <div class="detail-list">
                <div class="detail-row"><span>ID Murid</span><strong id="detailId">-</strong></div>
                <div class="detail-row"><span>Nama</span><strong id="detailNama">-</strong></div>
                <div class="detail-row"><span>Email</span><strong id="detailEmail">-</strong></div>
                <div class="detail-row"><span>WhatsApp</span><strong id="detailWhatsapp">-</strong></div>
                <div class="detail-row"><span>Level Belajar</span><strong id="detailLevel">-</strong></div>
                <div class="detail-row"><span>Paket</span><strong id="detailPaket">-</strong></div>
                <div class="detail-row"><span>Status</span><strong id="detailStatus">-</strong></div>
                <div class="detail-row"><span>Referral Agent</span><strong id="detailReferral">-</strong></div>
                <div class="detail-row"><span>Dibuat pada</span><strong id="detailCreatedAt">-</strong></div>
                <div class="detail-row"><span>Terakhir diupdate</span><strong id="detailUpdatedAt">-</strong></div>
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

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const muridUrl = "{{ url('admin/murid') }}";
    const exportUrl = "{{ route('admin.murid.export') }}";

    // ===== LIST: SEARCH + PAGINATION (async) =====
    let currentPage = {{ $muridList->currentPage() }};
    let currentSearch = @json($search);
    let searchDebounce = null;

    const listContainer = document.getElementById('muridListContainer');
    const searchInput = document.getElementById('searchInput');
    const btnExport = document.getElementById('btnExport');
    const muridTotalBadge = document.getElementById('muridTotalBadge');

    function loadList(page = 1) {
        currentPage = page;
        listContainer.style.opacity = '0.5';
        listContainer.style.pointerEvents = 'none';

        const url = new URL(muridUrl, window.location.origin);
        if (currentSearch) url.searchParams.set('search', currentSearch);
        url.searchParams.set('page', page);

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                listContainer.innerHTML = data.html;
                muridTotalBadge.textContent = `(${data.total})`;
                btnExport.disabled = data.total === 0;
            })
            .catch(() => showToast('Gagal memuat data murid.', 'error'))
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

    // Event delegation utk tombol pagination (tabel di-render ulang tiap loadList)
    listContainer.addEventListener('click', function (e) {
        const btn = e.target.closest('button[data-page]');
        if (!btn || btn.disabled) return;
        loadList(parseInt(btn.dataset.page, 10));
    });

    // ===== MODAL TAMBAH/EDIT =====
    const modalOverlay = document.getElementById('modalOverlay');
    const modalTitle = document.getElementById('modalTitle');
    const modalSub = document.getElementById('modalSub');
    const modalForm = document.getElementById('modalForm');
    const btnSubmit = document.getElementById('btnSubmit');

    let editingId = null;
    let formSnapshot = '';

    function fieldValues() {
        return JSON.stringify({
            nama: document.getElementById('field_nama').value,
            email: document.getElementById('field_email').value,
            whatsapp: document.getElementById('field_whatsapp').value,
            level_belajar: document.getElementById('field_level_belajar').value,
            paket: document.getElementById('field_paket').value,
        });
    }

    function clearFieldErrors() {
        modalForm.querySelectorAll('.field-error').forEach(el => el.remove());
    }

    function showFieldErrors(errors) {
        Object.keys(errors).forEach(field => {
            const input = document.getElementById(`field_${field}`);
            if (!input) return;
            const span = document.createElement('span');
            span.className = 'field-error';
            span.textContent = errors[field][0];
            input.insertAdjacentElement('afterend', span);
        });
    }

    function openAddModal() {
        editingId = null;
        modalTitle.textContent = 'Tambah Murid';
        modalSub.textContent = 'Isi data murid baru. Status pendaftaran otomatis "Daftar".';
        modalForm.reset();
        clearFieldErrors();
        formSnapshot = fieldValues();
        modalOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function openEditModal(id, nama, email, whatsapp, levelBelajar, paket) {
        editingId = id;
        modalTitle.textContent = 'Edit Murid';
        modalSub.textContent = `Edit data murid: ${nama}`;
        clearFieldErrors();

        document.getElementById('field_nama').value = nama;
        document.getElementById('field_email').value = email;
        document.getElementById('field_whatsapp').value = whatsapp;
        document.getElementById('field_level_belajar').value = levelBelajar;
        document.getElementById('field_paket').value = paket;

        formSnapshot = fieldValues();
        modalOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function isFormDirty() {
        return fieldValues() !== formSnapshot;
    }

    function closeModal(force = false) {
        if (!force && modalOverlay.classList.contains('open') && isFormDirty()) {
            if (!confirm('Perubahan belum disimpan. Yakin ingin menutup?')) return;
        }
        modalOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    modalOverlay.addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalOverlay.classList.contains('open')) closeModal();
    });

    modalForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearFieldErrors();

        const payload = {
            nama: document.getElementById('field_nama').value,
            email: document.getElementById('field_email').value,
            whatsapp: document.getElementById('field_whatsapp').value,
            level_belajar: document.getElementById('field_level_belajar').value,
            paket: document.getElementById('field_paket').value,
        };

        const isEdit = editingId !== null;
        const url = isEdit ? `${muridUrl}/${editingId}` : muridUrl;

        btnSubmit.disabled = true;
        const originalBtnHtml = btnSubmit.innerHTML;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        fetch(url, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        })
            .then(async (res) => {
                const data = await res.json();

                if (!res.ok) {
                    if (res.status === 422 && data.errors) {
                        showFieldErrors(data.errors);
                        showToast('Periksa kembali data yang diisi.', 'error');
                    } else {
                        showToast(data.message || 'Terjadi kesalahan, coba lagi.', 'error');
                    }
                    return;
                }

                showToast(data.message || 'Data murid berhasil disimpan.');
                closeModal(true);
                loadList(isEdit ? currentPage : 1);
            })
            .catch(() => showToast('Terjadi kesalahan jaringan, coba lagi.', 'error'))
            .finally(() => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnHtml;
            });
    });

    // ===== MODAL DETAIL =====
    const detailModalOverlay = document.getElementById('detailModalOverlay');

    function setDetailFields(d) {
        document.getElementById('detailId').textContent = d.id ?? '-';
        document.getElementById('detailNama').textContent = d.nama ?? '-';
        document.getElementById('detailEmail').textContent = d.email ?? '-';
        document.getElementById('detailWhatsapp').textContent = d.whatsapp ?? '-';
        document.getElementById('detailLevel').textContent = d.level_belajar ?? '-';
        document.getElementById('detailPaket').textContent = d.paket ?? '-';
        document.getElementById('detailStatus').textContent = d.status ?? '-';
        document.getElementById('detailReferral').textContent = d.referral_agent ?? '-';
        document.getElementById('detailCreatedAt').textContent = d.created_at ?? '-';
        document.getElementById('detailUpdatedAt').textContent = d.updated_at ?? '-';
    }

    function openDetailModal(id) {
        setDetailFields({});
        detailModalOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';

        fetch(`${muridUrl}/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(res => {
                if (!res.ok) throw new Error();
                return res.json();
            })
            .then(data => setDetailFields(data.data))
            .catch(() => {
                showToast('Gagal memuat detail murid.', 'error');
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

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && detailModalOverlay.classList.contains('open')) closeDetailModal();
    });

    // ===== DELETE (soft delete) =====
    function deleteMurid(id, nama) {
        if (!confirm(`Apakah Anda yakin ingin menghapus murid "${nama}"?`)) return;

        fetch(`${muridUrl}/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) {
                    showToast(data.message || 'Gagal menghapus murid.', 'error');
                    return;
                }
                showToast(data.message || 'Murid berhasil dihapus.');
                loadList(currentPage);
            })
            .catch(() => showToast('Terjadi kesalahan jaringan, coba lagi.', 'error'));
    }

    // ===== EXPORT CSV =====
    function exportMurid() {
        if (btnExport.disabled) return;
        window.location.href = exportUrl;
    }

    // ===== TOAST =====
    function showToast(message, type = 'success') {
        const existing = document.querySelector('.toast-custom');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'toast-custom';
        const isError = type === 'error';
        toast.innerHTML = `
            <i class="fas ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'}" style="color:${isError ? '#e74c3c' : 'var(--primary-light)'};"></i>
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
            borderLeft: `4px solid ${isError ? '#e74c3c' : '#22b455'}`,
            fontFamily: "'Inter', sans-serif",
            fontSize: '0.95rem',
        });

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
