<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCidadeRequest;
use App\Http\Requests\UpdateCidadeRequest;
use App\Models\Cidade;
use Illuminate\Http\Request;

class CidadeController extends Controller
{

    public function __construct(private Cidade $cidade)
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'medicos']);
    }
    public function index()
    {
        return $this->cidade->all();
    }
    public function store(StoreCidadeRequest $request)
    {
        $cidade = $this->cidade->create($request->all());

        return response()->json($cidade, 201);
    }

    public function show(int $id)
    {
        $cidade = $this->cidade->find($id);

        return response()->json($cidade);
    }
    public function update(UpdateCidadeRequest $request, int $id)
    {
        $cidade = $this->cidade->find($id);
        $cidade->update($request->all());
        return response()->json($cidade);
    }
    public function destroy(int $id)
    {
        $cidade = $this->cidade->find($id);
        $cidade->delete();

        return response()->json([], 204);
    }

    public function medicos(int $cidade_id)
    {
        $cidade = $this->cidade->where('id', $cidade_id)->with('medicos')->first();
        return $cidade->medicos;
    }
}
