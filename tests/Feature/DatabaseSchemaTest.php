<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_hr_tables_and_columns_exist(): void
    {
        $this->assertTrue(Schema::hasColumns('roles', ['id', 'name', 'created_at', 'updated_at']));
        $this->assertTrue(Schema::hasColumns('users', ['id', 'name', 'email', 'password', 'role_id', 'candidate_id', 'created_at', 'updated_at', 'deleted_at']));
        $this->assertTrue(Schema::hasColumns('candidates', ['id', 'full_name', 'email', 'phone', 'address', 'birth_date', 'status', 'photo_path', 'created_at', 'updated_at', 'deleted_at']));
        $this->assertTrue(Schema::hasColumns('jobs', ['id', 'title', 'department', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at']));
        $this->assertTrue(Schema::hasColumns('applications', ['id', 'candidate_id', 'job_id', 'apply_date', 'status', 'created_at', 'updated_at', 'deleted_at']));
    }

    public function test_core_models_have_expected_relationships(): void
    {
        $role = Role::factory()->create(['name' => 'HR Admin']);
        $candidate = Candidate::factory()->create();
        $user = User::factory()->create(['role_id' => $role->id, 'candidate_id' => $candidate->id]);
        $job = Job::factory()->create();
        $application = Application::factory()->create([
            'candidate_id' => $candidate->id,
            'job_id' => $job->id,
        ]);

        $this->assertTrue($user->role->is($role));
        $this->assertTrue($user->candidate->is($candidate));
        $this->assertTrue($candidate->applications->first()->is($application));
        $this->assertTrue($job->applications->first()->is($application));
        $this->assertTrue($application->candidate->is($candidate));
        $this->assertTrue($application->job->is($job));
    }
}
