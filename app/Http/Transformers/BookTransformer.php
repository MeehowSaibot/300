<?php

namespace App\Http\Transformers;

use App\Models\Author;
use App\Models\Book;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use League\Fractal\TransformerAbstract;

class BookTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        'author'
    ];

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

    public function includeAuthor(Book $book): NullResource|Item
    {
        $author = $book->author;
        return true === ($author instanceof Author) ? $this->item($author,
            new IncludeAuthorTransformer()) : $this->null();
    }
}