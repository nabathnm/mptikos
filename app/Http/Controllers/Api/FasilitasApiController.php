<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class FasilitasApiController extends Controller
{
    #[OA\Get(
        path: "/api/fasilitas",
        summary: "Daftar fasilitas (publik)",
        tags: ["Fasilitas"],
        parameters: [
            new OA\Parameter(name: "per_page", in: "query", description: "Jumlah data per halaman", schema: new OA\Schema(type: "integer", default: 10))
        ],
        responses: [
            new OA\Response(response: 200, description: "Berhasil mengambil daftar fasilitas")
        ]
    )]
    public function index(Request $request)
    {
        $fasilitas = Fasilitas::orderBy('nama_fasilitas')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Daftar fasilitas berhasil diambil',
            'data' => $fasilitas,
        ]);
    }

    #[OA\Get(
        path: "/api/fasilitas/{id}",
        summary: "Detail fasilitas (publik)",
        tags: ["Fasilitas"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID Fasilitas", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Berhasil mengambil detail fasilitas"),
            new OA\Response(response: 404, description: "Fasilitas tidak ditemukan")
        ]
    )]
    public function show($id)
    {
        $fasilitas = Fasilitas::find($id);

        if (! $fasilitas) {
            return response()->json([
                'success' => false,
                'message' => 'Fasilitas tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail fasilitas berhasil diambil',
            'data' => $fasilitas,
        ]);
    }

    #[OA\Post(
        path: "/api/fasilitas",
        summary: "Tambah fasilitas baru (hanya admin)",
        tags: ["Fasilitas"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nama_fasilitas"],
                properties: [
                    new OA\Property(property: "nama_fasilitas", type: "string", example: "Kolam Renang")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Fasilitas berhasil ditambahkan"),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request)
    {
        if (! Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat menambah fasilitas',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_fasilitas' => ['required', 'string', 'max:255', 'unique:fasilitas,nama_fasilitas'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $fasilitas = Fasilitas::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas berhasil ditambahkan',
            'data' => $fasilitas,
        ], 201);
    }

    #[OA\Patch(
        path: "/api/fasilitas/{id}",
        summary: "Update fasilitas (hanya admin)",
        tags: ["Fasilitas"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID Fasilitas", schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nama_fasilitas"],
                properties: [
                    new OA\Property(property: "nama_fasilitas", type: "string", example: "WiFi 100Mbps")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Fasilitas berhasil diperbarui"),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 404, description: "Fasilitas tidak ditemukan")
        ]
    )]
    public function update(Request $request, $id)
    {
        if (! Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat mengubah fasilitas',
            ], 403);
        }

        $fasilitas = Fasilitas::find($id);

        if (! $fasilitas) {
            return response()->json([
                'success' => false,
                'message' => 'Fasilitas tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_fasilitas' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fasilitas', 'nama_fasilitas')->ignore($fasilitas->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $fasilitas->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas berhasil diperbarui',
            'data' => $fasilitas,
        ]);
    }

    #[OA\Delete(
        path: "/api/fasilitas/{id}",
        summary: "Hapus fasilitas (hanya admin)",
        tags: ["Fasilitas"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID Fasilitas", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Fasilitas berhasil dihapus"),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 404, description: "Fasilitas tidak ditemukan")
        ]
    )]
    public function destroy($id)
    {
        if (! Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat menghapus fasilitas',
            ], 403);
        }

        $fasilitas = Fasilitas::find($id);

        if (! $fasilitas) {
            return response()->json([
                'success' => false,
                'message' => 'Fasilitas tidak ditemukan',
            ], 404);
        }

        $fasilitas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas berhasil dihapus',
        ]);
    }
}
