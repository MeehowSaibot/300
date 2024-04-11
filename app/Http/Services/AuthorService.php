<?php

namespace App\Http\Services;

use App\Http\Repositories\AuthorRepository;
use App\Http\Requests\GetAuthorsRequest;
use App\Http\Transformers\AuthorTransformer;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


class AuthorService
{
    public function __construct(
        private readonly AuthorRepository $authorRepository
    ) {
    }

    public function getAuthor(int $id): JsonResponse
    {
        try {
            /** @var Author $author */
            $author = $this->authorRepository->getAuthorById($id, [
                'id',
                'full_name'
            ],
                [
                    'books'
                ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Unable to find author named ' . $author->full_name . ' Message: ' . $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return fractal($author, new AuthorTransformer())->respond(Response::HTTP_OK);
    }

    public function getAuthors(GetAuthorsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $perPage = $data['perPage'] ?: 10;
        $page = $data['page'] ?: 1;

        $authors = $this->authorRepository->getPaginatedAuthors($perPage, $page, [
            'books'
        ]);

        return fractal($authors, new AuthorTransformer())->respond(Response::HTTP_OK);
    }
}