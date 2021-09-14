<?php

namespace App\Models;

use App\Models\BlogPost;
use App\Scopes\LatestScope;
use App\Traits\Taggable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, SoftDeletes, Taggable;

    protected $fillable = ['user_id', 'content', 'commentable_id', 'commentable_type'];

    public function blogPost()
    {
        return $this->belongsTo(BlogPost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeLatest(Builder $query)
    {
        return $query->orderBy(static::CREATED_AT, 'desc');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function (Comment $comment) {
            if ($comment->commentable_type == BlogPost::class) {
                Cache::tags(['blog-post'])->forget("blog-post-{$comment->blog_post_id}");
                Cache::tags(['blog-post'])->forget("MostCommented}");
            }
        });

        //static::addGlobalScope(new LatestScope);
    }
}
