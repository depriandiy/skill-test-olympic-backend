<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_staff_can_crud_jobs_and_interviewer_cannot_access_jobs(): void
    {
        $staff = $this->userWithRole(Role::HR_STAFF);
        $interviewer = $this->userWithRole(Role::INTERVIEWER);
        Job::factory()->create(['title' => 'Backend Developer', 'department' => 'Technology']);

        $this->actingAs($interviewer, 'sanctum')
            ->getJson('/api/jobs?search=backend')
            ->assertForbidden();

        $this->actingAs($interviewer, 'sanctum')
            ->postJson('/api/jobs', ['title' => 'QA Engineer', 'department' => 'Technology'])
            ->assertForbidden();

        $create = $this->actingAs($staff, 'sanctum')->postJson('/api/jobs', [
            'title' => 'Frontend Developer',
            'department' => 'Technology',
            'description' => 'Build user interfaces.',
            'is_active' => true,
        ]);

        $create->assertCreated()->assertJsonPath('data.title', 'Frontend Developer');
        $jobId = $create->json('data.id');

        $this->actingAs($staff, 'sanctum')
            ->putJson("/api/jobs/{$jobId}", ['is_active' => false])
            ->assertOk()
            ->assertJsonPath('data.is_active', false);

        $this->actingAs($staff, 'sanctum')
            ->deleteJson("/api/jobs/{$jobId}")
            ->assertOk();

        $this->assertSoftDeleted('jobs', ['id' => $jobId]);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::factory()->create(['name' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
