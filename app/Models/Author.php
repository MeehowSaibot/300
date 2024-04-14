<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;


/**
 * @property-read int $id
 * @property string $full_name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $last_book
 * @property Collection $books
 */
class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'last_book',
    ];

    /**
     * @return HasMany
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'author_id', 'id');
    }
}