<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicoRequest;
use App\Http\Requests\UpdateMedicoRequest;
use App\Models\Medico;

class MedicoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    public function index()
    {
        return Medico::all();
    }

    public function store(StoreMedicoRequest $request)
    {
        $medico = Medico::create($request->all());

        return response()->json($medico, 201);
    }

    public function show(Medico $medico)
    {
        return $medico;
    }

    public function update(UpdateMedicoRequest $request, Medico $medico)
    {
        $medico->update($request->all());

        return response()->json($medico, 200);
    }

    public function destroy(Medico $medico)
    {
        $medico->delete();

        return response()->json([], 204);
    }
}
