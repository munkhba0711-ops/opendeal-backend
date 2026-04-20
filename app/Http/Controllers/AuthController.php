<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Ирж буй мэдээллийг шалгах (Validation) - Монгол мессежтэй
        $validator = Validator::make($request->all(), [
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            // ШИНЭ: Алдаа гарсан үед буцаах Монгол мессежүүд
            'email.unique' => 'Энэ имэйл хаяг аль хэдийн бүртгэлтэй байна. Та нэвтэрч орно уу.',
            'password.min' => 'Нууц үг хамгийн багадаа 8 тэмдэгт байх ёстой.',
            'password.confirmed' => 'Нууц үгнүүд хоорондоо таарахгүй байна.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Мэдээлэл дутуу эсвэл буруу байна.',
                'errors' => $validator->errors() // Энэ дотор нөгөө Монгол мессеж маань буцна
            ], 422);
        }

        // 2. Хэрэглэгчийг баазад бүртгэх
        $user = User::create([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'name' => $request->last_name . ' ' . $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->account_type ?? 'buyer',
        ]);

        return response()->json([
            'message' => 'Бүртгэл амжилттай үүслээ!',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // PostgreSQL дээр индекс ашиглан хурдан хайх
        $user = User::whereRaw('LOWER(email) = ?', [strtolower($request->email)])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Имэйл эсвэл нууц үг буруу байна.'
            ], 401);
        }

        // Token үүсгэхээс өмнө хуучин токеныг цэвэрлэвэл хурдан байна
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Амжилттай нэвтэрлээ!',
            'access_token' => $token,
            'user' => $user
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        // Laravel-ийн Notification ашиглан Queue-д орж имэйл илгээгдэнэ
        \Illuminate\Support\Facades\Password::sendResetLink($request->only('email'));
        return response()->json(['message' => 'Нууц үг сэргээх линк имэйлээр илгээгдлээ.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => \Illuminate\Support\Facades\Hash::make($password)])->save();
            }
        );

        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? response()->json(['message' => 'Нууц үг амжилттай шинэчлэгдлээ.'])
            : response()->json(['error' => 'Линк хүчингүй байна.'], 400);
    }
}
