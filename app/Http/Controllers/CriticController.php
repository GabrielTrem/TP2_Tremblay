<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Critic;
use App\Resources\CriticResource;
use App\Repository\CriticRepositoryInterface;

class CriticController extends Controller
{

    private FilmRepositoryInterface $filmRepository;

    public function __construct(CriticRepositoryInterface $CriticRepository){
        $this->criticRepository = $criticRepository;
    }

    public function store(Request $request)
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
