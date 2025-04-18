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
