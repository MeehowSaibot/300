<?php

namespace App\Http\Repositories;

use App\Models\Author;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuthorRepository
{

    public function getAuthorById(int $id, array $columns = ['*'], array $relations = []): Author
    {
        /** @var Author $author */
        $author = Author::query()->with($relations)->select($columns)->where('id', $id)->firstOrFail();

        return $author;
    }

    public function getPaginatedAuthors(int $perPage, int $page, array $relations = []): LengthAwarePaginator
    {
        return Author::query()->with($relations)->paginate($perPage, ['*'], 'page', $page);
    }
}