<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Critic;
use App\Http\Resources\CriticResource;
use App\Repository\CriticRepositoryInterface;

class CriticController extends Controller
{
    private CriticRepositoryInterface $criticRepository;

    public function __construct(CriticRepositoryInterface $criticRepository){
        $this->criticRepository = $criticRepository;
    }

    public function store(Request $request)
    {
        try{
            $critic = $this->criticRepository->create($request->all());
            return (new CriticResource($critic))->response()->setStatusCode(CREATED);
        }
        catch (Exception $ex){
            abort(SERVER_ERROR, $ex->getMessage());
        }
    }
}
