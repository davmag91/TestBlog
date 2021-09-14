@extends('layout')

@section('content')
    <div class="row">
        <div class="col-4">
            <img src="{{ $user->image ? $user->image->url() : 'https://www.personality-insights.com/wp-content/uploads/2017/12/default-profile-pic-e1513291410505.jpg' }}" class="img-thumbnail avatar" />
        </div>
        <div class="col-8">
            <h3>{{ $user->name }}</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-comment-form :route="route('users.comments.store', ['user' => $user->id])" />
            <x-comment-list :comments="$user->commentsOn" />
        </div>
    </div>
@endsection
