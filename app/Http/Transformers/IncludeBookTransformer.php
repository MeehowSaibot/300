<?php

namespace App\Http\Transformers;

use App\Models\Book;
use League\Fractal\TransformerAbstract;

class IncludeBookTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [];

    /**
     * @param  Book  $book
     * @return array
     */
    public function transform(Book $book): array
    {
        return [
            'book_id' => $book->id,
            'book_name' => $book->name
        ];
    }
}