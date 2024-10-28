<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="User's name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="password_confirmation",
     *          in="query",
     *          description="Confirmation of the user's password. Must match the password field.",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Response(response="201", description="User registered successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function register(Request $request)
    {
        //validate user details
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/[a-zA-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],
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

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login a user",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Access token generated successfully"),
     *     @OA\Response(response="401", description="Invalid credentials"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse(false, $validator->errors()->first(), $validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponse(false, 'The selected email is invalid.', null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

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

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout a user",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="202", description="Access token deleted successfully"),
     *     @OA\Response(response="422", description="Validation errors"),
     *     @OA\Response(response="404", description="User not found")
     * )
     */
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

        return ApiResponse(true, 'Access Token Deleted', null, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     summary="Send password reset link to the user",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Password reset link sent successfully"),
     *     @OA\Response(response="422", description="Validation errors"),
     *     @OA\Response(response="404", description="User not found")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/reset-password",
     *     summary="Reset user's password",
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="Password reset token",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="password",
     *          in="query",
     *          description="New password for the user",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *           name="password_confirmation",
     *           in="query",
     *           description="Confirmation of the new password. Must match the password field.",
     *           required=true,
     *           @OA\Schema(type="string")
     *       ),
     *     @OA\Response(response="200", description="Password reset successfully"),
     *     @OA\Response(response="422", description="Validation errors"),
     *     @OA\Response(response="400", description="Bad request")
     * )
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/[a-zA-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],
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
