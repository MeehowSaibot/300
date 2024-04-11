<?php

namespace App\Http\Transformers;

use App\Models\Author;
use League\Fractal\TransformerAbstract;


class IncludeAuthorTransformer extends TransformerAbstract
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

    public function transform(Author $author): array
    {
        return [
            'author_id' => $author->id,
            'author' => $author->full_name,
        ];
    }
}