<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use App\Repository\FilmRepositoryInterface;
use App\Http\Requests\CreateFilmRequest;
use App\Http\Requests\UpdateFilmRequest;

class FilmController extends Controller
{   
    private FilmRepositoryInterface $filmRepository;

    public function __construct(FilmRepositoryInterface $filmRepository){
        $this->filmRepository = $filmRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/films",
     *     tags={"Films"},
     *     summary="Create a new film",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "release_year", "length", "description", "rating", "language_id", "special_features", "image"},
     *             @OA\Property(property="title", type="string", maxLength=50),
     *             @OA\Property(property="release_year", type="integer"),
     *             @OA\Property(property="length", type="integer"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="rating", type="string", maxLength=5),
     *             @OA\Property(property="language_id", type="integer"),
     *             @OA\Property(property="special_features", type="string", maxLength=200),
     *             @OA\Property(property="image", type="string", maxLength=40)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Language Not Found"
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
    public function store(CreateFilmRequest $request)
    {
        try
        {
            $film = $this->filmRepository->create($request->validated());
            return (new FilmResource($film))->response()->setStatusCode(CREATED);
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, $ex->getMessage());
        }  
    }

    /**
     * @OA\Put(
     *     path="/api/films/{film_id}",
     *     tags={"Films"},
     *     summary="Update an existing film",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="film_id",
     *         description="film id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "release_year", "length", "description", "rating", "language_id", "special_features", "image"},
     *             @OA\Property(property="title", type="string", maxLength=50),
     *             @OA\Property(property="release_year", type="integer"),
     *             @OA\Property(property="length", type="integer"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="rating", type="string", maxLength=5),
     *             @OA\Property(property="language_id", type="integer"),
     *             @OA\Property(property="special_features", type="string", maxLength=200),
     *             @OA\Property(property="image", type="string", maxLength=40)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Language Not Found"
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
    public function update(UpdateFilmRequest $request, string $id)
    {
        try{
            $film = $this->filmRepository->update($id, $request->validated());
            return (new FilmResource($film))->response()->setStatusCode(OK);
        }
        catch(QueryException $ex){
            abort(NOT_FOUND, 'Film Not Found');
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, $ex->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/film/{film_id}",
     *     tags={"Films"},
     *     summary="Delete an existing film",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="film_id",
     *         description="film id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No Content"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Film Not Found"
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too Many Requests"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $this->filmRepository->deleteFilmInCascade($id);
            return response()->noContent();
        } catch (QueryException $ex) {
            abort(NOT_FOUND, 'Film Not Found');
        }
        catch (Exception $ex) {
            abort(SERVER_ERROR, $ex->getMessage());
        }
    }
}

