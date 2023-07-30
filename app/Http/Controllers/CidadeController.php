<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCidadeRequest;
use App\Http\Requests\UpdateCidadeRequest;
use App\Models\Cidade;

class CidadeController extends Controller
{

    public function __construct(private Cidade $cidade)
    {

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
    public function update(UpdateCidadeRequest $request, $id)
    {
        //
    }
    public function destroy($id)
    {
        //
    }
}
