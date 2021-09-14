<div class="container">
    <div class="row">
        <x-card title="Most Commented" subtitle="What people are currently talking about">
            @slot('items')
                @foreach ($MostCommented as $post)
                    <li class="list-group-item">
                        <a href="{{ route('posts.show', ['post' => $post->id]) }}">
                            {{ $post->title }}
                        </a>
                        <br />
                        <div class="text-muted">{{ $post->comments_count }} comments</div>
                    </li>
                @endforeach
            @endslot
        </x-card>
    </div>
    <div class="row mt-4">
        <x-card title="Most Active" subtitle="Users with most posts written">
            @slot('items')
                @foreach ($MostActive as $u)
                    <li class="list-group-item">
                        {{ $u->name }}
                        <br />
                        <div class="text-muted">{{ $u->blog_posts_count }} posts</div>
                    </li>
                @endforeach
            @endslot
        </x-card>
    </div>
    <div class="row mt-4">
        <x-card title="Most Active Last Month" subtitle="Users with most posts written in the Last Month">
            @slot('items')
                @foreach ($MostActiveLastMonth as $u)
                    <li class="list-group-item">
                        {{ $u->name }}
                        <br />
                        <div class="text-muted">{{ $u->blog_posts_count }} posts</div>
                    </li>
                @endforeach
            @endslot
        </x-card>
    </div>
</div>
