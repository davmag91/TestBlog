<?php
namespace App\Http\ViewComposers;

use App\Models\User;
use App\Models\BlogPost;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class ActivityComposer
{
    public function compose(View $view)
    {
        $MostCommented = Cache::tags(['blog-post'])->remember('blog-post-commented', 60, function () {
            return BlogPost::MostCommented()->take(5)->get();
        });


        $MostActive = Cache::remember('users-most-active', 60, function () {
            return User::WithMostBlogPosts()->take(5)->get();
        });

        $MostActiveLastMonth = Cache::remember('users-most-active-last-month', 60, function () {
            return BlogPost::MostCommented()->take(5)->get();
        });

        $view->with('MostCommented',$MostCommented);
        $view->with('MostActive',$MostActive);
        $view->with('MostActiveLastMonth', $MostActiveLastMonth);
    }
}