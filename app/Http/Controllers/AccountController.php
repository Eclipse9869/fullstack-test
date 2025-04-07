<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Account API",
 *     version="1.0.0",
 *     description="Dokumentasi API untuk manajemen akun"
 * )
 *
 * @OA\Tag(
 *     name="Accounts",
 *     description="Endpoints untuk operasi akun"
 * )
 */
class AccountController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/accounts",
     *     summary="Ambil semua akun",
     *     tags={"Accounts"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil ambil semua akun"
     *     )
     * )
     */
    public function index()
    {
        $users = Account::all();

        if (request()->wantsJson()) {
            return response()->json(['data' => $users]);
        }

        return view('listAccount', compact('users'));
    }

    /**
     * @OA\Post(
     *     path="/api/accounts",
     *     summary="Tambah akun baru",
     *     tags={"Accounts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "age"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="age", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User berhasil ditambahkan"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'age' => 'required|numeric|min:1'
        ]);

        Account::create([
            'name' => $request->name,
            'email' => $request->email,
            'age' => $request->age,
        ]);
        
        return response()->json(['message' => 'User berhasil ditambahkan']);
    }

    /**
     * @OA\Put(
     *     path="/api/accounts/{id}",
     *     summary="Update akun",
     *     tags={"Accounts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "age"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="age", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User berhasil diupdate"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'age' => 'required|numeric|min:1'
        ]);

        $users = Account::findOrFail($id);
        $users->update([
            'name' => $request->name,
            'email' => $request->email,
            'age' => $request->age,
        ]);

        return response()->json(['message' => 'User berhasil diupdate']);
    }

    /**
     * @OA\Delete(
     *     path="/api/accounts/{id}",
     *     summary="Hapus akun",
     *     tags={"Accounts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User berhasil dihapus"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $users = Account::findOrFail($id);
        $users->delete();

        return response()->json(['message' => 'User berhasil dihapus']);
    }

    public function create() {}
    public function show(string $id) {}
    public function edit(string $id)
    {
        $users = Account::findOrFail($id);
        return response()->json($users);
    }

    public function __construct()
    {
        $this->middleware('validateAndLog')->only(['store', 'update']);
    }
}
