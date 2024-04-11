<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetAuthorsRequest;
use App\Http\Services\AuthorService;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{

    public function __construct(
        private readonly AuthorService $authorService
    ) {
    }

    public function getAuthors(GetAuthorsRequest $request): JsonResponse
    {
        return $this->authorService->getAuthors($request);
    }

    public function getAuthor(int $id): JsonResponse
    {
        return $this->authorService->getAuthor($id);
    }


}
