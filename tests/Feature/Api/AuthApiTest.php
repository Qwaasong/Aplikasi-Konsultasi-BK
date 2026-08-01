<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\OAuthClientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OAuthClientSeeder::class); // setiap DB test fresh butuh client
    }

    private function tokenFor(string $login, string $password): string
    {
        $response = $this->post('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => config('oauth.client_id'),
            'client_secret' => config('oauth.client_secret'),
            'username' => $login,
            'password' => $password,
            'scope' => '',
        ]);
        $response->assertOk();

        return $response->json('access_token');
    }

    public function test_register_creates_user(): void
    {
        $this->postJson('/api/v1/register', [
            'nama' => 'Siswa Baru',
            'username' => 'siswa_baru',
            'email' => 'siswa@sekolah.sch.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'jenis_kelamin' => 'P',
            'no_hp' => '081234567890',
        ])->assertStatus(201)->assertJson(['status' => true]);

        $this->assertDatabaseHas('users', ['username' => 'siswa_baru', 'role' => 'siswa']);
    }

    public function test_login_via_oauth_password_grant_by_username(): void
    {
        User::factory()->create(['username' => 'siswa1']);

        $this->post('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => config('oauth.client_id'),
            'client_secret' => config('oauth.client_secret'),
            'username' => 'siswa1',
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonStructure(['token_type', 'expires_in', 'access_token', 'refresh_token']);
    }

    public function test_login_via_oauth_password_grant_by_email(): void
    {
        $user = User::factory()->create();

        $this->post('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => config('oauth.client_id'),
            'client_secret' => config('oauth.client_secret'),
            'username' => $user->email,
            'password' => 'password123',
        ])->assertOk()->assertJsonStructure(['access_token', 'refresh_token']);
    }

    public function test_bad_credentials_rejected(): void
    {
        $this->post('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => config('oauth.client_id'),
            'client_secret' => config('oauth.client_secret'),
            'username' => 'nobody',
            'password' => 'wrong',
        ])->assertStatus(400); // League mengembalikan invalid_grant
    }

    public function test_protected_route_returns_401_without_token(): void
    {
        $this->getJson('/api/v1/profile')->assertStatus(401);
    }

    public function test_protected_route_returns_200_with_token(): void
    {
        User::factory()->create(['username' => 'siswa2']);
        $token = $this->tokenFor('siswa2', 'password123');

        $this->getJson('/api/v1/profile', ['Authorization' => 'Bearer '.$token])
            ->assertOk()
            ->assertJsonPath('data.username', 'siswa2');
    }

    public function test_login_endpoint_proxy_returns_token(): void
    {
        User::factory()->create(['username' => 'siswa3']);

        $this->postJson('/api/v1/login', ['username' => 'siswa3', 'password' => 'password123'])
            ->assertOk()
            ->assertJsonStructure(['token' => ['access_token', 'refresh_token']]);
    }
}
