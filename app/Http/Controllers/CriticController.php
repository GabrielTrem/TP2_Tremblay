<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Critic;
use App\Http\Resources\CriticResource;
use App\Repository\CriticRepositoryInterface;
use App\Http\Requests\CreateCriticRequest;

class CriticController extends Controller
{
    private CriticRepositoryInterface $criticRepository;

    public function __construct(CriticRepositoryInterface $criticRepository){
        $this->criticRepository = $criticRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/critics",
     *     tags={"Critics"},
     *     summary="Create a new critic",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"score", "comment", "user_id", "film_id"},
     *             @OA\Property(property="score", type="number", format="float"),
     *             @OA\Property(property="comment", type="string"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="film_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sucessful"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movie or User Not Found"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Existing Critic On Specific Movie"
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
    public function store(CreateCriticRequest $request)
    {
        try{
            $critic = $this->criticRepository->create($request->validated());
            return (new CriticResource($critic))->response()->setStatusCode(CREATED);
        }
        catch (Exception $ex){
            abort(SERVER_ERROR, $ex->getMessage());
        }
    }
}
