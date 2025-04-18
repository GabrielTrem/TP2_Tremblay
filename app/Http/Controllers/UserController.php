<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Repository\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserPasswordRequest;

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

    public function update(UpdateUserPasswordRequest $request)
    {
        try{
            $userId = Auth::user()->id;
            $validatedData =$request->validated();
            $user = $this->userRepository->update($userId, [ 'password' => Hash::make($validatedData['password'])]);
            return (new UserResource($user))->response()->setStatusCode(OK);
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, $ex->getMessage());
        }
    }
}
