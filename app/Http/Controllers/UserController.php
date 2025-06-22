<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Untuk validasi unique email saat update

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user.
     */
    public function index(Request $request)
    {
        $query = User::query();
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10); // Default 10 data per halaman

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('UserRole', 'like', '%' . $search . '%');
        }

        $users = $query->orderBy('name', 'asc')->paginate($perPage)->appends($request->except('page'));

        // Daftar role yang mungkin (sesuaikan dengan aplikasi Anda)
        $userRoles = ['Pimpinan', 'Admin', 'Marketing']; // Sesuaikan dengan role yang Anda miliki

        return view('users.index', compact('users', 'search', 'perPage', 'userRoles'));
    }

    /**
     * Menampilkan formulir untuk membuat user baru.
     */
    public function create()
    {
        $userRoles = ['Pimpinan', 'Admin', 'Marketing']; // Daftar role untuk dropdown
        return view('users.create', compact('userRoles'));
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|string|email|min:3|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' akan mencari password_confirmation
            'UserRole' => ['required', 'string', Rule::in(['Pimpinan', 'Admin', 'Marketing'])], // Sesuaikan role
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Password akan dihash oleh mutator di model User
            'UserRole' => $request->UserRole,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail user tertentu.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Menampilkan formulir untuk mengedit user tertentu.
     */
    public function edit(User $user)
    {
        $userRoles = ['Pimpinan', 'Admin', 'Marketing']; // Daftar role untuk dropdown
        return view('users.edit', compact('user', 'userRoles'));
    }

    /**
     * Memperbarui data user tertentu di database.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id), // Email harus unik, kecuali email user itu sendiri
            ],
            'password' => 'nullable|string|min:8|confirmed', // Password opsional, hanya jika ingin diubah
            'UserRole' => ['required', 'string', Rule::in(['Pimpinan', 'Admin', 'Marketing'])], // Sesuaikan role
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'UserRole' => $request->UserRole,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $userData['password'] = $request->password; // Password akan dihash oleh mutator
        }

        $user->update($userData);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Menghapus user tertentu dari database.
     */
    public function destroy(User $user)
    {
        // Pastikan user tidak menghapus dirinya sendiri
        if (auth()->id() == $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}

