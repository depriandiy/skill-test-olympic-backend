<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_bearer_token(): void
    {
        $role = Role::factory()->create(['name' => Role::HR_ADMIN]);
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret-password'),
            'role_id' => $role->id,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.email', 'admin@example.com')
            ->assertJsonStructure(['data' => ['access_token']]);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $role = Role::factory()->create(['name' => Role::HR_ADMIN]);
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret-password'),
            'role_id' => $role->id,
        ]);

        $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_view_profile_and_logout(): void
    {
        $role = Role::factory()->create(['name' => Role::HR_STAFF]);
        $user = User::factory()->create(['role_id' => $role->id]);
        $token = $user->createToken('api-token')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.role', Role::HR_STAFF);

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk();

        $this->assertSame(0, PersonalAccessToken::query()->count());
    }
}
