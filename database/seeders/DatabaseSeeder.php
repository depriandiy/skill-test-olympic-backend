<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $roles = Role::query()->pluck('id', 'name');

        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Aditya Rahman',
                'password' => 'password',
                'role_id' => $roles[Role::HR_ADMIN],
                'candidate_id' => null,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'staff.nadia@example.com'],
            [
                'name' => 'Nadia Putri',
                'password' => 'password',
                'role_id' => $roles[Role::HR_STAFF],
                'candidate_id' => null,
            ]
        );
        User::query()->updateOrCreate(
            ['email' => 'interviewer.raka@example.com'],
            [
                'name' => 'Raka Pratama',
                'password' => 'password',
                'role_id' => $roles[Role::INTERVIEWER],
                'candidate_id' => null,
            ]
        );

        $candidateRows = [
            ['full_name' => 'Alya Maharani', 'email' => 'alya.maharani@example.com', 'phone' => '0812-4401-2233', 'address' => 'Jl. Sukajadi No. 18, Bandung', 'birth_date' => '1998-03-12', 'status' => 'new'],
            ['full_name' => 'Bima Santoso', 'email' => 'bima.santoso@example.com', 'phone' => '0813-6502-1144', 'address' => 'Jl. Diponegoro No. 9, Semarang', 'birth_date' => '1996-07-22', 'status' => 'screening'],
            ['full_name' => 'Citra Lestari', 'email' => 'citra.lestari@example.com', 'phone' => '0821-7700-4321', 'address' => 'Jl. Melati Raya No. 5, Jakarta Selatan', 'birth_date' => '1999-11-04', 'status' => 'interview'],
            ['full_name' => 'Dimas Arya', 'email' => 'dimas.arya@example.com', 'phone' => '0857-1209-8821', 'address' => 'Jl. Kaliurang KM 6, Sleman', 'birth_date' => '1995-01-18', 'status' => 'accepted'],
            ['full_name' => 'Eka Wulandari', 'email' => 'eka.wulandari@example.com', 'phone' => '0819-2220-3344', 'address' => 'Jl. Veteran No. 42, Surabaya', 'birth_date' => '1997-09-29', 'status' => 'rejected'],
            ['full_name' => 'Farhan Nugroho', 'email' => 'farhan.nugroho@example.com', 'phone' => '0822-8831-0098', 'address' => 'Jl. Ahmad Yani No. 21, Bekasi', 'birth_date' => '1994-05-08', 'status' => 'screening'],
            ['full_name' => 'Gita Permata', 'email' => 'gita.permata@example.com', 'phone' => '0812-9987-1120', 'address' => 'Jl. Setiabudi No. 31, Medan', 'birth_date' => '2000-02-14', 'status' => 'new'],
            ['full_name' => 'Hendra Wijaya', 'email' => 'hendra.wijaya@example.com', 'phone' => '0815-6633-8811', 'address' => 'Jl. Teuku Umar No. 11, Denpasar', 'birth_date' => '1993-12-01', 'status' => 'interview'],
            ['full_name' => 'Indah Kartika', 'email' => 'indah.kartika@example.com', 'phone' => '0878-2411-5577', 'address' => 'Jl. Pemuda No. 76, Malang', 'birth_date' => '1998-06-17', 'status' => 'accepted'],
            ['full_name' => 'Joko Prasetyo', 'email' => 'joko.prasetyo@example.com', 'phone' => '0817-3422-7654', 'address' => 'Jl. Sudirman No. 14, Tangerang', 'birth_date' => '1992-10-25', 'status' => 'screening'],
            ['full_name' => 'Karina Safitri', 'email' => 'karina.safitri@example.com', 'phone' => '0821-1190-5522', 'address' => 'Jl. Cendrawasih No. 8, Makassar', 'birth_date' => '1997-04-03', 'status' => 'new'],
            ['full_name' => 'Lukman Hakim', 'email' => 'lukman.hakim@example.com', 'phone' => '0852-7410-1235', 'address' => 'Jl. Gatot Subroto No. 51, Solo', 'birth_date' => '1991-08-19', 'status' => 'rejected'],
            ['full_name' => 'Maya Saraswati', 'email' => 'maya.saraswati@example.com', 'phone' => '0813-4566-7788', 'address' => 'Jl. Mawar No. 3, Bogor', 'birth_date' => '1999-05-27', 'status' => 'interview'],
            ['full_name' => 'Naufal Ramadhan', 'email' => 'naufal.ramadhan@example.com', 'phone' => '0823-1002-3004', 'address' => 'Jl. Pahlawan No. 17, Balikpapan', 'birth_date' => '1996-02-09', 'status' => 'new'],
            ['full_name' => 'Olivia Anjani', 'email' => 'olivia.anjani@example.com', 'phone' => '0812-9090-3030', 'address' => 'Jl. Asia Afrika No. 26, Bandung', 'birth_date' => '1998-12-15', 'status' => 'screening'],
            ['full_name' => 'Putra Mahendra', 'email' => 'putra.mahendra@example.com', 'phone' => '0811-2300-7821', 'address' => 'Jl. Imam Bonjol No. 19, Palembang', 'birth_date' => '1994-09-10', 'status' => 'accepted'],
            ['full_name' => 'Rani Aprilia', 'email' => 'rani.aprilia@example.com', 'phone' => '0856-1112-9010', 'address' => 'Jl. Merdeka No. 6, Pontianak', 'birth_date' => '2001-01-30', 'status' => 'new'],
            ['full_name' => 'Satria Adinata', 'email' => 'satria.adinata@example.com', 'phone' => '0818-7856-4432', 'address' => 'Jl. Pandanaran No. 22, Semarang', 'birth_date' => '1995-11-21', 'status' => 'interview'],
        ];

        $candidates = collect($candidateRows)->mapWithKeys(function (array $row): array {
            $candidate = Candidate::query()->updateOrCreate(['email' => $row['email']], $row);

            return [$row['email'] => $candidate];
        });

        User::query()->updateOrCreate(
            ['email' => 'candidate.alya@example.com'],
            [
                'name' => 'Alya Maharani',
                'password' => 'password',
                'role_id' => $roles[Role::CANDIDATE],
                'candidate_id' => $candidates['alya.maharani@example.com']->id,
            ]
        );

        $jobRows = [
            ['title' => 'Frontend Engineer', 'department' => 'Technology', 'description' => 'Build responsive HRIS and recruitment interfaces with strong attention to usability.', 'is_active' => true],
            ['title' => 'Backend Engineer', 'department' => 'Technology', 'description' => 'Develop secure Laravel APIs, integrations, and database-backed services.', 'is_active' => true],
            ['title' => 'HR Operations Specialist', 'department' => 'Human Resources', 'description' => 'Manage employee data operations, onboarding documents, and HR workflow quality.', 'is_active' => true],
            ['title' => 'Recruitment Officer', 'department' => 'Recruitment', 'description' => 'Own candidate sourcing, screening, interview scheduling, and hiring coordination.', 'is_active' => true],
            ['title' => 'QA Automation Engineer', 'department' => 'Technology', 'description' => 'Create automated test coverage for web applications and REST APIs.', 'is_active' => true],
            ['title' => 'Finance Analyst', 'department' => 'Finance', 'description' => 'Prepare budget analysis, reporting, and operational cost review.', 'is_active' => false],
            ['title' => 'Product Designer', 'department' => 'Product', 'description' => 'Design clean product experiences for internal HR and recruitment tools.', 'is_active' => true],
            ['title' => 'Implementation Consultant', 'department' => 'Operations', 'description' => 'Support client rollout, data migration, training, and production readiness.', 'is_active' => true],
        ];

        $jobs = collect($jobRows)->mapWithKeys(function (array $row): array {
            $job = Job::query()->updateOrCreate(['title' => $row['title']], $row);

            return [$row['title'] => $job];
        });

        $applicationRows = [
            ['candidate' => 'alya.maharani@example.com', 'job' => 'Frontend Engineer', 'apply_date' => '2026-07-01', 'status' => 'submitted'],
            ['candidate' => 'bima.santoso@example.com', 'job' => 'Backend Engineer', 'apply_date' => '2026-07-02', 'status' => 'reviewed'],
            ['candidate' => 'citra.lestari@example.com', 'job' => 'Product Designer', 'apply_date' => '2026-07-03', 'status' => 'interview'],
            ['candidate' => 'dimas.arya@example.com', 'job' => 'QA Automation Engineer', 'apply_date' => '2026-07-04', 'status' => 'accepted'],
            ['candidate' => 'eka.wulandari@example.com', 'job' => 'Finance Analyst', 'apply_date' => '2026-07-05', 'status' => 'rejected'],
            ['candidate' => 'farhan.nugroho@example.com', 'job' => 'Backend Engineer', 'apply_date' => '2026-07-06', 'status' => 'reviewed'],
            ['candidate' => 'gita.permata@example.com', 'job' => 'Recruitment Officer', 'apply_date' => '2026-07-07', 'status' => 'submitted'],
            ['candidate' => 'hendra.wijaya@example.com', 'job' => 'Implementation Consultant', 'apply_date' => '2026-07-08', 'status' => 'interview'],
            ['candidate' => 'indah.kartika@example.com', 'job' => 'HR Operations Specialist', 'apply_date' => '2026-07-09', 'status' => 'accepted'],
            ['candidate' => 'joko.prasetyo@example.com', 'job' => 'Backend Engineer', 'apply_date' => '2026-07-10', 'status' => 'reviewed'],
            ['candidate' => 'karina.safitri@example.com', 'job' => 'Frontend Engineer', 'apply_date' => '2026-07-11', 'status' => 'submitted'],
            ['candidate' => 'lukman.hakim@example.com', 'job' => 'QA Automation Engineer', 'apply_date' => '2026-07-12', 'status' => 'rejected'],
            ['candidate' => 'maya.saraswati@example.com', 'job' => 'Product Designer', 'apply_date' => '2026-07-13', 'status' => 'interview'],
            ['candidate' => 'naufal.ramadhan@example.com', 'job' => 'Implementation Consultant', 'apply_date' => '2026-07-14', 'status' => 'submitted'],
            ['candidate' => 'olivia.anjani@example.com', 'job' => 'Recruitment Officer', 'apply_date' => '2026-07-15', 'status' => 'reviewed'],
            ['candidate' => 'putra.mahendra@example.com', 'job' => 'HR Operations Specialist', 'apply_date' => '2026-07-16', 'status' => 'accepted'],
            ['candidate' => 'rani.aprilia@example.com', 'job' => 'Frontend Engineer', 'apply_date' => '2026-07-17', 'status' => 'submitted'],
            ['candidate' => 'satria.adinata@example.com', 'job' => 'Backend Engineer', 'apply_date' => '2026-07-18', 'status' => 'interview'],
            ['candidate' => 'alya.maharani@example.com', 'job' => 'Product Designer', 'apply_date' => '2026-07-19', 'status' => 'reviewed'],
            ['candidate' => 'citra.lestari@example.com', 'job' => 'Frontend Engineer', 'apply_date' => '2026-07-20', 'status' => 'interview'],
        ];

        foreach ($applicationRows as $row) {
            Application::query()->updateOrCreate(
                [
                    'candidate_id' => $candidates[$row['candidate']]->id,
                    'job_id' => $jobs[$row['job']]->id,
                ],
                [
                    'apply_date' => $row['apply_date'],
                    'status' => $row['status'],
                ]
            );
        }
    }
}
