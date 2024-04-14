<?php

namespace Console\Commands;

use App\Models\Author;
use App\Models\Book;
use Database\Seeders\TestCasesSeeder;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateAuthorCommandTest extends TestCase
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

    // php artisan author:create
    public function testCreateAuthorCommandSuccess()
    {
        $newAuthorName = $this->faker->firstName;
        $newAuthorSurname = $this->faker->lastName;
        $newBookTitle = $this->faker->sentence;

        $this->assertDatabaseCount(Author::class, 20);
        $this->assertDatabaseCount(Book::class, 20);

        $this->artisan('author:create')
            ->expectsQuestion("What is the author's name?", $newAuthorName)
            ->expectsConfirmation("Is the author's name '$newAuthorName' correct?", 'yes')
            ->expectsQuestion("What is the author's surname?", $newAuthorSurname)
            ->expectsConfirmation("Is the author's surname '$newAuthorSurname' correct?", 'yes')
            ->expectsConfirmation("Create author '$newAuthorName $newAuthorSurname'?", 'yes')
            ->expectsQuestion("Add last written book title to the author?", 'yes')
            ->expectsQuestion('What is the book title?', $newBookTitle)
            ->expectsConfirmation("Is the book title '$newBookTitle' correct?", 'yes')
            ->expectsConfirmation("Add '$newBookTitle' book to the database?")
            ->expectsOutput('Author successfully created.');

        /** @var Author $newAuthor */
        $newAuthor = Author::query()->where('full_name', "$newAuthorName $newAuthorSurname")->first();

        $this->assertEquals($newBookTitle, $newAuthor->last_book);
        $this->assertDatabaseCount(Author::class, 21);
        $this->assertDatabaseCount(Book::class, 20);
    }

    // php artisan author:create
    public function testCreateAuthorWithNewBookCommandSuccess()
    {
        $newAuthorName = $this->faker->firstName;
        $newAuthorSurname = $this->faker->lastName;
        $newBookTitle = $this->faker->sentence;

        $this->assertDatabaseCount(Author::class, 20);
        $this->assertDatabaseCount(Book::class, 20);

        $this->artisan('author:create')
            ->expectsQuestion("What is the author's name?", $newAuthorName)
            ->expectsConfirmation("Is the author's name '$newAuthorName' correct?", 'yes')
            ->expectsQuestion("What is the author's surname?", $newAuthorSurname)
            ->expectsConfirmation("Is the author's surname '$newAuthorSurname' correct?", 'yes')
            ->expectsConfirmation("Create author '$newAuthorName $newAuthorSurname'?", 'yes')
            ->expectsQuestion("Add last written book title to the author?", 'yes')
            ->expectsQuestion('What is the book title?', $newBookTitle)
            ->expectsConfirmation("Is the book title '$newBookTitle' correct?", 'yes')
            ->expectsConfirmation("Add '$newBookTitle' book to the database?",'yes')
            ->expectsOutput('New author and new book successfully created.');

        /** @var Author $newAuthor */
        $newAuthor = Author::query()->where('full_name', "$newAuthorName $newAuthorSurname")->first();

        /** @var Book $newBook */
        $newBook = $newAuthor->books()->first();

        $this->assertDatabaseCount(Author::class, 21);
        $this->assertDatabaseCount(Book::class, 21);
        $this->assertEquals($newBookTitle, $newAuthor->last_book);
        $this->assertEquals($newAuthor->id, $newBook->author_id);
    }

    public function testCreateAuthorCreationStoppedFail()
    {
        $this->assertDatabaseCount(Author::class, 20);
        $this->assertDatabaseCount(Book::class, 20);

        $newAuthorName = $this->faker->firstName;
        $newAuthorSurname = $this->faker->lastName;

        $this->artisan('author:create')
            ->expectsQuestion("What is the author's name?", $newAuthorName)
            ->expectsConfirmation("Is the author's name '$newAuthorName' correct?", 'yes')
            ->expectsQuestion("What is the author's surname?", $newAuthorSurname)
            ->expectsConfirmation("Is the author's surname '$newAuthorSurname' correct?", 'yes')
            ->expectsConfirmation("Create author '$newAuthorName $newAuthorSurname'?")
            ->expectsOutput('Author creation stopped.');

        $this->assertDatabaseCount(Author::class, 20);
        $this->assertDatabaseCount(Book::class, 20);
    }

}