@extends('app')

@section('content')
    @foreach ($forums as $forum)
        <div class="row">
            <div class="col-md-8">
                <h3>
                    <a href="{{ act('forum', 'view', $forum->slug) }}">{{ $forum->name }}</a>
                    @if (permission('ForumAdmin'))
                        <a href="{{ act('forum', 'edit', $forum->id) }}" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>
                    @endif
                </h3>
                <p>
                    {{ $forum->description }}
                </p>
            </div>
            <div class="col-md-4 forum-info">
                <span class="label label-info">{{ $forum->stat_posts }} posts in {{ $forum->stat_threads }} threads</span>
                <p>
                    @if ($forum->last_post)
                    Last post: {{ Date::TimeAgo($forum->last_post->updated_at) }}
                    <br>
                    In thread: <a href="{{ act('thread', 'view', $forum->last_post->thread->id, 'last') }}">{{ $forum->last_post->thread->title }}</a>
                    <br>
                    By user: <a href="#">{{ $forum->last_post->user->name }}</a>
                    @endif
                </p>
            </div>
        </div>
    @endforeach
    @if (permission('ForumAdmin'))
        <hr/>
        <p>
            <a href="{{ act('forum', 'create') }}">Create new forum</a>
        </p>
    @endif
@endsection