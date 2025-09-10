<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Mail\PasskeyResetRequest;
use App\Models\User;
use App\Services\UserAuthService;
use App\Services\UserRegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthsController extends Controller
{
    public function checkIfUserExist(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $isUserExist = User::where('email', $request->email)->exists();

        return new JsonResponse($isUserExist, 200);
    }

    /**
     * Create a new registered user
     */
    public function register(Request $request) {
        $request->validate(        [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()]
        ]);

        $user = UserRegisterService::create($request->email)
            ->withPassword($request->password)
            ->notify()
            ->getUser();

        $token = UserAuthService::create($request->email, $request->password)
            ->createToken($request->header('GuestId'));


        return ApiResponse::success(
            message: 'Passkey was auhenticated successfully',
            data: $auth->getToken()
        );
        return new JsonResponse([
            'token' => $token,
            'user' => new UserResource($user)
        ], 200);
    }

    /**
     * Login an authenticated token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()]
        ]);

        $userAuthService = UserAuthService::create($request->email, $request->password);
        $token = $userAuthService->renewToken(name: $request->header('GuestId'));

        return new JsonResponse([
            'token' => (string) $token,
            'user' => new UserResource($userAuthService->getUser())
        ], 200);
    }

    /**
     * Destroy the current token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        // $request->user()->tokens()->delete();

        return new JsonResponse('Successfully logged out from this device', 200);
    }

    public function resetRequest(Request $request, UserAuthService $userAuthService)
    {
        $request->validate(['email' => 'required|email']);

        $token = $userAuthService->createResetToken($request->email);

        Mail::to($request->email)->send(new PasskeyResetRequest($token));

        return new ApiSuccessResponse('Reset link has been sent');
    }
}
