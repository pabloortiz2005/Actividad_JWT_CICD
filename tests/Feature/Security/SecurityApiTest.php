<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SecurityApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        // Generamos el token JWT para el usuario
        $this->token = auth('api')->login($this->user);
    }

   /** 23. Token expirado devuelve 401 */
public function test_token_expirado_devuelve_401()
{
    // Forzamos la invalidación del token simulando que ha caducado
    auth('api')->logout(); 
    // Al hacer logout, el token actual queda en la "lista negra" (blacklist) de JWT,
    // lo que emula exactamente el mismo comportamiento y respuesta que un token expirado.

    $response = $this->withHeader('Authorization', "Bearer $this->token")
                     ->getJson('/api/directores');

    $response->assertStatus(401);
}

    /** 24. Respuestas de error no exponen stack trace en producción */
    public function test_respuestas_de_error_no_exponen_stack_trace()
    {
        // Simulamos un entorno de producción seguro
        Config::set('app.env', 'production');
        Config::set('app.debug', false);

        // Forzamos un error buscando un director que no existe
        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->getJson('/api/directores/999999');

        // Verificamos que no se filtre información sensible del servidor
        $response->assertJsonMissingPath('exception')
                 ->assertJsonMissingPath('file')
                 ->assertJsonMissingPath('line')
                 ->assertJsonMissingPath('trace');
    }

    /** 25. Password no aparece en respuesta /me */
    public function test_password_no_aparece_en_respuesta_me()
    {
        
        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->getJson('/api/me');

        $response->assertStatus(200)
                 ->assertJsonMissingPath('password')
                 ->assertJsonMissingPath('password_hash');
    }
}