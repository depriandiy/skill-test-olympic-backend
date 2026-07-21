<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'candidate_id' => ['required', 'exists:candidates,id'],
            'job_id' => ['required', 'exists:jobs,id'],
            'apply_date' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::in(['submitted', 'reviewed', 'interview', 'accepted', 'rejected'])],
        ];
    }
}
