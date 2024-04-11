<?php

namespace App\Http\Repositories;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class BookRepository
{
    public function getBookById(int $id, array $columns = ['*'], array $relations = []): Book
    {
        /** @var Book $book */
        $book = Book::query()->with($relations)->select($columns)->where('id', $id)->firstOrFail();

        return $book;
    }

    public function getPaginatedBooks(int $perPage, int $page, array $relations = []): LengthAwarePaginator
    {
        return Book::query()->with($relations)->paginate($perPage, ['*'], 'page', $page);
    }
}