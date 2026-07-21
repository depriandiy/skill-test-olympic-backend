<?php

namespace App\Imports;

use App\Models\Candidate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CandidatesImport implements ToModel, WithHeadingRow
{
    public function model(array $row): ?Candidate
    {
        if (empty($row['email']) || empty($row['full_name'])) {
            return null;
        }

        return Candidate::query()->updateOrCreate(
            ['email' => $row['email']],
            [
                'full_name' => $row['full_name'],
                'phone' => $row['phone'] ?? null,
                'address' => $row['address'] ?? null,
                'birth_date' => $row['birth_date'] ?? null,
                'status' => $row['status'] ?? 'new',
            ],
        );
    }
}
