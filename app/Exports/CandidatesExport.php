<?php

namespace App\Exports;

use App\Models\Candidate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CandidatesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Candidate::query()
            ->select(['full_name', 'email', 'phone', 'address', 'birth_date', 'status'])
            ->orderBy('full_name')
            ->get();
    }

    public function headings(): array
    {
        return ['full_name', 'email', 'phone', 'address', 'birth_date', 'status'];
    }
}
