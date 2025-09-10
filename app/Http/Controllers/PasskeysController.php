<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Passkey;
use App\Services\PasskeyAuthService;
use App\Services\PasskeyRegisterService;
use App\Services\PasskeyService;
use App\Services\UserAuthService;
use App\Services\UserRegisterService;
use Illuminate\Cache\Cache;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PasskeysController extends Controller
{

    public function __construct(
        private PasskeyService $passkeyService,
        private PasskeyAuthService $passkeyAuthService,
        private PasskeyRegisterService $passkeyRegisterService,
    ){}

    public function registerOptions(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:users,email']);

        return $this->passkeyRegisterService->generateOptions(
            email: $request->email
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'attResp' => 'required|json',
            'uuid' => 'required|string',
            'email' => 'required|email|unique:users',
        ]);

        $publicKeyCredentialSource = $this->passkeyRegisterService->validate(
            attResp: $request->attResp,
            uuid: base64_decode($request->uuid),
            host: $request->getHost()
        );

        $user = UserRegisterService::create($request->email)
            ->withPasskey()
            ->notify()
            ->getUser();

        $auth = UserAuthService::login($request->email)
            ->withPasskey();

        $this->passkeyService->save($user, $publicKeyCredentialSource);

        return ApiResponse::success(
            message: 'Le passkey a été enregistré avec succès',
            data: $auth->getToken()
        );
    }

    public function authenticateOptions(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        return $this->passkeyAuthService->generateOptions(
            email: $request->email
        );
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'attResp' => 'required|json',
            'uuid' => 'required|uuid',
            'email' => 'required|email|exists:users',
        ]);

        $passkey = $this->passkeyAuthService->validate(
            attResp: $request->attResp,
            host: $request->getHost(),
            uuid: $request->uuid,
        );

        $auth = UserAuthService::login($request->email)
            ->withPasskey();

        return ApiResponse::success(
            message: 'Passkey was auhenticated successfully',
            data: $auth->getToken()
        );
    }

    public function destroy(Passkey $passkey)
    {
        Gate::authorize('delete', $passkey);

        $passkey->delete();

        return ApiResponse::noContent();
    }

}
