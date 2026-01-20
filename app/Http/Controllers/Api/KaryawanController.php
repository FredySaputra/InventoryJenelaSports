<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

use App\Http\Resources\KaryawanResource;
use App\Http\Requests\StoreKaryawanRequest;
use App\Http\Requests\UpdateKaryawanRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KaryawanController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors'  => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('username', 'password');

        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau Password salah'
            ], 401);
        }

        $user = auth()->guard('api')->user();

        if ($user->role !== 'Karyawan') {
            auth()->guard('api')->logout();

            return response()->json([
                'success' => false,
                'message' => 'Akses Ditolak. Aplikasi ini khusus Karyawan.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'data'    => [
                'user'  => [
                    'id'       => $user->id,
                    'nama'     => $user->nama,
                    'username' => $user->username,
                    'role'     => $user->role,
                ],
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->guard('api')->factory()->getTTL() * 60
            ]
        ], 200);
    }
    public function index()
    {
        $karyawan = User::where('role', 'Karyawan')
            ->orderBy('id', 'desc')
            ->get();

        return KaryawanResource::collection($karyawan);
    }

    public function store(StoreKaryawanRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $lastUser = User::where('id', 'like', 'KRY-%')->orderBy('id', 'desc')->first();

            if (!$lastUser) {
                $newId = 'KRY-001';
            } else {
                $lastNumber = (int) substr($lastUser->id, 4);
                $newNumber = $lastNumber + 1;
                $newId = 'KRY-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            }

            $user = User::create([
                'id'       => $newId,
                'nama'     => $request->nama,
                'noTelp'   => $request->noTelp,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role'     => 'Karyawan'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan',
                'data'    => new KaryawanResource($user)
            ], 201);
        });
    }

    public function update(UpdateKaryawanRequest $request, $id)
    {
        $user = User::where('id', $id)->where('role', 'Karyawan')->firstOrFail();

        $data = [
            'nama'     => $request->nama,
            'noTelp'   => $request->noTelp,
            'username' => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data karyawan berhasil diupdate',
            'data'    => new KaryawanResource($user)
        ]);
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)->where('role', 'Karyawan')->firstOrFail();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil dihapus'
        ]);
    }
}
