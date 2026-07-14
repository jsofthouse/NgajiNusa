<div class="table-wrap">
    <table id="userTable">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last Login</th>
                <th>Created At</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($userList as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <span class="status-badge {{ $user->status === \App\Models\User::STATUS_ACTIVE ? 'active' : 'nonaktif' }}">
                            {{ $user->status }}
                        </span>
                    </td>
                    <td>{{ $user->last_login_at?->format('d M Y H:i') ?? '-' }}</td>
                    <td>{{ $user->created_at?->format('d M Y H:i') }}</td>
                    <td>
                        <div class="table-actions">
                            <button type="button" title="Detail" onclick="openDetailModal({{ $user->id }})">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if (auth()->user()?->role === \App\Models\User::ROLE_SUPER_ADMIN)
                                <button type="button" title="Edit"
                                    onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}', '{{ $user->status }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if ($user->id === auth()->id())
                                    <button type="button" class="danger" title="Tidak bisa menghapus akun sendiri" disabled style="opacity:0.35;cursor:not-allowed;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <button type="button" class="danger" title="Hapus" onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:var(--text-light);">
                        Belum ada data Admin. Klik &quot;Tambah Admin&quot; untuk mulai.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($userList->total() > 0)
    <div class="pagination">
        <span class="info">Menampilkan {{ $userList->firstItem() }}-{{ $userList->lastItem() }} dari {{ $userList->total() }} user</span>
        <div class="pages">
            <button type="button" data-page="{{ max($userList->currentPage() - 1, 1) }}" {{ $userList->onFirstPage() ? 'disabled' : '' }}>&lsaquo;</button>
            @for ($page = 1; $page <= $userList->lastPage(); $page++)
                @if ($page === 1 || $page === $userList->lastPage() || abs($page - $userList->currentPage()) <= 1)
                    <button type="button" class="{{ $page === $userList->currentPage() ? 'active' : '' }}" data-page="{{ $page }}">{{ $page }}</button>
                @elseif ($page === 2 || $page === $userList->lastPage() - 1)
                    <button type="button" disabled>&hellip;</button>
                @endif
            @endfor
            <button type="button" data-page="{{ min($userList->currentPage() + 1, $userList->lastPage()) }}" {{ $userList->currentPage() >= $userList->lastPage() ? 'disabled' : '' }}>&rsaquo;</button>
        </div>
    </div>
@endif
