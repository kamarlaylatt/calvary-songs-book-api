<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HymnRequest extends FormRequest
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
            'no' => 'required|integer|min:1',
            'hymn_category_id' => 'required|exists:hymn_categories,id',
            'song_id' => 'nullable|exists:songs,id',
            'reference_id' => 'nullable|integer|min:1',
            'english_title' => 'nullable|string|max:255',
        ];
    }
}
