<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Job;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_totals_and_candidate_status_breakdown(): void
    {
        $admin = $this->userWithRole(Role::HR_ADMIN);
        Candidate::factory()->count(2)->create(['status' => 'screening']);
        Candidate::factory()->create(['status' => 'accepted']);
        Job::factory()->count(2)->create();

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonPath('data.total_candidates', 3)
            ->assertJsonPath('data.total_jobs', 2)
            ->assertJsonPath('data.candidates_per_status.screening', 2)
            ->assertJsonPath('data.candidates_per_status.accepted', 1);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::factory()->create(['name' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
