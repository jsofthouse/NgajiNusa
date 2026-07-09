@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-transaksi.css') }}" />
@endpush

@section('title', 'NgajiNusa - Transaksi & Pembayaran')

@section('page-title', '💳 Transaksi & Pembayaran')
@section('page-subtitle', 'Kelola semua transaksi dan pembayaran kursus')

@section('topbar-actions')
                <button class="btn-secondary" onclick="showToast('Data transaksi berhasil diekspor!')">
                    <i class="fas fa-file-excel"></i> Export
                </button>
                <button class="btn-primary" onclick="openNewInvoice()">
                    <i class="fas fa-plus"></i> Invoice Baru
                </button>
@endsection

@section('content')
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-coins"></i></div>
                <div class="stat-number">Rp 32,4Jt</div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle" style="color:var(--primary-light);"></i></div>
                <div class="stat-number">47</div>
                <div class="stat-label">Transaksi Lunas</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock" style="color:var(--secondary);"></i></div>
                <div class="stat-number">8</div>
                <div class="stat-label">Menunggu Pembayaran</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle" style="color:var(--danger);"></i></div>
                <div class="stat-number">3</div>
                <div class="stat-label">Kadaluarsa / Batal</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="trans-tabs">
            <button class="active" data-tab="semua">
                <i class="fas fa-list"></i> Semua
                <span class="badge">58</span>
            </button>
            <button data-tab="menunggu">
                <i class="fas fa-clock"></i> Menunggu
                <span class="badge">8</span>
            </button>
            <button data-tab="diproses">
                <i class="fas fa-spinner"></i> Diproses
                <span class="badge">3</span>
            </button>
            <button data-tab="lunas">
                <i class="fas fa-check"></i> Lunas
                <span class="badge">47</span>
            </button>
            <button data-tab="batal">
                <i class="fas fa-times"></i> Batal/Kadaluarsa
                <span class="badge">3</span>
            </button>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari invoice, murid, atau metode..." id="searchInput" oninput="filterTable()" />
            </div>
            <select id="filterMethod" onchange="filterTable()">
                <option value="">Semua Metode</option>
                <option value="QRIS">QRIS</option>
                <option value="Transfer">Transfer Bank</option>
                <option value="E-Wallet">E-Wallet</option>
                <option value="Tunai">Tunai</option>
            </select>
            <select id="filterDate" onchange="filterTable()">
                <option value="all">Semua Periode</option>
                <option value="today">Hari Ini</option>
                <option value="week">Minggu Ini</option>
                <option value="month">Bulan Ini</option>
            </select>
        </div>

        <!-- ===== TAB: SEMUA ===== -->
        <div class="tab-content active" id="tab-semua">
            <div class="table-wrap">
                <table id="transTable">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Murid</th>
                            <th>Paket</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transBody">
                        <tr data-status="menunggu" data-method="QRIS" data-date="2026-07-10">
                            <td><strong>#INV-049</strong></td>
                            <td>Fatimah A.</td>
                            <td>Pro</td>
                            <td><span class="payment-method"><i class="fas fa-qrcode" style="color:var(--primary);"></i> QRIS</span></td>
                            <td>Rp 450.000</td>
                            <td><span class="status-badge menunggu">Menunggu</span></td>
                            <td>10 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-049')"><i class="fas fa-eye"></i></button>
                                    <button class="whatsapp" onclick="showToast('Notifikasi WA dikirim!')"><i class="fab fa-whatsapp"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr data-status="lunas" data-method="Transfer" data-date="2026-07-09">
                            <td><strong>#INV-048</strong></td>
                            <td>Adam S.</td>
                            <td>Premium</td>
                            <td><span class="payment-method"><i class="fas fa-university" style="color:var(--blue);"></i> Transfer</span></td>
                            <td>Rp 800.000</td>
                            <td><span class="status-badge lunas">Lunas</span></td>
                            <td>09 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-048')"><i class="fas fa-eye"></i></button>
                                    <button onclick="showToast('Invoice dikirim ke email!')"><i class="fas fa-envelope"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr data-status="lunas" data-method="E-Wallet" data-date="2026-07-08">
                            <td><strong>#INV-047</strong></td>
                            <td>Zahra N.</td>
                            <td>Basic</td>
                            <td><span class="payment-method"><i class="fas fa-mobile-alt" style="color:var(--purple);"></i> E-Wallet</span></td>
                            <td>Rp 250.000</td>
                            <td><span class="status-badge lunas">Lunas</span></td>
                            <td>08 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-047')"><i class="fas fa-eye"></i></button>
                                    <button onclick="showToast('Invoice dikirim ke email!')"><i class="fas fa-envelope"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr data-status="diproses" data-method="Transfer" data-date="2026-07-07">
                            <td><strong>#INV-046</strong></td>
                            <td>Muhammad R.</td>
                            <td>Pro</td>
                            <td><span class="payment-method"><i class="fas fa-university" style="color:var(--blue);"></i> Transfer</span></td>
                            <td>Rp 450.000</td>
                            <td><span class="status-badge diproses">Diproses</span></td>
                            <td>07 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-046')"><i class="fas fa-eye"></i></button>
                                    <button class="whatsapp" onclick="showToast('Notifikasi WA dikirim!')"><i class="fab fa-whatsapp"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr data-status="lunas" data-method="QRIS" data-date="2026-07-06">
                            <td><strong>#INV-045</strong></td>
                            <td>Nisa A.</td>
                            <td>Premium</td>
                            <td><span class="payment-method"><i class="fas fa-qrcode" style="color:var(--primary);"></i> QRIS</span></td>
                            <td>Rp 800.000</td>
                            <td><span class="status-badge lunas">Lunas</span></td>
                            <td>06 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-045')"><i class="fas fa-eye"></i></button>
                                    <button onclick="showToast('Invoice dikirim ke email!')"><i class="fas fa-envelope"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr data-status="menunggu" data-method="E-Wallet" data-date="2026-07-05">
                            <td><strong>#INV-044</strong></td>
                            <td>Rizki A.</td>
                            <td>Basic</td>
                            <td><span class="payment-method"><i class="fas fa-mobile-alt" style="color:var(--purple);"></i> E-Wallet</span></td>
                            <td>Rp 250.000</td>
                            <td><span class="status-badge menunggu">Menunggu</span></td>
                            <td>05 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-044')"><i class="fas fa-eye"></i></button>
                                    <button class="whatsapp" onclick="showToast('Notifikasi WA dikirim!')"><i class="fab fa-whatsapp"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr data-status="batal" data-method="Transfer" data-date="2026-07-04" class="expired">
                            <td><strong>#INV-043</strong></td>
                            <td>Siti N.</td>
                            <td>Pro</td>
                            <td><span class="payment-method"><i class="fas fa-university" style="color:var(--blue);"></i> Transfer</span></td>
                            <td>Rp 450.000</td>
                            <td><span class="status-badge kadaluarsa">Kadaluarsa</span></td>
                            <td>04 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-043')"><i class="fas fa-eye"></i></button>
                                    <button onclick="showToast('Invoice dihapus!')" style="color:var(--danger);"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr data-status="lunas" data-method="QRIS" data-date="2026-07-03">
                            <td><strong>#INV-042</strong></td>
                            <td>Hasan M.</td>
                            <td>Premium</td>
                            <td><span class="payment-method"><i class="fas fa-qrcode" style="color:var(--primary);"></i> QRIS</span></td>
                            <td>Rp 800.000</td>
                            <td><span class="status-badge lunas">Lunas</span></td>
                            <td>03 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-042')"><i class="fas fa-eye"></i></button>
                                    <button onclick="showToast('Invoice dikirim ke email!')"><i class="fas fa-envelope"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="pagination">
                    <span class="info">Menampilkan 1-7 dari 58 transaksi</span>
                    <div class="pages">
                        <button>‹</button>
                        <button class="active">1</button>
                        <button>2</button>
                        <button>3</button>
                        <button>…</button>
                        <button>9</button>
                        <button>›</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TAB: MENUNGGU ===== -->
        <div class="tab-content" id="tab-menunggu">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Murid</th>
                            <th>Paket</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>#INV-049</strong></td>
                            <td>Fatimah A.</td>
                            <td>Pro</td>
                            <td><span class="payment-method"><i class="fas fa-qrcode" style="color:var(--primary);"></i> QRIS</span></td>
                            <td>Rp 450.000</td>
                            <td><span class="status-badge menunggu">Menunggu</span></td>
                            <td>10 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-049')"><i class="fas fa-eye"></i></button>
                                    <button class="whatsapp" onclick="showToast('Notifikasi WA dikirim!')"><i class="fab fa-whatsapp"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>#INV-044</strong></td>
                            <td>Rizki A.</td>
                            <td>Basic</td>
                            <td><span class="payment-method"><i class="fas fa-mobile-alt" style="color:var(--purple);"></i> E-Wallet</span></td>
                            <td>Rp 250.000</td>
                            <td><span class="status-badge menunggu">Menunggu</span></td>
                            <td>05 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-044')"><i class="fas fa-eye"></i></button>
                                    <button class="whatsapp" onclick="showToast('Notifikasi WA dikirim!')"><i class="fab fa-whatsapp"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== TAB: DIPROSES ===== -->
        <div class="tab-content" id="tab-diproses">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Murid</th>
                            <th>Paket</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>#INV-046</strong></td>
                            <td>Muhammad R.</td>
                            <td>Pro</td>
                            <td><span class="payment-method"><i class="fas fa-university" style="color:var(--blue);"></i> Transfer</span></td>
                            <td>Rp 450.000</td>
                            <td><span class="status-badge diproses">Diproses</span></td>
                            <td>07 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-046')"><i class="fas fa-eye"></i></button>
                                    <button class="whatsapp" onclick="showToast('Notifikasi WA dikirim!')"><i class="fab fa-whatsapp"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== TAB: LUNAS ===== -->
        <div class="tab-content" id="tab-lunas">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Murid</th>
                            <th>Paket</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>#INV-048</strong></td>
                            <td>Adam S.</td>
                            <td>Premium</td>
                            <td><span class="payment-method"><i class="fas fa-university" style="color:var(--blue);"></i> Transfer</span></td>
                            <td>Rp 800.000</td>
                            <td><span class="status-badge lunas">Lunas</span></td>
                            <td>09 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-048')"><i class="fas fa-eye"></i></button>
                                    <button onclick="showToast('Invoice dikirim ke email!')"><i class="fas fa-envelope"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>#INV-047</strong></td>
                            <td>Zahra N.</td>
                            <td>Basic</td>
                            <td><span class="payment-method"><i class="fas fa-mobile-alt" style="color:var(--purple);"></i> E-Wallet</span></td>
                            <td>Rp 250.000</td>
                            <td><span class="status-badge lunas">Lunas</span></td>
                            <td>08 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-047')"><i class="fas fa-eye"></i></button>
                                    <button onclick="showToast('Invoice dikirim ke email!')"><i class="fas fa-envelope"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== TAB: BATAL ===== -->
        <div class="tab-content" id="tab-batal">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Murid</th>
                            <th>Paket</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="expired">
                            <td><strong>#INV-043</strong></td>
                            <td>Siti N.</td>
                            <td>Pro</td>
                            <td><span class="payment-method"><i class="fas fa-university" style="color:var(--blue);"></i> Transfer</span></td>
                            <td>Rp 450.000</td>
                            <td><span class="status-badge kadaluarsa">Kadaluarsa</span></td>
                            <td>04 Jul 2026</td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="viewInvoice('INV-043')"><i class="fas fa-eye"></i></button>
                                    <button onclick="showToast('Invoice dihapus!')" style="color:var(--danger);"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
