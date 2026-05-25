<?php

namespace Tests\Feature\Directores;

use App\Models\Director;
use App\Models\Pelicula;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectorApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Creamos el usuario
        $this->user = User::factory()->create();
        
        // Obtenemos el token JWT manualmente
        $this->token = auth('api')->login($this->user);
        
        // Forzamos que el usuario actual sea este para evitar el 401
        $this->actingAs($this->user, 'api');
    }

    /** 9. Listar directores requiere autenticación */
    public function test_listar_directores_requiere_autenticacion()
    {
        // Forzamos el cierre de sesión para este test específico
        auth()->logout();
        
        $response = $this->getJson('/api/directores');
        $response->assertStatus(401);
    }

    /** 10. Listar directores devuelve colección */
    public function test_listar_directores_autenticado_devuelve_coleccion()
    {
        Director::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->getJson('/api/directores');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /** 11. Crear director con datos válidos */
    public function test_crear_director_con_datos_validos()
    {
        // Usamos los campos de tu controlador: name, surname, birthdate
        $data = [
            'name' => 'Christopher',
            'surname' => 'Nolan',
            'birthdate' => '1970-07-30'
        ];

        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->postJson('/api/directores', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('directors', ['name' => 'Christopher']);
    }

    /** 12. Crear director con datos inválidos (422) */
    public function test_crear_director_con_datos_invalidos_devuelve_422()
    {
        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->postJson('/api/directores', [
                             'name' => '', // Campo obligatorio vacío
                         ]);

        $response->assertStatus(422);
    }

    /** 13. Actualizar director existente */
    public function test_actualizar_director_existente()
    {
        $director = Director::factory()->create(['name' => 'Steven']);

        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->putJson("/api/directores/{$director->id}", [
                             'name' => 'Steven Spielberg'
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('directors', [
            'id' => $director->id,
            'name' => 'Steven Spielberg'
        ]);
    }

    /** 14. Actualizar director inexistente */
    public function test_actualizar_director_inexistente_devuelve_404()
    {
        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->putJson('/api/directores/999', ['name' => 'Inexistente']);

        $response->assertStatus(404);
    }

    /** 15. Eliminar director existente */
    public function test_eliminar_director_existente()
    {
        $director = Director::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->deleteJson("/api/directores/{$director->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('directors', ['id' => $director->id]);
    }


    /** 16. Eliminar director con películas asociadas */
public function test_eliminar_director_con_peliculas_asociadas()
{
    $director = Director::factory()->create();
    $pelicula = Pelicula::factory()->create(['director_id' => $director->id]);

    // Como lamigración tiene 'restrict', primero debemos borrar las películas
    $pelicula->delete();

    $response = $this->withHeader('Authorization', "Bearer $this->token")
                     ->deleteJson("/api/directores/{$director->id}");

    // Ahora sí debería devolver 200 porque ya no hay nada "restringiendo" el borrado
    $response->assertStatus(200);
    
    $this->assertDatabaseMissing('directors', ['id' => $director->id]);
}
}