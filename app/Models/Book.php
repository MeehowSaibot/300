<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property string $name
 * @property-read int $author_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property BelongsTo $author
 */
class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'author_id'
    ];

    /**
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id', 'id');
    }
}