<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase; 

    protected function setUp(): void
    {
        parent::setUp();
        // Creamos un usuario base para todas las pruebas
        $this->user = User::factory()->create([
            'email' => 'test@ejemplo.com',
            'password' => bcrypt('admin123'),
        ]);
    }

    /** 1. Login válido devuelve token */
    public function test_login_con_credenciales_validas_devuelve_token()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@ejemplo.com',
            'password' => 'admin123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    /** 2. Login inválido devuelve 401 */
    public function test_login_con_credenciales_invalidas_devuelve_401()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@ejemplo.com',
            'password' => 'password_falsa',
        ]);

        $response->assertStatus(401)
        ->assertJson(['error' => 'Credenciales inválidas']);    }

    /** 3. Campos faltantes devuelve 422 */
    public function test_login_con_campos_faltantes_devuelve_422()
    {
        $response = $this->postJson('/api/login', ['email' => 'test@ejemplo.com']); // Falta password

        $response->assertStatus(422);
    }

    /** 4. Logout invalida el token */
    public function test_logout_invalida_el_token()
    {
        $login = $this->postJson('/api/login', ['email' => 'test@ejemplo.com', 'password' => 'admin123']);
        $token = $login['access_token'];

        $this->withHeader('Authorization', "Bearer $token")->postJson('/api/logout')->assertStatus(200);
        
        // El token ya no debe servir
        $this->withHeader('Authorization', "Bearer $token")->getJson('/api/directores')->assertStatus(401);
    }

    /** 5. Refresh devuelve nuevo token */
    public function test_refresh_devuelve_nuevo_token_valido()
    {
        $login = $this->postJson('/api/login', ['email' => 'test@ejemplo.com', 'password' => 'admin123']);
        $token = $login['access_token'];

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/refresh');
        $response->assertStatus(200)->assertJsonStructure(['access_token']);
    }

    /** 6. Me devuelve datos del usuario */
    public function test_me_devuelve_datos_del_usuario_autenticado()
    {
        $login = $this->postJson('/api/login', ['email' => 'test@ejemplo.com', 'password' => 'admin123']);
        $token = $login['access_token'];

        $response = $this->withHeader('Authorization', "Bearer $token")->getJson('/api/me');
        $response->assertStatus(200)
                 ->assertJsonFragment(['email' => 'test@ejemplo.com'])
                 ->assertJsonMissing(['password']); // Seguridad: no debe enviar la contraseña
    }

    /** 7. Acceso sin token devuelve 401 */
    public function test_acceso_sin_token_devuelve_401()
    {
        $this->getJson('/api/directores')->assertStatus(401);
    }

    /** 8. Token malformado devuelve 401 */
    public function test_acceso_con_token_malformado_devuelve_401()
    {
        $this->withHeader('Authorization', 'Bearer token_falso_123')
             ->getJson('/api/directores')
             ->assertStatus(401);
    }
}