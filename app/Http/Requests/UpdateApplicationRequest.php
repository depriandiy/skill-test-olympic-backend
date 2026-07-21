<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'apply_date' => ['sometimes', 'date'],
            'status' => ['sometimes', 'required', Rule::in(['submitted', 'reviewed', 'interview', 'accepted', 'rejected'])],
        ];
    }
}
