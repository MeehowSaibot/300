<?php

namespace App\Http\Services;

use App\Models\Author;
use App\Models\Book;
use Database\Seeders\TestCasesSeeder;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(TestCasesSeeder::class);
        $this->faker = Faker::create();

        $this->allAuthors = Author::query()->with('books')->get();
        $this->allBooks = Book::query()->with('author')->get();

        $this->firstBook = $this->allBooks->first();
        $this->firstAuthor = $this->allAuthors->first();

        $this->firstAuthorsFirstBook = $this->firstAuthor->books->first();
    }

    // api/GET/authors
    public function testGetAuthorsSuccess()
    {
        $this->assertDatabaseCount(Author::class, 20);
        $this->assertDatabaseCount(Book::class, 20);

        $response = $this->json('get', '/api/authors',
            [
                'perPage' => 15,
                'page' => 1
            ]);

        $expectedResponse = $this->allAuthors->take(15)->map(function ($author) {
            return [
                'author_id' => $author->id,
                'author' => $author->full_name,
                'books' => [
                    'data' => [
                        [
                            'book_id' => $author->books->first()->id,
                            'book_name' => $author->books->first()->name,
                        ]
                    ]
                ]
            ];
        });

        $expectedResponse = [
            'data' => $expectedResponse->toArray(),
            'meta' => [
                'pagination' => [
                    'total' => $this->allAuthors->count(),
                    'count' => $expectedResponse->count(),
                    'per_page' => 15,
                    'current_page' => 1,
                    'total_pages' => 2,
                    'links' => [],
                ],
            ],
        ];

        $response->assertJson($expectedResponse);

        $response->assertJsonStructure([
            'data' => [
                [
                    'author_id',
                    'author',
                    'books' => [
                        'data' => [
                            [
                                'book_id',
                                'book_name',
                            ]
                        ]
                    ]
                ]
            ],
            'meta' => [
                'pagination' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages',
                    'links',
                ],
            ],
        ]);
    }

    // api/GET/author/{id}
    public function testGetAuthorSuccess()
    {
        $response = $this->json('get', '/api/authors/' . $this->firstAuthor->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'data' => [
                'author_id',
                'author',
                'books' => [
                    'data' => [
                        [
                            'book_id',
                            'book_name'
                        ]
                    ]
                ],
            ],
        ]);

        $response->assertJson([
            'data' => [
                'author_id' => $this->firstAuthor->id,
                'author' => $this->firstAuthor->full_name,
                'books' => [
                    'data' => [
                        [
                            'book_id' => $this->firstAuthorsFirstBook->id,
                            'book_name' => $this->firstAuthorsFirstBook->name,
                        ],
                    ],
                ]
            ]
        ]);
    }
}