<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TriageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['required', 'email'],
            'message' => ['required', 'string', 'min:3', 'max:5000'],
            'channel' => ['required', Rule::in(['airbnb', 'booking', 'direct', 'email'])],
        ];
    }
}
