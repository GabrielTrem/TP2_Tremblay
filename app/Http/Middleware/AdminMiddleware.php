<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\RoleRepositoryInterface;
use App\Exceptions\NotAdminException;

class AdminMiddleware
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository){
        $this->roleRepository = $roleRepository;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            $user = Auth::user();
            if($user->role_id != $roleRepository->getIdByName('admin')){
                throw new NotAdminException;
            }
            return $next($request);
        }
        catch(NotAdminException $ex){
            abort($ex->status(), $ex->message());
        } 
    }
}
