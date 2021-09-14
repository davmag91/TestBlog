<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Image;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use App\Http\Requests\StorePost;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

//Model->Policy
// [
//     'show' => 'view',
//     'create' => 'create',
//     'store' => 'create',
//     'edit' => 'update',
//     'update' => 'update',
//     'destroy' => 'delete'
// ]

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*
        //eager loading
        $posts = BlogPost::with('comments')->get();

        //lazy loading
        foreach($posts as $post)
            foreach($post->comments as $comment)
                echo $comment->content;*/

        return view(
            'posts.index',
            [
                'posts' => BlogPost::LatestWithRelations()->get()
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return view('posts.show', [
        //     'post' => BlogPost::with(['comments' => function($query){
        //         return $query->latest();
        //     }])->findOrFail($id)
        // ]);

        $blogPost = Cache::tags(['blog-post'])->remember(
            "blog-post-{$id}",
            60,
            fn () =>
            BlogPost::with('comments', 'tags', 'user', 'comments.user')
                ->findOrFail($id)
        );

        $sessionId = session()->getId();
        $counterKey = "blog-post-{$id}-counter";
        $usersKey = "blog-post-{$id}-users";

        $users = Cache::tags(['blog-post'])->get($usersKey, []);

        $usersUpdate = [];
        $difference = 0;
        $now = now();

        foreach ($users as $session => $lastVisit) {
            if ($now->diffInMinutes($lastVisit) >= 1) {
                $difference--;
            } else {
                $usersUpdate[$session] = $lastVisit;
            }
        }

        if (!array_key_exists($sessionId, $users) || $now->diffInMinutes($users[$sessionId]) >= 1) {
            $difference++;
        }

        $usersUpdate[$sessionId] = $now;
        Cache::tags(['blog-post'])->forever($usersKey, $usersUpdate);

        if (!Cache::tags(['blog-post'])->has($counterKey)) {
            Cache::tags(['blog-post'])->forever($counterKey, 1);
        } else {
            Cache::tags(['blog-post'])->increment($counterKey, $difference);
        }

        $counter = Cache::tags(['blog-post'])->get($counterKey);

        return view('posts.show', [
            'post' => $blogPost,
            'counter' => $counter
        ]);
    }

    public function create()
    {
        //$this->authorize('create');

        return view('posts.create');
    }

    public function store(StorePost $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = request()->user()->id;
        $blogPost = BlogPost::create($validatedData);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails');

            //ddd($blogPost->image);

            // $blogPost->image->save(Image::make([
            //     'path' => $path
            // ]));

            Image::create([
                'path' => $path,
                'imageable_id' => $blogPost->id,
                'imageable_type' => 'App\Models\BlogPost'
            ]);

            //$name1 = $file->storeAs('thumbnails', $blogPost->id. '.' . $file->guessExtension());
            //Storage::url($name1);
            //$name2 = Storage::disk('local')->putFileAs('thumbnails', $blogPost->id. '.' . $file->guessExtension());
        }


        $request->session()->flash('status', 'Blog post was created!');

        return redirect()->route('posts.show', ['post' => $blogPost->id]);
    }

    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);

        // if (Gate::denies('update-post', $post))
        //     abort(403, "You can't edit this blogpost.");

        $this->authorize($post);

        return view('posts.edit', ['post' => $post]);
    }

    public function update(StorePost $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        // if (Gate::denies('update-post', $post))
        //     abort(403, "You can't edit this blogpost.");

        $this->authorize($post);

        $validatedData = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails');

            if($post->image)
            {
                Storage::delete($post->image->path);
                $post->image->path = $path;
                $post->image->save();
            }else{
                // Image::create([
                //     'path' => $path,
                //     'imageable_id' => $post->id
                // ]);

                $post->image()->save(
                    Image::make(['path' => $path])
                );
            }
        }

        $post->fill($validatedData);
        $post->save();
        $request->session()->flash('status', 'Blog post was updated!');

        return redirect()->route('posts.show', ['post' => $post->id]);
    }

    public function destroy(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        // if (Gate::denies('delete-post', $post))
        //     abort(403, "You can't delete this blogpost.");

        $this->authorize($post);

        $post->delete();

        // BlogPost::destroy($id);

        $request->session()->flash('status', 'Blog post was deleted!');

        return redirect()->route('posts.index');
    }
}
