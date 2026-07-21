<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_demo_users_for_every_role_with_realistic_data(): void
    {
        $this->seed();

        foreach ([Role::HR_ADMIN, Role::HR_STAFF, Role::INTERVIEWER, Role::CANDIDATE] as $roleName) {
            $this->assertDatabaseHas('roles', ['name' => $roleName]);
            $this->assertTrue(
                User::query()->whereHas('role', fn ($query) => $query->where('name', $roleName))->exists(),
                "Missing demo user for {$roleName}.",
            );
        }

        $candidateUser = User::query()
            ->where('email', 'candidate.alya@example.com')
            ->firstOrFail();

        $this->assertNotNull($candidateUser->candidate_id);
        $this->assertGreaterThanOrEqual(5, Candidate::query()->count());
        $this->assertGreaterThanOrEqual(4, Job::query()->count());
        $this->assertGreaterThanOrEqual(4, Application::query()->count());
    }
}
