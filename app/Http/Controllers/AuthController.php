<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //validate user details
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return ApiResponse(false, $validator->errors()->first(), $validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'logout_time' => now(),
        ]);
        return ApiResponse(true, 'User Created', $user, Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse(false, $validator->errors()->first(), $validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->logout_time === null) {
            return ApiResponse(false, 'User Already Logged-in', null, Response::HTTP_NOT_ACCEPTABLE);
        }

        if (!Hash::check($request->password, $user->password)) {
            return ApiResponse(false, 'Invalid Credentials', null, Response::HTTP_UNAUTHORIZED);
        }

        // Authenticate the user and generate the token
        if (Auth::attempt($request->only('email', 'password'))) {
            $user->update(['logout_time' => null]);
            $authToken = $user->createToken('auth-token')->plainTextToken;
            return ApiResponse(true, 'Access Token Generated', ['token' => $authToken], Response::HTTP_CREATED);
        }

        return ApiResponse(false, 'Invalid Credentials', null, Response::HTTP_UNAUTHORIZED);
    }

    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return ApiResponse(false, $validator->errors()->first(), $validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['logout_time' => now()]);
        $user->tokens()->delete();

        return ApiResponse(true, 'Access Token Deleted', null, Response::HTTP_ACCEPTED);
    }

//    public function resetPassword(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'email' => 'email|required|exists:users,email',
//            'new_password' => 'required',
//        ]);
//        if ($validator->fails()) {
//            return ApiResponse(false, $validator->errors()->first(), $validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
//        }
//        $passwordUpdate = User::where('email', $request->email)->update([
//            'password' => bcrypt($request->new_password),
//        ]);
//        if (!$passwordUpdate) {
//            return ApiResponse(false, 'User Not Found', null, Response::HTTP_NOT_FOUND);
//        }
//
//        return ApiResponse(true, 'Password Updated Successfully', null, Response::HTTP_OK);
//    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return ApiResponse(false, $validator->errors()->first(), $validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Send the reset link
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? ApiResponse(true, __($status), null, Response::HTTP_OK)
            : ApiResponse(false, __($status), null, Response::HTTP_BAD_REQUEST);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return ApiResponse(false, $validator->errors()->first(), $validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Attempt to reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? ApiResponse(true, __($status), null, Response::HTTP_OK)
            : ApiResponse(false, __($status), null, Response::HTTP_BAD_REQUEST);
    }
}
