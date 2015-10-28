@extends('app')

@section('content')
    <h2>Edit Poll: {{ $poll->title }}</h2>
    @form(poll/edit)
        @hidden(id $poll)
        @text(title $poll) = Poll Title
        @text(close_date format=d/m/Y $poll) = Close Date (dd/mm/yyyy)
        @textarea(content_text $poll class=small) = Poll Description (don't put the items in here)
        <div class="alert alert-warning">Changing the order of the items or deleting from the middle will screw up the votes. Be careful.</div>
        @textarea(items $items class=small) = Poll Items (one per line)
        @submit = Edit poll
    @endform
@endsection