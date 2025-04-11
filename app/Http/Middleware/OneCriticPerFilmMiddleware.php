<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepositoryInterface;
use App\Exceptions\AlreadyExistingCriticOnFilmException;

class OneCriticPerFilmMiddleware
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $roleRepository){
        $this->userRepository = $userRepository;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            if($userRepository->getCriticByFilmId() != null){
                throw new AlreadyExistingCriticOnFilmException;
            }
            return $next($request);
        }
        catch(AlreadyExistingCriticOnFilmException $ex){
            abort($ex->status(), $ex->message());
        } 
    }
}
