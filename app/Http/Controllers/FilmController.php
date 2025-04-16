<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use App\Repository\FilmRepositoryInterface;

class FilmController extends Controller
{   
    private FilmRepositoryInterface $filmRepository;

    public function __construct(FilmRepositoryInterface $filmRepository){
        $this->filmRepository = $filmRepository;
    }

    public function store(StoreUserRequest $request)
    {
        $film = $filmRepository->create($request->validated());
        return (new UserResource($user))->response()->setStatusCode(CREATED);
    }

    public function destroy(string $id)
    {
        try {
            $filmRepository->delete($id);
            return response()->noContent();
        } catch (Exception $e) {
            abort(NOT_FOUND, $e->getMessage());
        }
    }
}

