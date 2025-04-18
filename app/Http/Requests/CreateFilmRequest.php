<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFilmRequest extends FormRequest
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
            'title' => 'required|string|max:50', 
            'release_year' => 'required|year',
            'length' => 'required|integer',
            'description' => 'required|string',
            'rating' => 'required|string|max:5',
            'language_id' => 'required|exists:languages,id',
            'special_features' => 'required|string|max:200',
            'image' => 'required|string|max:40'
        ];
    }
}
