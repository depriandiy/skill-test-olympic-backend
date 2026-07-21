<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CandidateImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_staff_can_export_candidates(): void
    {
        $staff = $this->userWithRole(Role::HR_STAFF);
        Candidate::factory()->create(['email' => 'export@example.com']);

        $this->actingAs($staff, 'sanctum')
            ->getJson('/api/candidates/export')
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    public function test_hr_staff_can_import_candidates_from_csv(): void
    {
        $staff = $this->userWithRole(Role::HR_STAFF);
        $file = UploadedFile::fake()->createWithContent(
            'candidates.csv',
            "full_name,email,phone,address,birth_date,status\nImported Candidate,imported@example.com,08111,Jakarta,1997-01-02,new\n",
        );

        $this->actingAs($staff, 'sanctum')
            ->postJson('/api/candidates/import', ['file' => $file])
            ->assertCreated();

        $this->assertDatabaseHas('candidates', [
            'email' => 'imported@example.com',
            'full_name' => 'Imported Candidate',
        ]);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::factory()->create(['name' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
