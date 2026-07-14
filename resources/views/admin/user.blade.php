@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}" />
<link rel="stylesheet" href="{{ asset('css/admin-user.css') }}" />
@endpush

@section('title', 'NgajiNusa - Manajemen User')

@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Kelola akun Admin & Super Admin')

@section('content')

            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama atau email..." value="{{ $filters['search'] }}" />
                </div>
                <select id="filterRole">
                    <option value="">Semua Role</option>
                    @foreach (\App\Models\User::ROLE_OPTIONS as $role)
                        <option value="{{ $role }}" {{ $filters['role'] === $role ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                </select>
                <select id="filterStatus">
                    <option value="">Semua Status</option>
                    @foreach (\App\Models\User::STATUS_OPTIONS as $status)
                        <option value="{{ $status }}" {{ $filters['status'] === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div class="data-panel">
                <div class="panel-header">
                    <h3>
                        <i class="fas fa-user-shield" style="color:var(--primary);"></i> Daftar Admin
                        <span class="panel-total" id="userTotalBadge">({{ $userList->total() }})</span>
                    </h3>
                    <div class="actions">
                        @if (auth()->user()?->role === \App\Models\User::ROLE_SUPER_ADMIN)
                            <button class="btn-add" onclick="openAddModal()"><i class="fas fa-plus"></i> Tambah Admin</button>
                        @endif
                    </div>
                </div>
                <div id="userListContainer">
                    @include('admin.partials.user-list', ['userList' => $userList])
                </div>
            </div>
@endsection

@section('modals')
    <!-- ===== MODAL TAMBAH/EDIT ADMIN ===== -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <button class="close-modal" onclick="closeModal()"><i class="fas fa-times"></i></button>
            <h3 id="modalTitle">Tambah Admin</h3>
            <p class="sub" id="modalSub">Isi data Admin baru. Status otomatis "Aktif".</p>
            <form id="modalForm">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" id="field_nama" placeholder="Nama lengkap" required />
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="field_email" placeholder="email@example.com" required />
                </div>
                <div class="form-group" id="group_password">
                    <label id="label_password">Password</label>
                    <input type="password" id="field_password" placeholder="Minimal 8 karakter" />
                </div>
                <div class="form-group" id="group_password_confirmation">
                    <label>Konfirmasi Password</label>
                    <input type="password" id="field_password_confirmation" placeholder="Ulangi password" />
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select id="field_role">
                        @foreach (\App\Models\User::ROLE_OPTIONS as $role)
                            <option value="{{ $role }}">{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" id="group_status" style="display:none;">
                    <label>Status</label>
                    <select id="field_status">
                        @foreach (\App\Models\User::STATUS_OPTIONS as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                    <span class="field-error" id="selfGuardHint" style="display:none;color:var(--text-light);">Tidak bisa mengubah role/status akun sendiri.</span>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-submit" id="btnSubmit"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== MODAL DETAIL ADMIN ===== -->
    <div class="modal-overlay" id="detailModalOverlay">
        <div class="modal">
            <button class="close-modal" onclick="closeDetailModal()"><i class="fas fa-times"></i></button>
            <h3>Detail Admin</h3>
            <p class="sub">Informasi lengkap akun Admin.</p>
            <div class="detail-list">
                <div class="detail-row"><span>ID</span><strong id="detailId">-</strong></div>
                <div class="detail-row"><span>Nama</span><strong id="detailNama">-</strong></div>
                <div class="detail-row"><span>Email</span><strong id="detailEmail">-</strong></div>
                <div class="detail-row"><span>Role</span><strong id="detailRole">-</strong></div>
                <div class="detail-row"><span>Status</span><strong id="detailStatus">-</strong></div>
                <div class="detail-row"><span>Last Login</span><strong id="detailLastLogin">-</strong></div>
                <div class="detail-row"><span>Dibuat oleh</span><strong id="detailCreatedBy">-</strong></div>
                <div class="detail-row"><span>Diubah oleh</span><strong id="detailUpdatedBy">-</strong></div>
                <div class="detail-row"><span>Dibuat pada</span><strong id="detailCreatedAt">-</strong></div>
                <div class="detail-row"><span>Terakhir diupdate</span><strong id="detailUpdatedAt">-</strong></div>
            </div>
        </div>
    </div>

    <!-- ===== MODAL KONFIRMASI HAPUS ===== -->
    <div class="modal-overlay" id="confirmDeleteOverlay">
        <div class="modal">
            <button class="close-modal" onclick="closeDeleteModal()"><i class="fas fa-times"></i></button>
            <h3><i class="fas fa-exclamation-triangle" style="color:var(--danger);"></i> Konfirmasi Hapus</h3>
            <p class="sub" id="confirmDeleteText">Yakin ingin menghapus admin ini?</p>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
                <button type="button" class="btn-submit" id="btnConfirmDelete" style="background:var(--danger);border-color:var(--danger);" onclick="confirmDeleteAction()">
                    <i class="fas fa-trash"></i> Hapus
                </button>
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
    const userUrl = "{{ url('admin/user') }}";
    const currentUserId = {{ auth()->id() ?? 'null' }};

    // ===== LIST: SEARCH + FILTER + PAGINATION (async) =====
    let currentPage = {{ $userList->currentPage() }};
    let currentSearch = @json($filters['search']);
    let searchDebounce = null;

    const listContainer = document.getElementById('userListContainer');
    const searchInput = document.getElementById('searchInput');
    const filterRole = document.getElementById('filterRole');
    const filterStatus = document.getElementById('filterStatus');
    const userTotalBadge = document.getElementById('userTotalBadge');

    function loadList(page = 1) {
        currentPage = page;
        listContainer.style.opacity = '0.5';
        listContainer.style.pointerEvents = 'none';

        const url = new URL(userUrl, window.location.origin);
        if (currentSearch) url.searchParams.set('search', currentSearch);
        if (filterRole.value) url.searchParams.set('role', filterRole.value);
        if (filterStatus.value) url.searchParams.set('status', filterStatus.value);
        url.searchParams.set('page', page);

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                listContainer.innerHTML = data.html;
                userTotalBadge.textContent = `(${data.total})`;
            })
            .catch(() => showToast('Gagal memuat data user.', 'error'))
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

    [filterRole, filterStatus].forEach(el => el.addEventListener('change', () => loadList(1)));

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
    const groupStatus = document.getElementById('group_status');
    const fieldRole = document.getElementById('field_role');
    const fieldStatus = document.getElementById('field_status');
    const labelPassword = document.getElementById('label_password');
    const selfGuardHint = document.getElementById('selfGuardHint');

    let editingId = null;
    let formSnapshot = '';

    function fieldValues() {
        return JSON.stringify({
            nama: document.getElementById('field_nama').value,
            email: document.getElementById('field_email').value,
            password: document.getElementById('field_password').value,
            role: fieldRole.value,
            status: fieldStatus.value,
        });
    }

    function clearFieldErrors() {
        modalForm.querySelectorAll('.field-error:not(#selfGuardHint)').forEach(el => el.remove());
    }

    function showFieldErrors(errors) {
        Object.keys(errors).forEach(field => {
            const map = { password_confirmation: 'password_confirmation' };
            const input = document.getElementById(`field_${map[field] ?? field}`);
            if (!input) return;
            const span = document.createElement('span');
            span.className = 'field-error';
            span.textContent = errors[field][0];
            input.insertAdjacentElement('afterend', span);
        });
    }

    function openAddModal() {
        editingId = null;
        modalTitle.textContent = 'Tambah Admin';
        modalSub.textContent = 'Isi data Admin baru. Status otomatis "Aktif".';
        modalForm.reset();
        clearFieldErrors();
        labelPassword.textContent = 'Password';
        document.getElementById('field_password').required = true;
        document.getElementById('field_password_confirmation').required = true;
        groupStatus.style.display = 'none';
        fieldRole.disabled = false;
        fieldStatus.disabled = false;
        selfGuardHint.style.display = 'none';
        formSnapshot = fieldValues();
        modalOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function openEditModal(id, nama, email, role, status) {
        editingId = id;
        modalTitle.textContent = 'Edit Admin';
        modalSub.textContent = `Edit data Admin: ${nama}`;
        clearFieldErrors();

        document.getElementById('field_nama').value = nama;
        document.getElementById('field_email').value = email;
        document.getElementById('field_password').value = '';
        document.getElementById('field_password_confirmation').value = '';
        labelPassword.textContent = 'Password (kosongkan jika tidak ingin mengubah)';
        document.getElementById('field_password').required = false;
        document.getElementById('field_password_confirmation').required = false;
        fieldRole.value = role;
        groupStatus.style.display = '';
        fieldStatus.value = status;

        // Business rule: Super Admin tidak bisa ubah role/nonaktifkan akun sendiri
        const isSelf = id === currentUserId;
        fieldRole.disabled = isSelf;
        fieldStatus.disabled = isSelf;
        selfGuardHint.style.display = isSelf ? '' : 'none';

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

    modalForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearFieldErrors();

        const isEdit = editingId !== null;
        const payload = {
            nama: document.getElementById('field_nama').value,
            email: document.getElementById('field_email').value,
            role: fieldRole.value,
        };

        const password = document.getElementById('field_password').value;
        const passwordConfirmation = document.getElementById('field_password_confirmation').value;
        if (password || !isEdit) {
            payload.password = password;
            payload.password_confirmation = passwordConfirmation;
        }
        if (isEdit) {
            payload.status = fieldStatus.value;
        }

        const url = isEdit ? `${userUrl}/${editingId}` : userUrl;

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

                showToast(data.message || 'Data Admin berhasil disimpan.');
                closeModal(true);
                loadList(isEdit ? currentPage : 1);
            })
            .catch(() => showToast('Terjadi kesalahan jaringan, coba lagi.', 'error'))
            .finally(() => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnHtml;
            });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalOverlay.classList.contains('open')) closeModal();
    });

    // ===== MODAL DETAIL =====
    const detailModalOverlay = document.getElementById('detailModalOverlay');

    function setDetailFields(d) {
        document.getElementById('detailId').textContent = d.id ?? '-';
        document.getElementById('detailNama').textContent = d.nama ?? '-';
        document.getElementById('detailEmail').textContent = d.email ?? '-';
        document.getElementById('detailRole').textContent = d.role ?? '-';
        document.getElementById('detailStatus').textContent = d.status ?? '-';
        document.getElementById('detailLastLogin').textContent = d.last_login_at ?? '-';
        document.getElementById('detailCreatedBy').textContent = d.created_by ?? '-';
        document.getElementById('detailUpdatedBy').textContent = d.updated_by ?? '-';
        document.getElementById('detailCreatedAt').textContent = d.created_at ?? '-';
        document.getElementById('detailUpdatedAt').textContent = d.updated_at ?? '-';
    }

    function openDetailModal(id) {
        setDetailFields({});
        detailModalOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';

        fetch(`${userUrl}/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(res => {
                if (!res.ok) throw new Error();
                return res.json();
            })
            .then(data => setDetailFields(data.data))
            .catch(() => {
                showToast('Gagal memuat detail Admin.', 'error');
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

    // ===== MODAL KONFIRMASI HAPUS =====
    const confirmDeleteOverlay = document.getElementById('confirmDeleteOverlay');
    let pendingDeleteId = null;

    function openDeleteModal(id, nama) {
        pendingDeleteId = id;
        document.getElementById('confirmDeleteText').textContent = `Apakah Anda yakin ingin menghapus admin "${nama}"?`;
        confirmDeleteOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        pendingDeleteId = null;
        confirmDeleteOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    confirmDeleteOverlay.addEventListener('click', function (e) {
        if (e.target === this) closeDeleteModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && confirmDeleteOverlay.classList.contains('open')) closeDeleteModal();
    });

    function confirmDeleteAction() {
        if (pendingDeleteId === null) return;
        const btn = document.getElementById('btnConfirmDelete');
        btn.disabled = true;

        fetch(`${userUrl}/${pendingDeleteId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) {
                    showToast(data.message || 'Gagal menghapus Admin.', 'error');
                    return;
                }
                showToast(data.message || 'Admin berhasil dihapus.');
                closeDeleteModal();
                loadList(currentPage);
            })
            .catch(() => showToast('Terjadi kesalahan jaringan, coba lagi.', 'error'))
            .finally(() => { btn.disabled = false; });
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
