<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
    }

    public function show(string $id)
    {
        try
        {
            $user = Auth::user();
            return (new FilmResource($this->userRepository->getById($user->id)))->response()->setStatusCode(OK);        
        }
        catch(QueryException $ex)
        {
            abort(NOT_FOUND, "User Not found");
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, $ex->getMessage());
        }
    }
}
