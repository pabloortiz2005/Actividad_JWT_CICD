<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Director;
use Illuminate\Http\JsonResponse;

class DirectorController extends Controller
{
    public function index(): JsonResponse
    {
        $directores = Director::all();
        return response()->json($directores, 200);
    }

    public function store(Request $request): JsonResponse
    {
        //Validación y almacenamiento
       // En DirectorController.php
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'surname'   => 'required|string|max:255',
            'birthdate' => 'required|date',
        ]);

        $director = Director::create($validated);
        return response()->json($director, 201);
    }

    public function show(string $id): JsonResponse
    {
        //Carga la relación peliculas definida en el modelo
        $director = Director::with('peliculas')->find($id);

        if (!$director) {
            return response()->json(['message' => 'Director no encontrado'], 404);
        }

        return response()->json($director, 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $director = Director::find($id);

        if (!$director) {
            return response()->json(['message' => 'Director no encontrado'], 404);
        }

        $director->update($request->all());
        return response()->json($director, 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $director = Director::find($id);

        if (!$director) {
            return response()->json(['message' => 'Director no encontrado'], 404);
        }

        $director->delete();
        return response()->json(['message' => 'Director eliminado correctamente'], 200);
    }
}