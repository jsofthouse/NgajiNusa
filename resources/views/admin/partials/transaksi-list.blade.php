<div class="table-wrap">
    <table id="transaksiTable">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Murid</th>
                <th>Paket</th>
                <th>Metode Pembayaran</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Waktu Daftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksiList as $transaksi)
                <tr>
                    <td>
                        <span class="invoice-cell">
                            @if (! $transaksi->opened_at)
                                <span class="new-dot" title="Transaksi belum dibuka"></span>
                            @endif
                            <strong>{{ $transaksi->invoice_number }}</strong>
                        </span>
                    </td>
                    <td>{{ $transaksi->murid->nama ?? '-' }}</td>
                    <td>{{ $transaksi->paket }}</td>
                    <td>{{ \App\Models\Transaksi::METODE_LABELS[$transaksi->metode_pembayaran] ?? $transaksi->metode_pembayaran }}</td>
                    <td>Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}</td>
                    <td>
                        <span class="status-badge {{ $transaksi->status }}">
                            {{ \App\Models\Transaksi::STATUS_LABELS[$transaksi->status] ?? $transaksi->status }}
                        </span>
                    </td>
                    <td>{{ $transaksi->created_at?->format('d M Y H:i') }}</td>
                    <td>
                        <div class="table-actions">
                            <button type="button" title="Detail" onclick="openDetailModal({{ $transaksi->id }})">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:var(--text-light);">
                        Belum ada transaksi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($transaksiList->total() > 0)
    <div class="pagination">
        <span class="info">Menampilkan {{ $transaksiList->firstItem() }}-{{ $transaksiList->lastItem() }} dari {{ $transaksiList->total() }} transaksi</span>
        <div class="pages">
            <button type="button" data-page="{{ max($transaksiList->currentPage() - 1, 1) }}" {{ $transaksiList->onFirstPage() ? 'disabled' : '' }}>&lsaquo;</button>
            @for ($page = 1; $page <= $transaksiList->lastPage(); $page++)
                @if ($page === 1 || $page === $transaksiList->lastPage() || abs($page - $transaksiList->currentPage()) <= 1)
                    <button type="button" class="{{ $page === $transaksiList->currentPage() ? 'active' : '' }}" data-page="{{ $page }}">{{ $page }}</button>
                @elseif ($page === 2 || $page === $transaksiList->lastPage() - 1)
                    <button type="button" disabled>&hellip;</button>
                @endif
            @endfor
            <button type="button" data-page="{{ min($transaksiList->currentPage() + 1, $transaksiList->lastPage()) }}" {{ $transaksiList->currentPage() >= $transaksiList->lastPage() ? 'disabled' : '' }}>&rsaquo;</button>
        </div>
    </div>
@endif
