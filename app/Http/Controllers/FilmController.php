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

    //For test
    public function index(){
        try{
            return $this->filmRepository->getAll();
        }
        catch (Exception $ex){
            abort(SERVER_ERROR, $ex->getMessage());
        }
    }

    public function store(Request $request)
    {
        try
        {
            $film = $this->filmRepository->create($request->all());
            return (new FilmResource($film))->response()->setStatusCode(CREATED);
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, $ex->getMessage());
        }  
    }

    public function update(Request $request, string $id)
    {
        try{
            $film = $this->filmRepository->update($id, $request->all());
            return (new FilmResource($film))->response()->setStatusCode(OK);
        }
        catch(QueryException $ex){
            abort(NOT_FOUND, 'Film Not Found');
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, $ex->getMessage());
        }
    }

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

