<?php

namespace App\Models;

use App\Models\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'kategori_id',
        'title',
        'slug',
        'content',
        'thumbnail',
        'is_published',
        'published_at',
    ];
    //1 post hanya dimiliki 1 user
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
    public function kategori(): BelongsTo {
        return $this->belongsTo(Kategori::class);
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }

    public function tags():BelongsToMany {
        return $this->belongsToMany(Tag::class);
    }
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
    //Buat local scope
    public function scopePublished($query) {
      return  $query->where('is_published',true)
              ->where('published_at', '<=', now());
    }

    // #[Override]
    // public function getRouteKeyName(): string
    // {
    //     return 'slug';
    // }
    //buat global scope
    // protected static function booted(): void {
    //     static::addGlobalScope(new PublishedScope());
    // }
}
