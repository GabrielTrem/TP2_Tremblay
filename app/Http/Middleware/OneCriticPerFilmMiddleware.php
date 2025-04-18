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

    public function __construct(UserRepositoryInterface $userRepository){
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
            if($this->userRepository->userHasCriticForFilm($request->user_id, $request->film_id)){
                throw new AlreadyExistingCriticOnFilmException;
            }
            return $next($request);
        }
        catch(QueryException $ex){
            abort(NOT_FOUND, "User Not Found");
        } 
        catch(AlreadyExistingCriticOnFilmException $ex){
            abort($ex->status(), $ex->message());
        } 
    }
}