<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function index()
    {   
        // lay all nguoi co role la user
        $users = User::where('role','user')->get();

        return response()->json($users);
    }


    public function store(Request $request)
    {

        try {

            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json($user, 201);
        } catch (QueryException $e) {
            // kiem tra neu loi lien quan den trung email
            if ($e->getCode() == 23000) { // ma loi cua vi pham rang buoc unique
                return response()->json([
                    'error' => 'Email đã tồn tại'
                ], 400);
            }

            return response()->json([
                'error' => 'Có lỗi xảy ra. Vui lòng thử lại!'
            ], 500);
        }
    }

    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function showProfile(){

        return response()->json(auth()->user());
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if($request->has('password')){
            $request->merge([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->update($request->all());
        return response()->json(['message' => 'User updated successfully']);
    }



    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['message' => 'User deleted successfuly']);
    }
}
