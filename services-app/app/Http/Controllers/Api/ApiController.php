<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function Userregister(Request $request)
    {
        $validate = Validator::make($request->all(), [

            'name' => 'required|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validate->errors()
            ], 400);
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $response['name'] = $user->name;
        $response['email'] = $user->email;
        $response['token'] = $user->createToken('MyApp')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'New user Registered Successfully',
            'data' => $response
        ], 200);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fails',
                'message' => $validate->errors()
            ], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $response['token'] = $user->createToken('MyApp')->plainTextToken;
            $response['name'] = $user->name;
            $response['email'] = $user->email;
            $response['role'] = $user->role;

            return response()->json([
                'status' => 'success',
                'message' => 'Login Successfully',
                'data' => $response
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'invalid credentials'
            ], 400);
        }
    }

    public function allUser()
    {
        $users = User::get();
        if (!$users) {
            return response()->json([
                'status' => 'fail',
                'count' => count($users),
                'message' => 'No User Found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count' => count($users),
            'data' => $users
        ], 200);
    }

    public function editUser(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No User Found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|email'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fails',
                'message' => $validate->errors()
            ], 400);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated Successfully',
            'data' => $user
        ]);
    }

    public function deleteUser(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => 'fails',
                'message' => 'No User Found'
            ], 404);
        }

        $user->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User Deleted Successfully'
        ], 200);
    }
}
