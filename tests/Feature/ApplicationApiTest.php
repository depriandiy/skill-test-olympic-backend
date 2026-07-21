<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_create_own_application_and_admin_can_update_status(): void
    {
        $candidate = Candidate::factory()->create();
        $candidateUser = $this->userWithRole(Role::CANDIDATE, ['candidate_id' => $candidate->id]);
        $admin = $this->userWithRole(Role::HR_ADMIN);
        $job = Job::factory()->create();

        $create = $this->actingAs($candidateUser, 'sanctum')->postJson('/api/applications', [
            'candidate_id' => $candidate->id,
            'job_id' => $job->id,
            'apply_date' => '2026-07-21',
        ]);

        $create->assertCreated()
            ->assertJsonPath('data.candidate.id', $candidate->id)
            ->assertJsonPath('data.job.id', $job->id)
            ->assertJsonPath('data.status', 'submitted');
        $this->assertDatabaseHas('candidates', [
            'id' => $candidate->id,
            'status' => 'screening',
        ]);

        $applicationId = $create->json('data.id');
        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/applications/{$applicationId}", ['status' => 'reviewed'])
            ->assertOk()
            ->assertJsonPath('data.status', 'reviewed');
        $this->assertDatabaseHas('candidates', [
            'id' => $candidate->id,
            'status' => 'screening',
        ]);

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/applications/{$applicationId}", ['status' => 'interview'])
            ->assertOk()
            ->assertJsonPath('data.status', 'interview');
        $this->assertDatabaseHas('candidates', [
            'id' => $candidate->id,
            'status' => 'interview',
        ]);
    }

    public function test_application_index_supports_pdf_singular_alias_for_admin(): void
    {
        $admin = $this->userWithRole(Role::HR_ADMIN);
        Application::factory()->create();

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/application')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_hr_staff_and_interviewer_cannot_access_applications(): void
    {
        $staff = $this->userWithRole(Role::HR_STAFF);
        $interviewer = $this->userWithRole(Role::INTERVIEWER);

        $this->actingAs($staff, 'sanctum')
            ->getJson('/api/applications')
            ->assertForbidden();

        $this->actingAs($interviewer, 'sanctum')
            ->getJson('/api/applications')
            ->assertForbidden();
    }

    private function userWithRole(string $roleName, array $attributes = []): User
    {
        $role = Role::factory()->create(['name' => $roleName]);

        return User::factory()->create(array_merge(['role_id' => $role->id], $attributes));
    }
}
