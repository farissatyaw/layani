<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function doLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
        }
        if ($user->isAdmin == false) {
            return response()->json([
                'token' => $user->createToken('Personal Access Token', ['user'])->plainTextToken,
                'status' => 'User',
            ], 200);
        }

        return response()->json([
            'token' => $user->createToken('Personal Access Token', ['admin'])->plainTextToken,
            'status' => 'admin',
        ], 200);
    }

    public function doLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout Successful',
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'isAdmin' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->isAdmin,
        ]);

        return response()->json([
            'user' => $user,
        ], 200);
    }

    public function show()
    {
        return response()->json([
            'user' => auth()->user(),
        ]);
    }

    public function update()
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Data '.$user->name.' berhasil diupdate',
            'user' => $user,
        ], 200);
    }

    public function destroy()
    {
        $user = auth()->user();

        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'message' => 'Data '.$user->name.' berhasil di delete',
        ], 200);
    }
}
