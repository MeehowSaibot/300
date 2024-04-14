<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateAuthorCommand extends Command
{
    protected $signature = 'author:create';
    protected $description = 'Adds a new author to the database.';

    public function handle(): void
    {
        try {
            DB::beginTransaction();
            $infoMessage = null;

            do {
                $name = $this->ask("What is the author's name?");
                $confirmName = $this->confirm("Is the author's name '$name' correct?");
            } while (!$confirmName);

            do {
                $surname = $this->ask("What is the author's surname?");
                $confirmSurname = $this->confirm("Is the author's surname '$surname' correct?");
            } while (!$confirmSurname);

            $confirmAuthor = $this->confirm("Create author '$name $surname'?");

            if (!$confirmAuthor) {
                $this->error('Author creation stopped.');
                return;
            }

            $author = new Author([
                'full_name' => $name . ' ' . $surname,
            ]);

            $confirmAddTitle = $this->confirm("Add last written book title to the author?");

            if ($confirmAddTitle) {
                do {
                    $lastBookTitle = $this->ask('What is the book title?');
                    $confirmLastTitle = $this->confirm("Is the book title '$lastBookTitle' correct?");
                } while (!$confirmLastTitle);

                $author->last_book = $lastBookTitle;
                $author->save();

                $confirmAddBook = $this->confirm("Add '$lastBookTitle' book to the database?");
                $infoMessage = 'Author successfully created.';

                if ($confirmAddBook) {

                    /** @var Book $book */
                    $author->books()->create([
                        'name' => $lastBookTitle,
                    ]);

                    $infoMessage = 'New author and new book successfully created.';
                }
            }

            DB::commit();
            $this->info($infoMessage);
        } catch (\Exception $exception) {
            DB::rollBack();

            $this->error($exception->getMessage());
        }
    }
}