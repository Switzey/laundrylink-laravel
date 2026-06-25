<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CleanerServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'cleaner';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'gt:0', 'max:9999999.99'],
            'unit' => ['required', Rule::in(['per_item', 'per_kg', 'flat'])],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function serviceData(): array
    {
        return $this->validated() + [
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : true,
        ];
    }
}
