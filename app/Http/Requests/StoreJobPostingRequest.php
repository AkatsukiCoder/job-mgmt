<?php

namespace App\Http\Requests;

use App\Models\JobPosting;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreJobPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', 'string', 'in:'.implode(',', JobPosting::EMPLOYMENT_TYPES)],
            'description' => ['nullable', 'string'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'gte:salary_min'],
            'currency' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', 'string', 'in:'.implode(',', JobPosting::STATUSES)],
            'posted_at' => ['date', 'date_format:Y-m-d H:i:s', 'after_or_equal:now'],
            'expires_at' => ['date', 'date_format:Y-m-d H:i:s', 'after_or_equal:posted_at'],
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
