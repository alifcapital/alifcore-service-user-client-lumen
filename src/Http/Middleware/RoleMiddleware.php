<?php


namespace AlifCapital\UserServiceClient\Http\Middleware;


use AlifCapital\UserServiceClient\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$accessRoles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$accessRoles)
    {
        $userRoles = auth()->user()->getRoles();
        $arrIntersect = array_intersect($accessRoles, $userRoles);
        if(count($arrIntersect) <= 0){
            return $this->errorResponse(Response::HTTP_UNAUTHORIZED, Response::$statusTexts[Response::HTTP_UNAUTHORIZED]);
        }
        return $next($request);
    }
}
