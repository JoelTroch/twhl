@extends('app')

@section('content')
    <hc>
        @if ($journal->isEditable())
            <a href="{{ act('journal', 'delete', $journal->id) }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> Delete</a>
            <a href="{{ act('journal', 'edit', $journal->id) }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
        @endif
        <h1>Journal Post by @avatar($journal->user inline)</h1>
        <ol class="breadcrumb">
            <li><a href="{{ act('journal', 'index') }}">Journals</a></li>
            <li class="active">View Journal</li>
        </ol>
    </hc>
    <div class="media media-panel">
        <div class="media-left">
            <div class="media-object">
                @avatar($journal->user small show_border=true show_name=false)
            </div>
        </div>
        <div class="media-body">
            <div class="media-heading">
                @avatar($journal->user text) &bull;
                @date($journal->created_at) &bull;
                <a href="#comments" class="btn btn-xs btn-link link">
                    <span class="glyphicon glyphicon-comment"></span>
                    {{ $journal->stat_comments }} comment{{$journal->stat_comments==1?'':'s'}}
                </a>
            </div>
            <div class="bbcode">
                {!! $journal->content_html !!}
            </div>
        </div>
    </div>
    @include('comments.list', [ 'article' => $journal, 'comments' => $comments, 'article_type' => \App\Models\Comments\Comment::JOURNAL, 'article_id' => $journal->id ])
@endsection