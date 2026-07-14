<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private const PER_PAGE = 10;

    /**
     * List Admin utk halaman Manajemen User: search (nama/email) + filter role/status,
     * selalu urut terbaru dulu. Cuma role Admin/Super Admin yang tampil di sini
     * (tab Admin) — belum ada user Guru/Murid, disiapkan utk update berikutnya.
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return User::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    /**
     * Tambah akun Admin baru. Status selalu Aktif (default), created_by dicatat.
     * Hanya Super Admin yang boleh memanggil ini (dicek business rule).
     */
    public function createAdmin(array $data, User $actor): User
    {
        $this->assertCanCreateAdmin($actor);

        return User::create([
            'name' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'status' => User::STATUS_ACTIVE,
            'created_by' => $actor->id,
        ]);
    }

    /**
     * Update akun Admin. Password cuma diubah kalau field password diisi.
     * updated_by dicatat. Business rule dicek sebelum data disentuh.
     */
    public function updateAdmin(User $target, array $data, User $actor): User
    {
        $this->assertCanUpdateAdmin($actor, $target, $data);

        $payload = [
            'name' => $data['nama'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status'],
            'updated_by' => $actor->id,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $target->update($payload);

        return $target->refresh();
    }

    /**
     * Soft delete akun Admin. Business rule dicek sebelum hapus.
     */
    public function deleteAdmin(User $target, User $actor): void
    {
        $this->assertCanDeleteAdmin($actor, $target);

        $target->delete();
    }

    /**
     * Jumlah Super Admin aktif — dipakai guard "minimal 1 Super Admin aktif".
     */
    public function activeSuperAdminCount(): int
    {
        return User::where('role', User::ROLE_SUPER_ADMIN)
            ->where('status', User::STATUS_ACTIVE)
            ->count();
    }

    /**
     * BUSINESS RULE — hanya Super Admin yang boleh membuat Admin.
     */
    private function assertCanCreateAdmin(User $actor): void
    {
        if ($actor->role !== User::ROLE_SUPER_ADMIN) {
            throw new \RuntimeException('Hanya Super Admin yang bisa menambah akun Admin.');
        }
    }

    /**
     * BUSINESS RULE — hanya Super Admin yang boleh edit Admin (termasuk Admin lain
     * atau dirinya sendiri), tidak boleh ubah role/nonaktifkan diri sendiri, dan
     * sistem harus selalu punya minimal 1 Super Admin aktif.
     */
    private function assertCanUpdateAdmin(User $actor, User $target, array $data): void
    {
        if ($actor->role !== User::ROLE_SUPER_ADMIN) {
            throw new \RuntimeException('Anda tidak memiliki izin mengubah data Admin.');
        }

        $isSelf = $actor->id === $target->id;

        if ($isSelf && $data['role'] !== $target->role) {
            throw new \RuntimeException('Super Admin tidak bisa mengubah role akun sendiri.');
        }

        if ($isSelf && $data['status'] !== User::STATUS_ACTIVE) {
            throw new \RuntimeException('Super Admin tidak bisa menonaktifkan akun sendiri.');
        }

        $targetIsActiveSuperAdmin = $target->role === User::ROLE_SUPER_ADMIN && $target->status === User::STATUS_ACTIVE;
        $willLoseSuperAdminStatus = $data['role'] !== User::ROLE_SUPER_ADMIN || $data['status'] !== User::STATUS_ACTIVE;

        if ($targetIsActiveSuperAdmin && $willLoseSuperAdminStatus && $this->activeSuperAdminCount() <= 1) {
            throw new \RuntimeException('Sistem harus memiliki minimal 1 Super Admin aktif.');
        }
    }

    /**
     * BUSINESS RULE — hanya Super Admin yang boleh hapus Admin, tidak boleh hapus
     * akun sendiri, dan sistem harus selalu punya minimal 1 Super Admin aktif.
     */
    private function assertCanDeleteAdmin(User $actor, User $target): void
    {
        if ($actor->role !== User::ROLE_SUPER_ADMIN) {
            throw new \RuntimeException('Anda tidak memiliki izin menghapus Admin.');
        }

        if ($actor->id === $target->id) {
            throw new \RuntimeException('Tidak bisa menghapus akun sendiri.');
        }

        $targetIsActiveSuperAdmin = $target->role === User::ROLE_SUPER_ADMIN && $target->status === User::STATUS_ACTIVE;

        if ($targetIsActiveSuperAdmin && $this->activeSuperAdminCount() <= 1) {
            throw new \RuntimeException('Sistem harus memiliki minimal 1 Super Admin aktif.');
        }
    }
}
