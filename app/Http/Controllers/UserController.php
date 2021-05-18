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
            ], 200);
        }

        return response()->json([
            'token' => $user->createToken('Personal Access Token', ['admin'])->plainTextToken,
        ], 200);
    }

    public function doLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout Successful',
        ]);
    }
}
