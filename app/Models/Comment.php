<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'content',
        'post_id',
        'parent_id',
    ];
    public function post():BelongsTo {
        return $this->belongsTo(Post::class);
    }
    public function parent(): BelongsTo
    {
        // Menyebutkan nama kelasnya sendiri (Comment::class) dan kolom 'parent_id'
        return $this->belongsTo(Comment::class, 'parent_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
