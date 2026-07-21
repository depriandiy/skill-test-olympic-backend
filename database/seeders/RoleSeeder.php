<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([Role::HR_ADMIN, Role::HR_STAFF, Role::INTERVIEWER, Role::CANDIDATE] as $role) {
            Role::query()->firstOrCreate(['name' => $role]);
        }
    }
}
