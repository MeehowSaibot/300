<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBookRequest;
use App\Http\Requests\DeleteBookRequest;
use App\Http\Requests\GetBooksRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Services\BookService;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{

    public function __construct(
        private readonly BookService $bookService,
    ) {
    }

    public function getBooks(GetBooksRequest $request): JsonResponse
    {
        return $this->bookService->getBooks($request);
    }

    public function getBook(int $id): JsonResponse
    {
        return $this->bookService->getBook($id);
    }

    public function addBook(AddBookRequest $request): JsonResponse
    {
        return $this->bookService->addBook($request);
    }

    public function updateBook(UpdateBookRequest $request): JsonResponse
    {
        return $this->bookService->updateBook($request);
    }

    public function deleteBook(int $id): JsonResponse
    {
        return $this->bookService->deleteBook($id);
    }
}
