<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Repository\RoleRepositoryInterface;

class AuthController extends Controller
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository){
        $this->roleRepository = $roleRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/signup",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login","password","email","last_name","first_name"},
     *             @OA\Property(property="login", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="password_confirmation", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="first_name", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sucessful"
     *     ),
     *     @OA\Response(
     *          response=422, 
     *          description="Invalid data"
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too Many Requests"
     *     )
     * )
     */
    public function register(RegisterUserRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::create([
            'login' => $validatedData['login'],
            'password' => Hash::make($validatedData['password']),
            'email' => $validatedData['email'],
            'last_name' => $validatedData['last_name'],
            'first_name' => $validatedData['first_name'],
            'role_id' => $this->roleRepository->getIdByName('user')
        ]);
        return (new UserResource($user))->response()->setStatusCode(CREATED);
    }

    /**
     * @OA\Post(
     *     path="/api/signin",
     *     tags={"Authentication"},
     *     summary="Login user and return token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login","password"},
     *             @OA\Property(property="login", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *          response=401, 
     *          description="Invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too Many Requests"
     *     )
     * )
     */
    public function login(LoginUserRequest $request)
    {      
        if(Auth::attempt($request->validated())){
            $user = Auth::user();
            $token = $user->createToken('login_token');
            return response()->json(['token' => $token->plainTextToken])->setStatusCode(OK);
        }
        abort(UNAUTHORIZED, "You have entered an invalid username or password");
    }

    /**
     * @OA\Post(
     *     path="/api/signout",
     *     tags={"Authentication"},
     *     summary="Logout the current user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=204,
     *         description="No content(successful)"
     *     ),
     *     @OA\Response(
     *          response=401, 
     *          description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too Many Requests"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        try{
            $user = Auth::user();
            $user->tokens()->delete();
            return response()->noContent();    
        }
        catch(Exception $ex){
            abort(SERVER, "Server Error");
        }
    }
}
