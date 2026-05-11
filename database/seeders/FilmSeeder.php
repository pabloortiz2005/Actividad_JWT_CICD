<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelicula;

class FilmSeeder extends Seeder
{
    public function run(): void
    {
        Pelicula::create([
            'title' => 'Inception',
            'release_date' => '2010-07-16',
            'sinopsis' => 'Sueños dentro de sueños',
            'duration' => 148,
            'gendre' => 'Ciencia ficción',
            'director_id' => 3
        ]);

        Pelicula::create([
            'title' => 'Pulp Fiction',
            'release_date' => '1994-10-14',
            'sinopsis' => 'Historias criminales cruzadas',
            'duration' => 154,
            'gendre' => 'Crimen',
            'director_id' => 6
        ]);
    }
}