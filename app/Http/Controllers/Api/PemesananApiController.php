<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kosan;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class PemesananApiController extends Controller
{
    #[OA\Get(
        path: "/api/pemesanan",
        summary: "Daftar pemesanan milik user yang login",
        tags: ["Pemesanan"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Berhasil mengambil daftar pemesanan"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'pemilik') {
            // Pemilik lihat pemesanan kosan mereka
            $kosanIds = Kosan::where('user_id', $user->id)->pluck('id');
            $pemesanans = Pemesanan::with(['user:id,nama,email,no_telepon', 'kosan:id,nama_kosan,alamat'])
                ->whereIn('kosan_id', $kosanIds)
                ->latest()
                ->get();
        } else {
            // User biasa lihat pemesanan sendiri
            $pemesanans = Pemesanan::with(['kosan:id,nama_kosan,alamat,harga_per_bulan'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar pemesanan berhasil diambil',
            'data'    => $pemesanans,
        ]);
    }

    #[OA\Post(
        path: "/api/pemesanan",
        summary: "Buat pemesanan baru",
        tags: ["Pemesanan"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["kosan_id", "tanggal_masuk", "durasi_bulan"],
                properties: [
                    new OA\Property(property: "kosan_id", type: "integer", example: 1),
                    new OA\Property(property: "tanggal_masuk", type: "string", format: "date", example: "2024-06-01"),
                    new OA\Property(property: "durasi_bulan", type: "integer", minimum: 1, maximum: 24, example: 3),
                    new OA\Property(property: "catatan", type: "string", example: "Mohon segera diproses.")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Pemesanan berhasil dibuat"),
            new OA\Response(response: 400, description: "Kamar penuh atau sudah ada pemesanan aktif"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kosan_id'      => 'required|exists:kosans,id',
            'tanggal_masuk' => 'required|date|after_or_equal:today',
            'durasi_bulan'  => 'required|integer|min:1|max:24',
            'catatan'       => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $kosan = Kosan::find($request->kosan_id);

        if ($kosan->kamar_tersedia <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kamar tidak tersedia (sudah penuh)',
            ], 400);
        }

        // Cek jika user sudah punya pemesanan aktif di kos yang sama
        $sudahAktif = Pemesanan::where('user_id', Auth::id())
            ->where('kosan_id', $kosan->id)
            ->whereIn('status', ['pending', 'disetujui'])
            ->exists();

        if ($sudahAktif) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki pemesanan aktif di kosan ini',
            ], 400);
        }

        $totalHarga = $kosan->harga_per_bulan * $request->durasi_bulan;

        $pemesanan = Pemesanan::create([
            'user_id'       => Auth::id(),
            'kosan_id'      => $kosan->id,
            'tanggal_masuk' => $request->tanggal_masuk,
            'durasi_bulan'  => $request->durasi_bulan,
            'total_harga'   => $totalHarga,
            'status'        => 'pending',
            'catatan'       => $request->catatan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pemesanan berhasil dibuat, menunggu persetujuan pemilik',
            'data'    => $pemesanan->load('kosan:id,nama_kosan,alamat,harga_per_bulan'),
        ], 201);
    }

    #[OA\Get(
        path: "/api/pemesanan/{id}",
        summary: "Detail satu pemesanan",
        tags: ["Pemesanan"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID Pemesanan", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Berhasil mengambil detail pemesanan"),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 404, description: "Pemesanan tidak ditemukan")
        ]
    )]
    public function show($id)
    {
        $user = Auth::user();

        $pemesanan = Pemesanan::with(['kosan', 'user:id,nama,email,no_telepon'])->find($id);

        if (! $pemesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pemesanan tidak ditemukan',
            ], 404);
        }

        // User biasa hanya bisa lihat miliknya
        // Pemilik hanya bisa lihat pemesanan kosan miliknya
        $bolehLihat = ($pemesanan->user_id === $user->id) ||
            ($user->role === 'pemilik' && $pemesanan->kosan->user_id === $user->id);

        if (! $bolehLihat) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pemesanan ini',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pemesanan berhasil diambil',
            'data'    => $pemesanan,
        ]);
    }

    #[OA\Patch(
        path: "/api/pemesanan/{id}",
        summary: "Update status pemesanan",
        description: "Pemilik bisa setuju/tolak, User bisa batalkan",
        tags: ["Pemesanan"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID Pemesanan", schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["status"],
                properties: [
                    new OA\Property(property: "status", type: "string", enum: ["disetujui", "ditolak", "dibatalkan"])
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Status berhasil diperbarui"),
            new OA\Response(response: 400, description: "Status tidak dapat diproses"),
            new OA\Response(response: 403, description: "Forbidden")
        ]
    )]
    public function update(Request $request, $id)
    {
        $pemesanan = Pemesanan::with('kosan')->find($id);

        if (! $pemesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pemesanan tidak ditemukan',
            ], 404);
        }

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:disetujui,ditolak,dibatalkan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Status tidak valid. Pilihan: disetujui, ditolak, dibatalkan',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $statusBaru = $request->status;

        // Logika hak akses berdasarkan role
        if ($statusBaru === 'dibatalkan') {
            // Hanya user pemesan yang bisa batalkan
            if ($pemesanan->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pemesan yang dapat membatalkan pemesanan',
                ], 403);
            }
            if ($pemesanan->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pemesanan berstatus pending yang dapat dibatalkan',
                ], 400);
            }
        } else {
            // disetujui / ditolak — hanya pemilik kosan
            if ($user->role !== 'pemilik' || $pemesanan->kosan->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pemilik kosan yang dapat menyetujui atau menolak pemesanan',
                ], 403);
            }
            if ($pemesanan->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pemesanan berstatus pending yang dapat diproses',
                ], 400);
            }
        }

        $pemesanan->update(['status' => $statusBaru]);

        // Kurangi kamar tersedia jika disetujui
        if ($statusBaru === 'disetujui') {
            $pemesanan->kosan->decrement('kamar_tersedia');
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pemesanan berhasil diperbarui menjadi ' . $statusBaru,
            'data'    => $pemesanan->fresh('kosan'),
        ]);
    }

    #[OA\Delete(
        path: "/api/pemesanan/{id}",
        summary: "Hapus pemesanan (hanya jika pending dan milik sendiri)",
        tags: ["Pemesanan"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID Pemesanan", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Pemesanan berhasil dihapus"),
            new OA\Response(response: 400, description: "Hanya pending yang bisa dihapus"),
            new OA\Response(response: 404, description: "Pemesanan tidak ditemukan")
        ]
    )]
    public function destroy($id)
    {
        $pemesanan = Pemesanan::where('user_id', Auth::id())->find($id);

        if (! $pemesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pemesanan tidak ditemukan',
            ], 404);
        }

        if ($pemesanan->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pemesanan berstatus pending yang dapat dihapus',
            ], 400);
        }

        $pemesanan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pemesanan berhasil dihapus',
        ]);
    }
}
