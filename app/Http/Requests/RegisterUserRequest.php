<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => 'required|unique:users,login|string|max:50',
            'password' => 'required|confirmed|string|max:255',
            'email' => 'required|email|unique:users,email|string|max:50',
            'last_name' => 'required|string|max:50',
            'first_name' => 'required|string|max:50'
        ];
    }
}
