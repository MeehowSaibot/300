<?php

namespace App\Jobs;

use App\Models\Author;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAuthorsLastBookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Author $author,
        public string $title
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->author->last_book = $this->title;
        $this->author->updateOrFail();
    }
}
