@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}" />
<link rel="stylesheet" href="{{ asset('css/admin-referral-agent.css') }}" />
@endpush

@section('title', 'NgajiNusa - Referral Agent')

@section('page-title', 'Referral Agent')
@section('page-subtitle', 'Kelola agen referral dan pantau jumlah murid yang mereka bawa')

@section('topbar-actions')
                <button class="notif-btn" onclick="openAddModal()" style="background:var(--primary);color:var(--white);border-color:var(--primary);width:auto;padding:0 20px;border-radius:50px;">
                    <i class="fas fa-plus"></i> Tambah Agent
                </button>
@endsection

@section('content')

@if (session('success'))
    <div class="ra-alert ra-alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

            <div class="data-panel">
                <div class="panel-header">
                    <h3><i class="fas fa-user-tie" style="color:var(--primary);"></i> Daftar Referral Agent</h3>
                    <div class="actions">
                        <button class="btn-add" onclick="openAddModal()"><i class="fas fa-plus"></i> Tambah Agent</button>
                    </div>
                </div>
                <div class="table-wrap">
                    <table id="referralAgentTable">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>WhatsApp</th>
                                <th>Kode Referral</th>
                                <th>Link Referral</th>
                                <th>Murid Referral</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($agents as $agent)
                                @php $link = $referralAgentService->buildReferralLink($agent); @endphp
                                <tr>
                                    <td>{{ $agent->nama }}</td>
                                    <td>{{ $agent->email }}</td>
                                    <td>{{ $agent->whatsapp }}</td>
                                    <td>
                                        <div class="ra-kode">
                                            <code>{{ $agent->kode }}</code>
                                            <button type="button" class="ra-copy-btn" onclick="raCopy('{{ $agent->kode }}', this)" title="Copy Kode">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ra-kode">
                                            <span class="ra-link-text" title="{{ $link }}">{{ $link }}</span>
                                            <button type="button" class="ra-copy-btn" onclick="raCopy('{{ $link }}', this)" title="Copy Link">
                                                <i class="fas fa-link"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>{{ $agent->murid_count }}</td>
                                    <td>
                                        <span class="status-badge {{ $agent->status === \App\Models\ReferralAgent::STATUS_ACTIVE ? 'active' : 'cancelled' }}">
                                            {{ $agent->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button type="button" title="Edit"
                                                onclick="openEditModal({{ $agent->id }}, '{{ addslashes($agent->nama) }}', '{{ addslashes($agent->email) }}', '{{ $agent->whatsapp }}', '{{ $agent->status }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.referral-agent.toggle-status', $agent) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" title="{{ $agent->status === \App\Models\ReferralAgent::STATUS_ACTIVE ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="fas {{ $agent->status === \App\Models\ReferralAgent::STATUS_ACTIVE ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align:center;color:var(--text-light);">Belum ada Referral Agent. Klik "Tambah Agent" untuk mulai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
@endsection

@section('modals')
    <!-- ===== MODAL TAMBAH/EDIT AGENT ===== -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <button class="close-modal" onclick="closeModal()"><i class="fas fa-times"></i></button>
            <h3 id="modalTitle">Tambah Referral Agent</h3>
            <p class="sub" id="modalSub">Isi data agent baru. Kode referral akan dibuat otomatis.</p>
            <form id="modalForm" method="POST" action="{{ route('admin.referral-agent.store') }}">
                @csrf
                <div id="methodField"></div>
                <input type="hidden" name="_editing_id" id="field_editing_id" value="{{ old('_editing_id') }}" />
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" id="field_nama" value="{{ old('nama') }}" placeholder="Nama agent" required />
                    @error('nama') <span class="ra-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="field_email" value="{{ old('email') }}" placeholder="email@example.com" required />
                    @error('email') <span class="ra-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Nomor WhatsApp</label>
                    <input type="tel" name="whatsapp" id="field_whatsapp" value="{{ old('whatsapp') }}" placeholder="08xxxxxxxxxx" required />
                    @error('whatsapp') <span class="ra-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="field_status">
                        <option value="{{ \App\Models\ReferralAgent::STATUS_ACTIVE }}" {{ old('status', \App\Models\ReferralAgent::STATUS_ACTIVE) === \App\Models\ReferralAgent::STATUS_ACTIVE ? 'selected' : '' }}>Aktif</option>
                        <option value="{{ \App\Models\ReferralAgent::STATUS_INACTIVE }}" {{ old('status') === \App\Models\ReferralAgent::STATUS_INACTIVE ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status') <span class="ra-error">{{ $message }}</span> @enderror
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
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        if (window.innerWidth <= 992) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        }
    });

    // ===== MODAL TAMBAH/EDIT =====
    const modalOverlay = document.getElementById('modalOverlay');
    const modalTitle = document.getElementById('modalTitle');
    const modalSub = document.getElementById('modalSub');
    const modalForm = document.getElementById('modalForm');
    const methodField = document.getElementById('methodField');

    const storeUrl = "{{ route('admin.referral-agent.store') }}";

    function openAddModal() {
        modalTitle.textContent = 'Tambah Referral Agent';
        modalSub.textContent = 'Isi data agent baru. Kode referral akan dibuat otomatis.';
        modalForm.action = storeUrl;
        methodField.innerHTML = '';
        document.getElementById('field_editing_id').value = '';
        modalForm.reset();
        modalOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function openEditModal(id, nama, email, whatsapp, status) {
        modalTitle.textContent = 'Edit Referral Agent';
        modalSub.textContent = `Edit data agent: ${nama}`;
        modalForm.action = `{{ url('admin/referral-agent') }}/${id}`;
        methodField.innerHTML = "<input type='hidden' name='_method' value='PUT'>";
        document.getElementById('field_editing_id').value = id;

        document.getElementById('field_nama').value = nama;
        document.getElementById('field_email').value = email;
        document.getElementById('field_whatsapp').value = whatsapp;
        document.getElementById('field_status').value = status;

        modalOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modalOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    modalOverlay.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalOverlay.classList.contains('open')) closeModal();
    });

    // Kalau ada validation error dari server, buka lagi modal yang relevan
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            @if (old('_editing_id'))
                openEditModal(
                    '{{ old('_editing_id') }}',
                    '{{ addslashes(old('nama', '')) }}',
                    '{{ addslashes(old('email', '')) }}',
                    '{{ old('whatsapp', '') }}',
                    '{{ old('status', '') }}'
                );
            @else
                openAddModal();
            @endif
        });
    @endif

    // ===== COPY KODE / LINK =====
    function raCopyFeedback(btnEl) {
        const original = btnEl.innerHTML;
        btnEl.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => { btnEl.innerHTML = original; }, 1500);
    }

    // Fallback manual pakai textarea + execCommand, dipakai kalau navigator.clipboard
    // gak tersedia (browser lama) atau ditolak (bukan secure context, mis. http://ngaji-nusa.test).
    function raCopyFallback(text, btnEl) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();

        try {
            document.execCommand('copy');
            raCopyFeedback(btnEl);
        } catch (err) {
            alert('Gagal copy otomatis. Silakan copy manual: ' + text);
        }

        document.body.removeChild(textarea);
    }

    function raCopy(text, btnEl) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text)
                .then(() => raCopyFeedback(btnEl))
                .catch(() => raCopyFallback(text, btnEl));
        } else {
            raCopyFallback(text, btnEl);
        }
    }
</script>
@endpush