@endsection

@section('modals')
    <!-- ===== INVOICE DETAIL MODAL ===== -->
    <div class="modal-overlay" id="invoiceModal">
        <div class="modal">
            <button class="close-modal" onclick="closeInvoiceModal()"><i class="fas fa-times"></i></button>

            <div class="invoice-header">
                <div>
                    <div class="title"><i class="fas fa-file-invoice" style="margin-right:8px;"></i> Invoice</div>
                    <div class="sub" id="invNumber">#INV-049</div>
                </div>
                <span class="status-badge menunggu" id="invStatus">Menunggu</span>
            </div>

            <div class="invoice-body">
                <div class="field">
                    <div class="label">Murid</div>
                    <div class="value" id="invCustomer">Fatimah A.</div>
                </div>
                <div class="field">
                    <div class="label">Tanggal</div>
                    <div class="value" id="invDate">10 Juli 2026</div>
                </div>
                <div class="field">
                    <div class="label">Paket</div>
                    <div class="value" id="invPackage">Pro</div>
                </div>
                <div class="field">
                    <div class="label">Metode Pembayaran</div>
                    <div class="value" id="invMethod">QRIS</div>
                </div>
                <div class="field">
                    <div class="label">Jatuh Tempo</div>
                    <div class="value" id="invDue">12 Juli 2026</div>
                </div>
                <div class="field">
                    <div class="label">Status</div>
                    <div class="value" id="invStatusText">Menunggu Pembayaran</div>
                </div>
            </div>

            <div class="invoice-items">
                <div class="item">
                    <span>Paket Pro (8 sesi)</span>
                    <span>Rp 450.000</span>
                </div>
                <div class="item">
                    <span>Biaya Administrasi</span>
                    <span>Rp 0</span>
                </div>
                <div class="item total">
                    <span>Total</span>
                    <span id="invTotal">Rp 450.000</span>
                </div>
            </div>

            <div class="invoice-actions">
                <button class="btn-primary" onclick="showToast('Invoice berhasil dikirim ke email!')">
                    <i class="fas fa-envelope"></i> Kirim Email
                </button>
                <button class="btn-primary" onclick="showToast('Notifikasi WA berhasil dikirim!')">
                    <i class="fab fa-whatsapp"></i> Kirim WA
                </button>
                <button class="btn-secondary" onclick="showToast('Invoice berhasil dicetak!')">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <button class="btn-danger" onclick="if(confirm('Konfirmasi pembatalan invoice?')){showToast('Invoice dibatalkan!');closeInvoiceModal();}">
                    <i class="fas fa-times"></i> Batalkan
                </button>
            </div>
        </div>
    </div>

    <!-- ===== NEW INVOICE MODAL ===== -->
    <div class="modal-overlay" id="newInvoiceModal">
        <div class="modal">
            <button class="close-modal" onclick="closeNewInvoiceModal()"><i class="fas fa-times"></i></button>
            <h3 style="font-size:1.4rem;font-weight:700;margin-bottom:6px;">
                <i class="fas fa-file-invoice" style="color:var(--primary);"></i> Buat Invoice Baru
            </h3>
            <p style="color:var(--text-gray);font-size:0.9rem;margin-bottom:20px;">Isi data untuk membuat invoice baru.</p>

            <form onsubmit="saveNewInvoice(event)">
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-weight:600;font-size:0.85rem;margin-bottom:4px;">Murid</label>
                    <select style="width:100%;padding:12px 16px;border:2px solid #e0e8e0;border-radius:var(--radius-sm);font-size:0.95rem;font-family:inherit;background:var(--white);">
                        <option>Fatimah A.</option>
                        <option>Adam S.</option>
                        <option>Zahra N.</option>
                        <option>Muhammad R.</option>
                        <option>Nisa A.</option>
                    </select>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-weight:600;font-size:0.85rem;margin-bottom:4px;">Paket</label>
                    <select style="width:100%;padding:12px 16px;border:2px solid #e0e8e0;border-radius:var(--radius-sm);font-size:0.95rem;font-family:inherit;background:var(--white);">
                        <option>Basic - Rp 250.000</option>
                        <option selected>Pro - Rp 450.000</option>
                        <option>Premium - Rp 800.000</option>
                    </select>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-weight:600;font-size:0.85rem;margin-bottom:4px;">Metode Pembayaran</label>
                    <select style="width:100%;padding:12px 16px;border:2px solid #e0e8e0;border-radius:var(--radius-sm);font-size:0.95rem;font-family:inherit;background:var(--white);">
                        <option>QRIS</option>
                        <option>Transfer Bank</option>
                        <option>E-Wallet</option>
                        <option>Tunai</option>
                    </select>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-weight:600;font-size:0.85rem;margin-bottom:4px;">Tanggal Jatuh Tempo</label>
                    <input type="date" value="2026-07-17" style="width:100%;padding:12px 16px;border:2px solid #e0e8e0;border-radius:var(--radius-sm);font-size:0.95rem;font-family:inherit;" />
                </div>
                <div style="display:flex;gap:12px;margin-top:20px;">
                    <button type="button" onclick="closeNewInvoiceModal()" style="padding:12px 28px;border-radius:50px;font-weight:600;background:#f0f4f0;color:var(--text-gray);">Batal</button>
                    <button type="submit" style="padding:12px 28px;border-radius:50px;font-weight:600;background:var(--primary);color:var(--white);flex:1;">
                        <i class="fas fa-save"></i> Buat Invoice
                    </button>
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

        // ===== TABS =====
        const tabButtons = document.querySelectorAll('.trans-tabs button');
        const tabContents = {
            semua: document.getElementById('tab-semua'),
            menunggu: document.getElementById('tab-menunggu'),
            diproses: document.getElementById('tab-diproses'),
            lunas: document.getElementById('tab-lunas'),
            batal: document.getElementById('tab-batal'),
        };

        tabButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                tabButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                Object.keys(tabContents).forEach(key => {
                    tabContents[key].classList.remove('active');
                });

                const tabId = this.dataset.tab;
                if (tabContents[tabId]) {
                    tabContents[tabId].classList.add('active');
                }
            });
        });

        // ===== FILTER TABLE =====
        function filterTable() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const method = document.getElementById('filterMethod').value.toLowerCase();
            const date = document.getElementById('filterDate').value;

            const rows = document.querySelectorAll('#transBody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const rowMethod = row.dataset.method ? row.dataset.method.toLowerCase() : '';
                const rowDate = row.dataset.date || '';

                let show = true;

                if (query && !text.includes(query)) show = false;
                if (method && rowMethod !== method) show = false;
                if (date === 'today' && rowDate !== '2026-07-10') show = false;
                if (date === 'week') {
                    const d = new Date(rowDate);
                    const weekStart = new Date('2026-07-05');
                    const weekEnd = new Date('2026-07-11');
                    if (d < weekStart || d > weekEnd) show = false;
                }
                if (date === 'month') {
                    const d = new Date(rowDate);
                    if (d.getMonth() !== 6 || d.getFullYear() !== 2026) show = false;
                }

                row.style.display = show ? '' : 'none';
            });
        }

        // ===== INVOICE MODAL =====
        function viewInvoice(id) {
            const modal = document.getElementById('invoiceModal');
            document.getElementById('invNumber').textContent = '#' + id;
            document.getElementById('invStatus').textContent = 'Menunggu';
            document.getElementById('invCustomer').textContent = 'Fatimah A.';
            document.getElementById('invDate').textContent = '10 Juli 2026';
            document.getElementById('invPackage').textContent = 'Pro';
            document.getElementById('invMethod').textContent = 'QRIS';
            document.getElementById('invDue').textContent = '12 Juli 2026';
            document.getElementById('invStatusText').textContent = 'Menunggu Pembayaran';
            document.getElementById('invTotal').textContent = 'Rp 450.000';

            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeInvoiceModal() {
            document.getElementById('invoiceModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        // ===== NEW INVOICE MODAL =====
        function openNewInvoice() {
            document.getElementById('newInvoiceModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeNewInvoiceModal() {
            document.getElementById('newInvoiceModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        function saveNewInvoice(e) {
            e.preventDefault();
            showToast('Invoice baru berhasil dibuat!');
            closeNewInvoiceModal();
        }

        // Close modals on backdrop click
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('open');
                    document.body.style.overflow = '';
                }
            });
        });

        // ===== LOGOUT =====
        function confirmLogout(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin logout?')) {
                showToast('Anda telah logout.');
                window.location.href = '#';
            }
        }

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

        // ===== ESCAPE KEY =====
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeInvoiceModal();
                closeNewInvoiceModal();
            }
        });
</script>
@endpush
