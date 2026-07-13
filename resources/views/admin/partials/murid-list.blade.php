<div class="table-wrap">
    <table id="muridTable">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>WhatsApp</th>
                <th>Level Belajar</th>
                <th>Paket</th>
                <th>Status</th>
                <th>Waktu Daftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($muridList as $murid)
                <tr>
                    <td>{{ $murid->nama }}</td>
                    <td>{{ $murid->email }}</td>
                    <td>{{ $murid->whatsapp }}</td>
                    <td>{{ $murid->level_belajar }}</td>
                    <td>{{ $murid->paket }}</td>
                    <td>
                        <span class="status-badge {{ $murid->status === \App\Models\Murid::STATUS_DAFTAR ? 'pending' : 'active' }}">
                            {{ $murid->status }}
                        </span>
                    </td>
                    <td>{{ $murid->created_at?->format('d M Y H:i') }}</td>
                    <td>
                        <div class="table-actions">
                            <button type="button" title="Detail" onclick="openDetailModal({{ $murid->id }})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" title="Edit"
                                onclick="openEditModal({{ $murid->id }}, '{{ addslashes($murid->nama) }}', '{{ addslashes($murid->email) }}', '{{ $murid->whatsapp }}', '{{ $murid->level_belajar }}', '{{ $murid->paket }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="danger" title="Hapus" onclick="deleteMurid({{ $murid->id }}, '{{ addslashes($murid->nama) }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:var(--text-light);">
                        Belum ada data murid. Klik &quot;Tambah Murid&quot; untuk mulai.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($muridList->total() > 0)
    <div class="pagination">
        <span class="info">Menampilkan {{ $muridList->firstItem() }}-{{ $muridList->lastItem() }} dari {{ $muridList->total() }} murid</span>
        <div class="pages">
            <button type="button" data-page="{{ max($muridList->currentPage() - 1, 1) }}" {{ $muridList->onFirstPage() ? 'disabled' : '' }}>&lsaquo;</button>
            @for ($page = 1; $page <= $muridList->lastPage(); $page++)
                @if ($page === 1 || $page === $muridList->lastPage() || abs($page - $muridList->currentPage()) <= 1)
                    <button type="button" class="{{ $page === $muridList->currentPage() ? 'active' : '' }}" data-page="{{ $page }}">{{ $page }}</button>
                @elseif ($page === 2 || $page === $muridList->lastPage() - 1)
                    <button type="button" disabled>&hellip;</button>
                @endif
            @endfor
            <button type="button" data-page="{{ min($muridList->currentPage() + 1, $muridList->lastPage()) }}" {{ $muridList->currentPage() >= $muridList->lastPage() ? 'disabled' : '' }}>&rsaquo;</button>
        </div>
    </div>
@endif
