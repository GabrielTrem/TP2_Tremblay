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

    public function store(Request $request)
    {
        $film = $filmRepository->create($request->validated());
        return (new FilmResource($film))->response()->setStatusCode(CREATED);
    }

    public function update(Request $request, string $id)
    {
        try{
            $film = $filmRepository->update($id, $request->validated());
            return (new FilmResource($film))->response()->setStatusCode(OK);
        }
        catch(QueryException $ex){
            abort(NOT_FOUND, 'Film Not Found');
        }
        catch(Exception $ex){
            abort(SERVER, $ex->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $filmRepository->delete($id);
            return response()->noContent();
        } catch (QueryException $ex) {
            abort(NOT_FOUND, 'Film Not Found');
        }
        catch (Exception $ex) {
            abort(SERVER, $ex->getMessage());
        }
    }
}

