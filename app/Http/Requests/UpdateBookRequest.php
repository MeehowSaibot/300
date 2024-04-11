<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Book $book */
        $book = Book::query()->where('name', $this->input('book_name'))->first();

        $rules = [
            'book_id' => 'required|numeric|exists:books,id',
            'author_id' => 'required|numeric|exists:authors,id',
            'book_name' => ['required', 'string', 'max:255']
        ];

        if ($book instanceof Book) {
            $rules['book_name'][] = Rule::unique('books', 'name')->ignore($book->name);
        }

        return $rules;
    }
}