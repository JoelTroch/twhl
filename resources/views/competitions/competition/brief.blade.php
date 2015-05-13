@extends('app')

@section('content')
    <h2>
        Competition Brief: {{ $comp->name }}
        @if (permission('CompetitionAdmin'))
            <a href="{{ act('competition-admin', 'delete', $comp->id) }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> Delete</a>
            <a href="{{ act('competition-admin', 'edit-rules', $comp->id) }}" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-list-alt"></span> Edit Rules</a>
            <a href="{{ act('competition-admin', 'edit', $comp->id) }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
        @endif
    </h2>
    <div class="row">
        <div class="col-md-6 text-center">
            <span class="comp-status-message">Competition Status:</span>
            <span class="comp-status">{{ $comp->getStatusText() }}</span>
            @if ($comp->isOpen())
                <div id="countdown" class="countdown"></div>
            @elseif ($comp->isVotingOpen())
                <a href="{{ act('competition', 'vote', $comp->id) }}" class="btn btn-success btn-lg">{{ $comp->canVote() ? 'Vote Now' : 'View Entries' }}</a>
            @endif
        </div>
        <div class="col-md-6">
            <dl class="dl-horizontal">
                <dt>Open Date</dt><dd>{{ $comp->open_date->format('jS F Y') }} (00:00 GMT)</dd>
                <dt>Close Date</dt><dd>{{ $comp->close_date->format('jS F Y') }} (23:59 GMT)</dd>
                @if ($comp->isVoted())
                <dt>Voting Open Date</dt><dd>{{ $comp->getVotingOpenTime()->format('jS F Y') }} (01:00 GMT)</dd>
                <dt>Voting Close Date</dt><dd>{{ $comp->getVotingCloseTime()->format('jS F Y') }} (23:59 GMT)</dd>
                @endif
                <dt>Type</dt><dd>{{ $comp->type->name }}</dd>
                <dt>Judging Type</dt><dd>{{ $comp->judge_type->name }}</dd>
                <dt>Allowed Engines</dt><dd>{{ implode(', ', $comp->engines->map(function($x) { return $x->name; })->toArray() ) }}</dd>
                @if (count($comp->judges) > 0)
                <dt>Judges</dt><dd>{!! implode('<br>', $comp->judges->map(function($x) { return e($x->name); })->toArray() ) !!}</dd>
                @endif
            </dl>
        </div>
    </div>
    <hr/>
    <div class="bbcode">{!! $comp->brief_text !!}</div>

    @if (count($rule_groups) > 0)
        <hr />
        <h3>Competition Rules</h3>
        <ul>
            @foreach ($rule_groups as $group => $rules)
                <li>
                    <strong>{{ $group }}</strong>
                    <ul>
                    @foreach ($rules as $rule)
                        <li><div class="bbcode">{!! $rule !!}</div></li>
                    @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    @endif
    @if ($user_entry)
        <hr>
        <h3>Your Entry</h3>
        @include('competitions.entry._entry', [ 'comp' => $comp, 'entry' => $user_entry ])
    @endif
    @if ($comp->isOpen() && permission('CompetitionEnter'))
        <hr>
        @include('competitions.entry._entry-form', [ 'comp' => $comp, 'entry' => $user_entry ])
    @endif
@endsection

@section('styles')
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jquery-countdown/2.0.1/jquery.countdown.min.css">
@endsection

@section('scripts')
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery-countdown/2.0.1/jquery.plugin.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery-countdown/2.0.1/jquery.countdown.min.js"></script>
    <script type="text/javascript">
        $('#countdown').countdown({until: new Date({{ $comp->getCloseTime()->format('U') }} * 1000), description: 'Until the competition is closed'});
    </script>
@endsection