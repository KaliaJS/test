<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

class OptionalSanctumAuth
{
    private string $appNamespace;
    private const AUTH_ERROR = ['message' => 'Bad Authentication'];

    public function __construct()
    {
        $this->appNamespace = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'app.jimjim.bio')->toString();
    }

    public function handle(Request $request, Closure $next): Response
    {
        Auth::shouldUse('sanctum');

        if ($user = $request->user()) {
            return $next($request);
        }

        $guestId = $request->header('Guest-Id');
        $fingerprint = $request->header('Build-Hash');

        if (!$guestId || !$fingerprint) {
            return response()->json(self::AUTH_ERROR, 401);
        }

        if (!hash_equals(Uuid::uuid5($this->appNamespace, $fingerprint)->toString(), $guestId)) {
            return response()->json(self::AUTH_ERROR, 401);
        }

        $request->setUserResolver(
            fn () => (object) ['id' => null, 'guest_id' => $guestId]
        );

        return $next($request);
    }
}
