<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    //
    public function index()
    {
        // lay all nguoi co role la user
        $users = User::where('role', 'user')->get();

        return response()->json($users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'address' => $user->address,
                'phonenumber' => $user->phonenumber,
                'sex' => $user->sex,
                'birthday' => $user->birthday,
                'avatar' => $user->avatar ? url('/storage/' . $user->avatar) : null
            ];
        }));
    }


    public function store(Request $request)
    {

        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'address' => 'nullable|string|max:255',
                'phonenumber' => 'nullable|string|max:20',
                'sex' => 'nullable|in:male,female,other',
                'birthday' => 'nullable|date',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Xử lý ảnh đại diện
            $avatar = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $avatar = $avatarPath;
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'phonenumber' => $request->phonenumber,
                'sex' => $request->sex,
                'birthday' => $request->birthday,
                'avatar' => $avatar,
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
        $user = User::findOrFail($id);
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'role'=>$user->role,
            'email' => $user->email,
            'password' => $user->password,
            'address' => $user->address,
            'phonenumber' => $user->phonenumber,
            'sex' => $user->sex,
            'birthday' => $user->birthday,
            'created_at'=> $user->created_at,
            'avatar' => $user->avatar ? url('/storage/' . $user->avatar) : null
        ]);
    }

    public function showProfile()
    {
        $user = auth()->user();
        $user->avatar = $user->avatar ? url('/storage/' . $user->avatar) : null;
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phonenumber' => 'nullable|string|max:20',
            'sex' => 'nullable|in:male,female,other',
            'birthday' => 'nullable|date',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        

        if ($request->has('password')) {
            $request->merge([
                'password' => Hash::make($request->password),
            ]);
        }

        if ($request->hasFile('avatar')) {
            // Lưu file vào thư mục `storage/app/public/avatars`
            $path = $request->file('avatar')->store('public/avatars');
        
            // Lưu đường dẫn tương đối vào database (bỏ 'public/')
            $user->avatar = str_replace('public/', '', $path);
        }

        // Lấy dữ liệu từ request, nhưng bỏ qua 'avatar' để tránh ghi đè
        $data = $request->except('avatar');
        $user->update($data);

        return response()->json([
            'message' => 'File uploaded successfully',
            'file_path' => asset('storage/' . $user->avatar), // Trả về URL đầy đủ
        ]);
        // return response()->json(['message' => 'User updated successfully']);
    }



    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['message' => 'User deleted successfuly']);
    }

    
}
