<div class="mb-2 mt-2">
    @auth
        <form method="POST" action="{{ $route }}">
            @csrf

            <div class="form-group">
                <label>Content</label>
                <textarea type="text" name="content" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Add Comment</button>
        </form>
        <x-errors></x-errors>
    @else
        <a href="{{ route('login') }}">Sign-in</a> to post comments!
    @endauth
    <hr/>
</div>
