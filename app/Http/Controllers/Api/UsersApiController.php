<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use OpenApi\Attributes as OA;

class UsersApiController extends Controller
{
    protected function ensureAdmin(Request $request)
    {
        if (! $request->user() || $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat mengakses resource ini',
            ], 403);
        }

        return null;
    }

    #[OA\Get(
        path: "/api/users",
        summary: "Daftar semua user (hanya admin)",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "q", in: "query", description: "Pencarian nama/email", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "role", in: "query", description: "Filter berdasarkan role", schema: new OA\Schema(type: "string", enum: ["admin", "pemilik", "user"])),
            new OA\Parameter(name: "per_page", in: "query", description: "Jumlah data per halaman", schema: new OA\Schema(type: "integer", default: 10))
        ],
        responses: [
            new OA\Response(response: 200, description: "Berhasil mengambil daftar user"),
            new OA\Response(response: 403, description: "Forbidden")
        ]
    )]
    public function index(Request $request)
    {
        if ($resp = $this->ensureAdmin($request)) {
            return $resp;
        }

        $query = User::query();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->q}%")
                    ->orWhere('email', 'like', "%{$request->q}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Daftar user berhasil diambil',
            'data' => $users,
        ]);
    }

    #[OA\Get(
        path: "/api/users/{id}",
        summary: "Detail satu user (hanya admin)",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID User", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Berhasil mengambil detail user"),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 404, description: "User tidak ditemukan")
        ]
    )]
    public function show(Request $request, $id)
    {
        if ($resp = $this->ensureAdmin($request)) {
            return $resp;
        }

        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail user berhasil diambil',
            'data' => $user,
        ]);
    }

    #[OA\Post(
        path: "/api/users",
        summary: "Tambah user baru (hanya admin)",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nama", "email", "password", "password_confirmation", "role"],
                properties: [
                    new OA\Property(property: "nama", type: "string", example: "Admin Baru"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin2@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "role", type: "string", enum: ["admin", "pemilik", "user"], example: "user"),
                    new OA\Property(property: "no_telepon", type: "string", example: "08123456789")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "User berhasil ditambahkan"),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 409, description: "Admin sudah ada"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request)
    {
        if ($resp = $this->ensureAdmin($request)) {
            return $resp;
        }

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,pemilik,user'],
            'no_telepon' => ['nullable', 'string', 'max:50'],
        ]);

        if ($data['role'] === 'admin' && User::where('role', 'admin')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Admin hanya boleh satu akun.',
            ], 409);
        }

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $user,
        ], 201);
    }

    #[OA\Patch(
        path: "/api/users/{id}",
        summary: "Update user (hanya admin)",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID User", schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nama", type: "string"),
                    new OA\Property(property: "email", type: "string", format: "email"),
                    new OA\Property(property: "password", type: "string", format: "password"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password"),
                    new OA\Property(property: "role", type: "string", enum: ["admin", "pemilik", "user"]),
                    new OA\Property(property: "no_telepon", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "User berhasil diperbarui"),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 404, description: "User tidak ditemukan")
        ]
    )]
    public function update(Request $request, $id)
    {
        if ($resp = $this->ensureAdmin($request)) {
            return $resp;
        }

        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,pemilik,user'],
            'no_telepon' => ['nullable', 'string', 'max:50'],
        ]);

        if ($data['role'] === 'admin' && User::where('role', 'admin')->where('id', '!=', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Admin hanya boleh satu akun.',
            ], 409);
        }

        $user->fill([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'role' => $data['role'],
            'no_telepon' => $data['no_telepon'],
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diperbarui',
            'data' => $user,
        ]);
    }

    #[OA\Delete(
        path: "/api/users/{id}",
        summary: "Hapus user (hanya admin)",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID User", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "User berhasil dihapus"),
            new OA\Response(response: 400, description: "Tidak dapat menghapus diri sendiri"),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 404, description: "User tidak ditemukan")
        ]
    )]
    public function destroy(Request $request, $id)
    {
        if ($resp = $this->ensureAdmin($request)) {
            return $resp;
        }

        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        if ($request->user()->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun sendiri.',
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus',
        ]);
    }
}
