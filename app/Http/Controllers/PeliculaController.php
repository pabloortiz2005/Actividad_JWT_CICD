<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PeliculaController extends Controller
{
    /**
     * Listado de películas con su director
     */
    public function index(): JsonResponse
    {
        return response()->json(Pelicula::with('director')->get(), 200);
    }

    /**
     * Crear nueva película
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'release_date' => 'required|date',
            'sinopsis' => 'required|string',
            'duration' => 'required|integer',
            'gendre' => 'required|string',
            'director_id' => 'required|exists:directors,id',
        ]);

        $pelicula = Pelicula::create($validated);
        return response()->json($pelicula, 201);
    }

    /**
     * Mostrar una película específica
     */
    public function show($id): JsonResponse
    {
        $pelicula = Pelicula::with('director')->find($id);
        
        if (!$pelicula) {
            return response()->json(['error' => 'Película no encontrada'], 404);
        }

        return response()->json($pelicula, 200);
    }

    /**
     * Actualizar película
     */
    public function update(Request $request, $id): JsonResponse
    {
        $pelicula = Pelicula::find($id);

        if (!$pelicula) {
            return response()->json(['error' => 'Película no encontrada'], 404);
        }

        $pelicula->update($request->all());
        return response()->json($pelicula, 200);
    }

    /**
     * Eliminar película
     */
    public function destroy($id): JsonResponse
    {
        $pelicula = Pelicula::find($id);

        if (!$pelicula) {
            return response()->json(['error' => 'Película no encontrada'], 404);
        }

        $pelicula->delete();
        return response()->json(['message' => 'Eliminada correctamente'], 200);
    }
}