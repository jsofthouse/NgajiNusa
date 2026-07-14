<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request, UserService $userService): View|JsonResponse
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'role' => (string) $request->query('role', ''),
            'status' => (string) $request->query('status', ''),
        ];

        $userList = $userService->paginate($filters);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.partials.user-list', ['userList' => $userList])->render(),
                'total' => $userList->total(),
            ]);
        }

        return view('admin.user', [
            'userList' => $userList,
            'filters' => $filters,
        ]);
    }

    public function store(StoreAdminUserRequest $request, UserService $userService): JsonResponse
    {
        try {
            $user = $userService->createAdmin($request->validated(), $request->user());
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Admin berhasil ditambahkan.',
            'data' => ['id' => $user->id],
        ]);
    }

    public function show(User $user): JsonResponse
    {
        $user->load(['createdBy', 'updatedBy']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'nama' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'last_login_at' => $user->last_login_at?->format('d M Y H:i'),
                'created_by' => $user->createdBy->name ?? null,
                'updated_by' => $user->updatedBy->name ?? null,
                'created_at' => $user->created_at?->format('d M Y H:i'),
                'updated_at' => $user->updated_at?->format('d M Y H:i'),
            ],
        ]);
    }

    public function update(UpdateAdminUserRequest $request, User $user, UserService $userService): JsonResponse
    {
        try {
            $userService->updateAdmin($user, $request->validated(), $request->user());
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Admin berhasil diperbarui.',
        ]);
    }

    public function destroy(Request $request, User $user, UserService $userService): JsonResponse
    {
        try {
            $userService->deleteAdmin($user, $request->user());
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Admin berhasil dihapus.',
        ]);
    }
}
