<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'regex:/^[a-zA-Z\s]+$/'],
            'phone' => ['required', 'string', 'digits:10', 'regex:/^[6-9]\d{9}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'inquiry_type' => ['nullable', 'string'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.regex' => 'Full Name must contain letters and spaces only.',
            'phone.required' => 'Please enter your 10-digit mobile number.',
            'phone.regex' => 'Mobile number must be a valid 10-digit number starting with 6, 7, 8, or 9.',
            'email.email' => 'Please enter a valid email address.',
        ];
    }
}
