<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTransaksiCatatanRequest;
use App\Http\Requests\VerifyTransaksiRequest;
use App\Models\Transaksi;
use App\Services\TransaksiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminTransaksiController extends Controller
{
    public function index(Request $request, TransaksiService $transaksiService): View|JsonResponse
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => (string) $request->query('status', 'semua'),
            'paket' => (string) $request->query('paket', ''),
            'metode_pembayaran' => (string) $request->query('metode_pembayaran', ''),
            'date_from' => (string) $request->query('date_from', ''),
            'date_to' => (string) $request->query('date_to', ''),
        ];

        $transaksiList = $transaksiService->paginate($filters);
        $summary = $transaksiService->dashboardSummary();
        $counts = $transaksiService->statusCounts();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.partials.transaksi-list', ['transaksiList' => $transaksiList])->render(),
                'total' => $transaksiList->total(),
                'summary' => $summary,
                'counts' => $counts,
            ]);
        }

        return view('admin.transaksi', [
            'transaksiList' => $transaksiList,
            'filters' => $filters,
            'summary' => $summary,
            'counts' => $counts,
        ]);
    }

    public function show(Transaksi $transaksi, TransaksiService $transaksiService): JsonResponse
    {
        $transaksiService->markOpened($transaksi, auth()->user());

        $transaksi->load(['murid', 'verifiedBy', 'activities.causer']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $transaksi->id,
                'invoice_number' => $transaksi->invoice_number,
                'jenis' => $transaksi->jenis,
                'paket' => $transaksi->paket,
                'nominal' => $transaksi->nominal,
                'metode_pembayaran' => $transaksi->metode_pembayaran,
                'metode_pembayaran_label' => Transaksi::METODE_LABELS[$transaksi->metode_pembayaran] ?? $transaksi->metode_pembayaran,
                'status' => $transaksi->status,
                'status_label' => Transaksi::STATUS_LABELS[$transaksi->status] ?? $transaksi->status,
                'catatan_internal' => $transaksi->catatan_internal,
                'created_at' => $transaksi->created_at?->format('d M Y H:i'),
                'verified_at' => $transaksi->verified_at?->format('d M Y H:i'),
                'verified_by' => $transaksi->verifiedBy->name ?? null,
                'has_bukti_transfer' => $transaksi->bukti_path !== null,
                'bukti_original_filename' => $transaksi->bukti_original_filename,
                'murid' => [
                    'nama' => $transaksi->murid->nama ?? '-',
                    'whatsapp' => $transaksi->murid->whatsapp ?? '-',
                    'email' => $transaksi->murid->email ?? '-',
                    'paket' => $transaksi->murid->paket ?? '-',
                ],
                'activities' => $transaksi->activities->map(fn ($activity) => [
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'causer' => $activity->causer->name ?? 'Sistem',
                    'created_at' => $activity->created_at?->format('d M Y H:i'),
                ]),
            ],
        ]);
    }

    public function verify(VerifyTransaksiRequest $request, Transaksi $transaksi, TransaksiService $transaksiService): JsonResponse
    {
        if ($transaksi->status === Transaksi::STATUS_BERHASIL) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi ini sudah diverifikasi sebelumnya.',
            ], 422);
        }

        $transaksiService->verifyPayment(
            $transaksi,
            $request->file('bukti_transfer'),
            $request->input('catatan_internal'),
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diverifikasi.',
        ]);
    }

    public function reject(Request $request, Transaksi $transaksi, TransaksiService $transaksiService): JsonResponse
    {
        if ($transaksi->status === Transaksi::STATUS_BERHASIL) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi yang sudah berhasil tidak bisa ditolak.',
            ], 422);
        }

        if ($transaksi->status === Transaksi::STATUS_DITOLAK) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi ini sudah ditolak sebelumnya.',
            ], 422);
        }

        $transaksiService->reject($transaksi, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil ditolak.',
        ]);
    }

    public function updateCatatan(UpdateTransaksiCatatanRequest $request, Transaksi $transaksi, TransaksiService $transaksiService): JsonResponse
    {
        $transaksiService->updateCatatan($transaksi, $request->input('catatan_internal'), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Catatan internal berhasil disimpan.',
        ]);
    }

    /**
     * Tampilkan bukti transfer inline (dipakai <img> preview & tombol "Lihat").
     */
    public function previewBukti(Transaksi $transaksi)
    {
        abort_if($transaksi->bukti_path === null, 404);

        return Storage::disk('local')->response($transaksi->bukti_path);
    }

    /**
     * Download bukti transfer dengan nama file asli (tombol "Download").
     */
    public function downloadBukti(Transaksi $transaksi)
    {
        abort_if($transaksi->bukti_path === null, 404);

        return Storage::disk('local')->download(
            $transaksi->bukti_path,
            $transaksi->bukti_original_filename ?? 'bukti-transfer.jpg'
        );
    }
}
