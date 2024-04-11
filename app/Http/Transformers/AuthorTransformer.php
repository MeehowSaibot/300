<?php

namespace App\Http\Transformers;

use App\Models\Author;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\NullResource;
use League\Fractal\TransformerAbstract;


class AuthorTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        'books'
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [];

    public function transform(Author $author): array
    {
        return [
            'author_id' => $author->id,
            'author' => $author->full_name,
        ];
    }

    public function includeBooks(Author $author): NullResource|Collection
    {
        $books = $author->books;
        return $books->count() > 0 ? $this->collection($books, new IncludeBookTransformer()) : $this->null();
    }
}