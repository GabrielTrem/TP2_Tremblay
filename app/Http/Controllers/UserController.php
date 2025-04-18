<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Repository\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
    }

    public function show()
    {
        try
        {
            $user = Auth::user();
            return (new UserResource($this->userRepository->getById($user->id)))->response()->setStatusCode(OK);        
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, $ex->getMessage());
        }
    }

    public function update(Request $request)
    {
        try{
            $user = Auth::user();
            $validatedData =$request->validated();
            $updatedUser = $userRepository->update($id, [ 'password' => Hash::make($validatedData['password'])]);
            return (new UserResource($updatedUser))->response()->setStatusCode(OK);
        }
        catch(QueryException $ex){
            abort(NOT_FOUND, "User Not Found");
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, $ex->getMessage());
        }
    }
}
