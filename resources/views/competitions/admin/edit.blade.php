@title('Edit Competition: '.$comp->name)
@extends('app')

@section('content')
    <hc>
        <h1>Edit competition: {{ $comp->name }}</h1>
        <ol class="breadcrumb">
            <li><a href="{{ act('competition', 'index') }}">Competitions</a></li>
            <li><a href="{{ act('competition', 'brief', $comp->id) }}">{{ $comp->name}}</a></li>
            <li class="active">Edit</li>
        </ol>
    </hc>

    @form(competition-admin/edit upload=true)
        @hidden(id $comp)
        @text(name:competition_name $comp) = Competition Name
        @autocomplete(status_id api/competition-statuses $comp) = Competition Status
        @autocomplete(type_id api/competition-types $comp) = Competition Type
        @autocomplete(judge_type_id api/competition-judge-types $comp) = Judging Method
        @autocomplete(engines[] api/engines multiple=true $comp) = Allowed Engines
        @autocomplete(judges[] api/users multiple=true $comp) = Judges (if applicable)
        @textarea(brief_text $comp) = Competition Brief
        @text(open_date format=d/m/Y $comp) = Date Open (dd/mm/yyyy)
        @text(close_date format=d/m/Y $comp) = Date Closed (dd/mm/yyyy)
        @text(voting_close_date format=d/m/Y $comp) = Date Voting Closed (if applicable) (dd/mm/yyyy)
        @file(brief_attachment $comp) = (Optional) Attachment (16mb limit)
        @if ($comp->brief_attachment)
            <p>Current Attachment: {{ $comp->brief_attachment }}</p>
            @checkbox(delete_attachment) = Delete the attachment
        @endif
        @textarea(results_intro_text $comp) = Results Intro Text (required to close)
        @textarea(results_outro_text $comp) = Results Outro Text (optional)
        @submit = Edit Competition
    @endform
@endsection
