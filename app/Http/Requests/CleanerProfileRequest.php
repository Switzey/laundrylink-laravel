<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CleanerProfileRequest extends FormRequest
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
            'business_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'turnaround_time' => ['nullable', 'string', 'max:120'],
            'opening_hours' => ['nullable', 'string', 'max:160'],
            'is_available' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function profileData(): array
    {
        return $this->validated() + [
            'is_available' => $this->has('is_available') ? $this->boolean('is_available') : true,
        ];
    }
}
