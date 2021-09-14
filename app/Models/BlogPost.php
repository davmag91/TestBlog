<?php

namespace App\Models;

use App\Models\Comment;
use App\Scopes\DeletedAdminScope;
use App\Traits\Taggable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogPost extends Model
{
    protected $fillable = ['title','content','user_id'];

    use HasFactory, SoftDeletes, Taggable;

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
        //return $this->morphOne(Image::class, 'blog_post');
    }

    public function scopeLatest(Builder $query){
        return $query->orderBy(static::CREATED_AT, 'desc');
    }

    public function scopeMostCommented(Builder $query)
    {
        return $query->withCount('comments')->orderBy('comments_count', 'desc');
    }

    public function scopeLatestWithRelations(Builder $query)
    {
        return $query
        ->latest()
        ->withCount('comments')
        ->with('user')
        ->with('tags');
    }

    public static function boot()
    {   
        static::addGlobalScope(new DeletedAdminScope);
        
        parent::boot();

        static::updating(function(BlogPost $blogPost){
            Cache::tags(['blog-post'])->forget("blog-post-{$blogPost->id}");
        });

        static::deleting(function(BlogPost $blogPost){
              $blogPost->comments()->delete();
              //$blogPost->image()->delete();
              Cache::tags(['blog-post'])->forget("blog-post-{$blogPost->id}");
         });

         static::restored(function(BlogPost $blogPost){
            $blogPost->comments()->restore();
         });
    }
}
