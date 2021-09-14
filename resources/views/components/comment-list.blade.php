@forelse($comments as $comment)
<p>
    {{ $comment->content }}
</p>
<x-tags :tags="$comment->tags"></x-tags>
<x-updated :name="$comment->user->name" :date="$comment->created_at->diffForHumans()" :userId="$comment->user->id" />
@empty
<p>No comments yet</p>
@endforelse