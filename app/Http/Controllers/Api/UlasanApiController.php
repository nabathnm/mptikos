<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kosan;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class UlasanApiController extends Controller
{
    #[OA\Get(
        path: "/api/ulasan",
        summary: "Daftar ulasan (publik)",
        tags: ["Ulasan"],
        parameters: [
            new OA\Parameter(name: "kosan_id", in: "query", description: "Filter ulasan berdasarkan ID Kosan", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "per_page", in: "query", description: "Jumlah data per halaman", schema: new OA\Schema(type: "integer", default: 10))
        ],
        responses: [
            new OA\Response(response: 200, description: "Berhasil mengambil daftar ulasan")
        ]
    )]
    public function index(Request $request)
    {
        $query = Ulasan::with(['user:id,nama', 'kosan:id,nama_kosan']);

        if ($request->filled('kosan_id')) {
            $query->where('kosan_id', $request->kosan_id);
        }

        $ulasans = $query->latest()->paginate($request->get('per_page', 10));

        $summary = null;
        if ($request->filled('kosan_id')) {
            $rataRating = Ulasan::where('kosan_id', $request->kosan_id)->avg('rating');
            $total = Ulasan::where('kosan_id', $request->kosan_id)->count();
            $summary = [
                'rata_rating' => $rataRating ? round($rataRating, 1) : 0,
                'total' => $total,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil diambil',
            'data'    => $ulasans,
            'summary' => $summary,
        ]);
    }

    #[OA\Get(
        path: "/api/ulasan/{id}",
        summary: "Detail ulasan (publik)",
        tags: ["Ulasan"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID Ulasan", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Berhasil mengambil detail ulasan"),
            new OA\Response(response: 404, description: "Ulasan tidak ditemukan")
        ]
    )]
    public function show($id)
    {
        $ulasan = Ulasan::with(['user:id,nama', 'kosan:id,nama_kosan'])->find($id);

        if (! $ulasan) {
            return response()->json([
                'success' => false,
                'message' => 'Ulasan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail ulasan berhasil diambil',
            'data'    => $ulasan,
        ]);
    }

    #[OA\Post(
        path: "/api/ulasan",
        summary: "Tambah ulasan untuk kosan (harus pernah menyewa)",
        tags: ["Ulasan"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["kosan_id", "rating"],
                properties: [
                    new OA\Property(property: "kosan_id", type: "integer", example: 1),
                    new OA\Property(property: "rating", type: "integer", minimum: 1, maximum: 5, example: 5),
                    new OA\Property(property: "komentar", type: "string", example: "Tempatnya bersih dan nyaman.")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Ulasan berhasil ditambahkan"),
            new OA\Response(response: 403, description: "Hanya penyewa yang bisa mengulas"),
            new OA\Response(response: 409, description: "Sudah pernah mengulas"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kosan_id' => 'required|exists:kosans,id',
            'rating'   => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $kosan = Kosan::find($request->kosan_id);

        if (! $kosan) {
            return response()->json([
                'success' => false,
                'message' => 'Kosan tidak ditemukan',
            ], 404);
        }

        // Cek apakah user pernah menyewa kos ini dan disetujui
        $sudahSewa = Auth::user()
            ->pemesanans()
            ->where('kosan_id', $request->kosan_id)
            ->where('status', 'disetujui')
            ->exists();

        if (! $sudahSewa) {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya dapat mengulas kosan yang pernah Anda sewa dan disetujui',
            ], 403);
        }

        // Cek sudah pernah mengulas atau belum
        $sudahUlas = Ulasan::where('user_id', Auth::id())
            ->where('kosan_id', $request->kosan_id)
            ->exists();

        if ($sudahUlas) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan ulasan untuk kosan ini',
            ], 409);
        }

        $ulasan = Ulasan::create([
            'user_id'  => Auth::id(),
            'kosan_id' => $request->kosan_id,
            'rating'   => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil ditambahkan',
            'data'    => $ulasan->load('user:id,nama'),
        ], 201);
    }

    #[OA\Patch(
        path: "/api/ulasan/{id}",
        summary: "Update ulasan milik sendiri",
        tags: ["Ulasan"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID Ulasan", schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "rating", type: "integer", minimum: 1, maximum: 5),
                    new OA\Property(property: "komentar", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Ulasan berhasil diperbarui"),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 404, description: "Ulasan tidak ditemukan")
        ]
    )]
    public function update(Request $request, $id)
    {
        $ulasan = Ulasan::find($id);

        if (! $ulasan) {
            return response()->json([
                'success' => false,
                'message' => 'Ulasan tidak ditemukan',
            ], 404);
        }

        $user = Auth::user();

        if ($ulasan->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengubah ulasan ini',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating'   => 'sometimes|required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $ulasan->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil diperbarui',
            'data'    => $ulasan->fresh('user:id,nama'),
        ]);
    }

    #[OA\Delete(
        path: "/api/ulasan/{id}",
        summary: "Hapus ulasan milik sendiri atau admin",
        tags: ["Ulasan"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID Ulasan", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Ulasan berhasil dihapus"),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 404, description: "Ulasan tidak ditemukan")
        ]
    )]
    public function destroy($id)
    {
        $ulasan = Ulasan::find($id);

        if (! $ulasan) {
            return response()->json([
                'success' => false,
                'message' => 'Ulasan tidak ditemukan',
            ], 404);
        }

        $user = Auth::user();

        if ($ulasan->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus ulasan ini',
            ], 403);
        }

        $ulasan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dihapus',
        ]);
    }
}
