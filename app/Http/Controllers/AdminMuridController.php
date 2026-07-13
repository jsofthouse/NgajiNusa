<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminMuridRequest;
use App\Http\Requests\UpdateAdminMuridRequest;
use App\Models\Murid;
use App\Services\MuridService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminMuridController extends Controller
{
    public function index(Request $request, MuridService $muridService): View|JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $muridList = $muridService->paginate($search !== '' ? $search : null);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.partials.murid-list', ['muridList' => $muridList])->render(),
                'total' => $muridList->total(),
            ]);
        }

        return view('admin.murid', [
            'muridList' => $muridList,
            'search' => $search,
        ]);
    }

    public function store(StoreAdminMuridRequest $request, MuridService $muridService): JsonResponse
    {
        $murid = $muridService->createMurid($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Murid berhasil ditambahkan.',
            'data' => ['id' => $murid->id],
        ]);
    }

    public function show(Murid $murid): JsonResponse
    {
        $murid->load('referralAgent');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $murid->id,
                'nama' => $murid->nama,
                'email' => $murid->email,
                'whatsapp' => $murid->whatsapp,
                'level_belajar' => $murid->level_belajar,
                'paket' => $murid->paket,
                'status' => $murid->status,
                'referral_agent' => $murid->referralAgent->nama ?? null,
                'created_at' => $murid->created_at?->format('d M Y H:i'),
                'updated_at' => $murid->updated_at?->format('d M Y H:i'),
            ],
        ]);
    }

    public function update(UpdateAdminMuridRequest $request, Murid $murid, MuridService $muridService): JsonResponse
    {
        $muridService->updateMurid($murid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Murid berhasil diperbarui.',
        ]);
    }

    public function destroy(Murid $murid): JsonResponse
    {
        $murid->delete();

        return response()->json([
            'success' => true,
            'message' => 'Murid berhasil dihapus.',
        ]);
    }

    /**
     * Export seluruh data murid ke CSV (tanpa filter/search).
     */
    public function export(MuridService $muridService): StreamedResponse
    {
        $muridList = $muridService->allForExport();
        $filename = 'data-murid-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($muridList) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM biar Excel baca UTF-8 dengan benar

            fputcsv($handle, ['ID', 'Nama', 'Email', 'WhatsApp', 'Level Belajar', 'Paket', 'Status', 'Waktu Daftar']);

            foreach ($muridList as $murid) {
                fputcsv($handle, [
                    $murid->id,
                    $murid->nama,
                    $murid->email,
                    $murid->whatsapp,
                    $murid->level_belajar,
                    $murid->paket,
                    $murid->status,
                    $murid->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
