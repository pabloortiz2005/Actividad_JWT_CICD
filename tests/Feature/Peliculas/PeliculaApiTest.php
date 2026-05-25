<?php

namespace Tests\Feature\Peliculas;

use App\Models\Director;
use App\Models\Pelicula;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeliculaApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = auth('api')->login($this->user);
        $this->actingAs($this->user, 'api');
    }

    /** 17. Listar películas devuelve colección */
    public function test_listar_peliculas_autenticado_devuelve_coleccion()
    {
        Pelicula::factory()->count(3)->create();

        $this->withHeader('Authorization', "Bearer $this->token")
             ->getJson('/api/peliculas')
             ->assertStatus(200)
             ->assertJsonCount(3);
    }

    /** 18. Crear película asociada a director existente */
    public function test_crear_pelicula_asociada_a_director_existente()
    {
        $director = Director::factory()->create();
        
        $data = [
            'title' => 'Inception',
            'release_date' => '2010-07-16',
            'sinopsis' => 'Un ladrón que roba secretos a través del subconsciente.',
            'duration' => 148,
            'gendre' => 'Sci-Fi',
            'director_id' => $director->id
        ];

        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->postJson('/api/peliculas', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('peliculas', ['title' => 'Inception', 'director_id' => $director->id]);
    }

    /** 19. Crear película con director inexistente (422) */
    public function test_crear_pelicula_con_director_inexistente_devuelve_422()
    {
        $data = [
            'title' => 'Test',
            'release_date' => '2024-01-01',
            'sinopsis' => 'Test',
            'duration' => 100,
            'gendre' => 'Drama',
            'director_id' => 9999 // ID que no existe
        ];

        $this->withHeader('Authorization', "Bearer $this->token")
             ->postJson('/api/peliculas', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['director_id']);
    }

    /** 20. Actualizar película */
    public function test_actualizar_pelicula()
    {
        $pelicula = Pelicula::factory()->create(['title' => 'Titulo Original']);

        $this->withHeader('Authorization', "Bearer $this->token")
             ->putJson("/api/peliculas/{$pelicula->id}", ['title' => 'Titulo Editado'])
             ->assertStatus(200);

        $this->assertDatabaseHas('peliculas', ['id' => $pelicula->id, 'title' => 'Titulo Editado']);
    }

    /** 21. Eliminar película */
    public function test_eliminar_pelicula()
    {
        $pelicula = Pelicula::factory()->create();

        $this->withHeader('Authorization', "Bearer $this->token")
             ->deleteJson("/api/peliculas/{$pelicula->id}")
             ->assertStatus(200);

        $this->assertDatabaseMissing('peliculas', ['id' => $pelicula->id]);
    }

    /** 22. Mostrar película incluye datos del director */
    public function test_mostrar_pelicula_incluye_datos_del_director()
    {
        $director = Director::factory()->create(['name' => 'Pablo']);
        $pelicula = Pelicula::factory()->create(['director_id' => $director->id]);

        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->getJson("/api/peliculas/{$pelicula->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id', 'title', 'director' => ['id', 'name']
                 ])
                 ->assertJsonPath('director.name', 'Pablo');
    }
}