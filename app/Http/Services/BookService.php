<?php

namespace App\Http\Services;

use App\Http\Repositories\AuthorRepository;
use App\Http\Repositories\BookRepository;
use App\Http\Requests\AddBookRequest;
use App\Http\Requests\GetBooksRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Transformers\BookTransformer;
use App\Jobs\UpdateAuthorsLastBookJob;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


class BookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    public function getBook(int $id): JsonResponse
    {
        $book = $this->bookRepository->getBookById($id, [
            'id',
            'name',
            'author_id',
        ],
            [
                'author'
            ]);

        return fractal($book, new BookTransformer())->respond(Response::HTTP_OK);
    }

    public function getBooks(GetBooksRequest $request): JsonResponse
    {
        $data = $request->validated();
        $perPage = $data['perPage'] ?: 10;
        $page = $data['page'] ?: 1;

        /** @var LengthAwarePaginator $paginatedBookModels */
        $paginatedBookModels = $this->bookRepository->getPaginatedBooks($perPage, $page, [
            'author'
        ]);

        return fractal($paginatedBookModels, new BookTransformer())->respond(Response::HTTP_OK);
    }

    public function addBook(AddBookRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var Book $book */
        $book = Book::query()->create([
            'name' => $data['book_name'],
            'author_id' => $data['author_id'],
        ]);

        $book->load('author');

        UpdateAuthorsLastBookJob::dispatch($book->author, $book->name);

        return fractal($book, new BookTransformer())->respond(Response::HTTP_CREATED);
    }

    public function updateBook(UpdateBookRequest $request): JsonResponse
    {
        $data = $request->validated();

        $bookToUpdate = $this->bookRepository->getBookById($data['book_id'], [
            'id',
            'name',
            'author_id',
        ],
            [
                'author',
            ]);

        $author = $this->authorRepository->getAuthorById($data['author_id'], [
            'id',
            'full_name'
        ]);

        try {
            $bookToUpdate->updateOrFail([
                'name' => $data['book_name'],
                'author_id' => $author->id,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Unable to update author named ' . $author->full_name . ' Message: ' . $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $bookToUpdate->refresh();
        return fractal($bookToUpdate, new BookTransformer())->respond(Response::HTTP_OK);
    }

    public function deleteBook(int $id): JsonResponse
    {
        $modelToDelete = $this->bookRepository->getBookById($id, [
            'id'
        ]);

        try {
            $modelToDelete->delete();
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Unable to delete book named ' . $modelToDelete->name . '. Message: ' . $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json(['message' => 'Book successfully deleted.']);
    }
}