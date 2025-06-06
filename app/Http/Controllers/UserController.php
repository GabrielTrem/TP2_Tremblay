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

    /**
     * @OA\Get(
     *     path="/api/user",
     *     tags={"Users"},
     *     summary="Get current user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too Many Requests",
     *     )
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/api/user",
     *     tags={"Users"},
     *     summary="Update password of current user",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password", "password_confirmation"},
     *             @OA\Property(property="password", type="string", maxLength=255),
     *             @OA\Property(property="password_confirmation", type="string", maxLength=255),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=422, 
     *         description="Invalid data"
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too Many Requests"
     *     )
     * )
     */
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
