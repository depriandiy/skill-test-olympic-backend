<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CandidateApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_staff_can_manage_candidates_with_search_filter_sort_and_photo_upload(): void
    {
        Storage::fake('public');
        $user = $this->userWithRole(Role::HR_STAFF);
        Candidate::factory()->create(['full_name' => 'Budi Screening', 'status' => 'screening']);
        Candidate::factory()->create(['full_name' => 'Citra Accepted', 'status' => 'accepted']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/candidates?search=budi&status=screening&sort=full_name&direction=asc')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.full_name', 'Budi Screening');

        $create = $this->actingAs($user, 'sanctum')->postJson('/api/candidates', [
            'full_name' => 'Dina Candidate',
            'email' => 'dina@example.com',
            'phone' => '08123456789',
            'address' => 'Jakarta',
            'birth_date' => '1998-01-02',
            'status' => 'new',
            'photo' => UploadedFile::fake()->image('dina.jpg'),
        ]);

        $create->assertCreated()->assertJsonPath('data.email', 'dina@example.com');
        Storage::disk('public')->assertExists($create->json('data.photo_path'));

        $candidateId = $create->json('data.id');
        $this->actingAs($user, 'sanctum')
            ->putJson("/api/candidates/{$candidateId}", ['status' => 'interview'])
            ->assertOk()
            ->assertJsonPath('data.status', 'interview');

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/candidates/{$candidateId}")
            ->assertOk();

        $this->assertSoftDeleted('candidates', ['id' => $candidateId]);
    }

    public function test_candidate_can_only_view_own_profile(): void
    {
        $ownCandidate = Candidate::factory()->create();
        $otherCandidate = Candidate::factory()->create();
        $user = $this->userWithRole(Role::CANDIDATE, ['candidate_id' => $ownCandidate->id]);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/candidates/{$ownCandidate->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $ownCandidate->id);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/candidates/{$otherCandidate->id}")
            ->assertForbidden();
    }

    private function userWithRole(string $roleName, array $attributes = []): User
    {
        $role = Role::factory()->create(['name' => $roleName]);

        return User::factory()->create(array_merge(['role_id' => $role->id], $attributes));
    }
}
