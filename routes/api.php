<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CidadeController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::apiResource('cidades', CidadeController::class);
Route::get('cidades/{cidade}/medicos', [CidadeController::class, 'medicos']);

Route::apiResource('medicos', MedicoController::class);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('medicos/{medico}/pacientes', [MedicoController::class, 'pacientes']);
    Route::post('medicos/{medico}/pacientes', [MedicoController::class, 'addPaciente']);

    Route::apiResource('pacientes', PacienteController::class);

});

