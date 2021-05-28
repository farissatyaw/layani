<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
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
        if ($user->is_admin == false) {
            return response()->json([
                'token' => $user->createToken('Personal Access Token', ['user'])->plainTextToken,
                'data' => $user,
                'status' => 'User',
            ], 200);
        }

        return response()->json([
            'token' => $user->createToken('Personal Access Token', ['admin'])->plainTextToken,
            'data' => $user,
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
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Register Sukses',
            'user' => $user,
        ], 200);
    }

    public function show()
    {
        return response()->json([
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        $data['password'] = Hash::make($data['password']);
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

    public function leaderboard()
    {
        $users = User::select('name', 'photo', 'exp', 'rank')->orderByDesc('exp')->get();

        return response()->json([
            'users' => $users,
        ], 200);
    }

    public function checkRank()
    {
        return response()->json([
            'userexp' => User::select('id', 'exp', 'rank')->where('id', auth()->user()->id)->get(),
        ]);
    }

    public function activitylog()
    {
        $logs = auth()->user()->logs;

        foreach ($logs as $log) {
            $log->complaint_id = Complaint::find($log->complaint_id);
        }

        return response()->json([
            'logs' => $logs,
        ], );
    }
}
