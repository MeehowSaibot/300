<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class TestCasesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if ('testing' === app()->environment()) {
            $faker = Faker::create();
            $authors = Author::factory(20)->create();

            /** @var Author $author */
            foreach ($authors as $author) {
                $book = Book::factory()->create([
                    'author_id' => $author->id,
                    'name' => $faker->sentence,
                ]);

                $author->last_book = $book->name;
                $author->save();
            }
        }
    }
}
