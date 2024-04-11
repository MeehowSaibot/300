<?php

namespace App\Http\Services;

use App\Jobs\UpdateAuthorsLastBookJob;
use App\Models\Author;
use App\Models\Book;
use Database\Seeders\TestCasesSeeder;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BooksControllerTest extends TestCase
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

    // api/POST/books
    public function testAddBookSuccess()
    {
        Queue::fake();

        $this->assertDatabaseCount(Author::class, 20);
        $this->assertDatabaseCount(Book::class, 20);

        Queue::assertNothingPushed();

        $newBookName = $this->faker->sentence;

        $response = $this->json('post', '/api/books/',
            [
                'book_name' => $newBookName,
                'author_id' => $this->firstAuthor->id,
            ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseCount(Book::class, 21);

        /** @var Book $newBook */
        $newBook = Book::query()->where('name', $newBookName)->first();

        $response->assertJsonStructure([
            'data' => [
                'book_id',
                'book_name',
                'author' => [
                    'data' => [
                        'author_id',
                        'author'
                    ]
                ]
            ]
        ]);

        $response->assertJson([
            'data' =>
                [
                    'book_id' => $newBook->id,
                    'book_name' => $newBookName,
                    'author' => [
                        'data' => [
                            'author_id' => $this->firstAuthor->id,
                            'author' => $this->firstAuthor->full_name,
                        ]
                    ]
                ]
        ]);

        Queue::assertPushed(UpdateAuthorsLastBookJob::class, function (UpdateAuthorsLastBookJob $job) use ($newBook) {
            return $job->author->id === $this->firstAuthor->id && $job->title === $newBook->name;
        });

        $this->assertEquals($this->firstAuthor->last_book, $this->firstAuthorsFirstBook->name);
        $job = new UpdateAuthorsLastBookJob($this->firstAuthor, $newBookName);
        $job->handle();
        $this->assertEquals($this->firstAuthor->last_book, $newBookName);
    }

    // api/POST/books/
    public function testAddBookAlreadyExistsFail()
    {
        $this->assertDatabaseCount(Author::class, 20);
        $this->assertDatabaseCount(Book::class, 20);

        $response = $this->json('post', '/api/books/',
            [
                'book_name' => $this->firstBook->name,
                'author_id' => $this->firstAuthor->id,
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseCount(Book::class, 20);

        $response->assertJson([
            'message' => 'The book name has already been taken.',
            'errors' =>
                [
                    'book_name' =>
                        [
                            'The book name has already been taken.',
                        ],
                ],
        ]);
    }

    // api/DELETE/books
    public function testDeleteBookSuccess()
    {
        $this->assertCount(1, $this->firstAuthor->books);
        $this->assertDatabaseCount(Book::class, 20);
        $test1 = $this->firstAuthorsFirstBook->id;

        $response = $this->json('delete', '/api/books/' . $this->firstAuthorsFirstBook->id);

        $this->firstAuthor->refresh();

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['message' => 'Book successfully deleted.']);

        $this->assertDatabaseCount(Book::class, 19);
        $this->assertCount(0, $this->firstAuthor->books);
    }

    // api/GET/books
    public function testGetBooksSuccess()
    {
        $response = $this->json('get', '/api/books',
            [
                'perPage' => 10,
                'page' => 1
            ]);

        $expectedResponse = $this->allBooks->take(10)->map(function ($book) {
            return [
                'book_id' => $book->id,
                'book_name' => $book->name,
                'author' => [
                    'data' => [
                        'author' => $book->author->full_name,
                        'author_id' => $book->author->id,
                    ]
                ]
            ];
        });

        $expectedResponse = [
            'data' => $expectedResponse->toArray(),
            'meta' => [
                'pagination' => [
                    'total' => $this->allBooks->count(),
                    'count' => $expectedResponse->count(),
                    'per_page' => 10,
                    'current_page' => 1,
                    'total_pages' => 2,
                    'links' => [],
                ],
            ],
        ];

        $response->assertJson($expectedResponse);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'book_id',
                    'book_name',
                    'author' => [
                        'data' => [
                            'author_id',
                            'author'
                        ]
                    ]
                ],
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

    // api/GET/books/{id}
    public function testGetBookSuccess()
    {
        $response = $this->json('get', '/api/books/' . $this->firstBook->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'data' => [
                'book_id',
                'book_name',
                'author' => [
                    'data' => [
                        'author_id',
                        'author'
                    ]
                ]
            ],
        ]);

        $response->assertJson([
            'data' => [
                'book_id' => $this->firstBook->id,
                'book_name' => $this->firstBook->name,
                'author' => [
                    'data' => [
                        'author' => $this->firstAuthor->full_name,
                        'author_id' => $this->firstAuthor->id,
                    ]
                ]
            ]
        ]);
    }

    // api/PUT/books
    public function testUpdateBookSuccess()
    {
        /** @var Author $newAuthor */
        $newAuthor = $this->allAuthors->last();

        $newData = [
            'book_id' => $this->firstBook->id,
            'book_name' => $this->faker->sentence,
            'author_id' => $newAuthor->id
        ];

        $response = $this->json('put', '/api/books/', $newData);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'data' => [
                'book_id' => $this->firstBook->id,
                'book_name' => $newData['book_name'],
                'author' => [
                    'data' => [
                        'author_id' => $newAuthor->id,
                        'author' => $newAuthor->full_name
                    ]
                ]
            ]
        ]);

        $response->assertJsonStructure([
            'data' => [
                'book_id',
                'book_name',
                'author' => [
                    'data' => [
                        'author_id',
                        'author'
                    ]
                ]
            ],
        ]);
    }
}