<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetBooksRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'perPage' => 'required|numeric|max:150|min:5',
            'page' => 'required|numeric|min:1',
        ];
    }
}