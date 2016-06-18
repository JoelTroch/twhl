@title('Competition: '.$comp->name)
@extends('app')

@section('content')
    <hc>
        @if (permission('CompetitionAdmin'))
            <a href="{{ act('competition-admin', 'delete', $comp->id) }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> Delete</a>
            <a href="{{ act('competition-admin', 'edit-rules', $comp->id) }}" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-list-alt"></span> Edit Rules</a>
            <a href="{{ act('competition-admin', 'edit', $comp->id) }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
        @endif
        <h1>Competition brief: {{ $comp->name }}</h1>
        <ol class="breadcrumb">
            <li><a href="{{ act('competition', 'index') }}">Competitions</a></li>
            <li class="active">View Brief</li>
        </ol>
    </hc>
    <div class="row competition-brief">
        <div class="col-lg-4 col-lg-push-8 col-md-6 col-md-push-6">

            <div class="text-center">
                <span class="comp-status-message">Competition Status:</span>
                <span class="comp-status">{{ $comp->getStatusText() }}</span>
                @if ($comp->isOpen())
                    <div id="countdown" class="countdown"></div>
                    @if ($comp->canJudge())
                        <hr />
                        <a href="{{ act('competition-judging', 'view', $comp->id) }}" class="btn btn-inverse btn-xs"><span class="glyphicon glyphicon-eye-open"></span> View Entries</a>
                    @endif
                @elseif ($comp->isVotingOpen())
                    <a href="{{ act('competition', 'vote', $comp->id) }}" class="btn btn-success btn-lg">{{ $comp->canVote() ? 'Vote Now' : 'View Entries' }}</a>
                    @if ($comp->canJudge())
                        <hr />
                        <a href="{{ act('competition-judging', 'view', $comp->id) }}" class="btn btn-inverse btn-xs"><span class="glyphicon glyphicon-eye-open"></span> Edit Entries / Results</a>
                    @endif
                @elseif ($comp->isJudging() && $comp->canJudge())
                    <a href="{{ act('competition-judging', 'view', $comp->id) }}" class="btn btn-inverse btn-lg"><span class="glyphicon glyphicon-eye-open"></span> Go to Judging Panel</a>
                    <hr/>
                    <a href="{{ act('competition-judging', 'preview', $comp->id) }}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-eye-open"></span> Preview Results</a>
                @elseif ($comp->isClosed())
                    <a href="{{ act('competition', 'results', $comp->id) }}" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-eye-open"></span> View Results</a>
                    @if ($comp->canJudge())
                        <hr />
                        <a href="{{ act('competition-judging', 'view', $comp->id) }}" class="btn btn-inverse btn-xs"><span class="glyphicon glyphicon-eye-open"></span> Edit Entries / Results</a>
                    @endif
                @endif
            </div>

            <hr/>

            <dl class="dl-horizontal dl-small">
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
                <dt>Judges</dt>
                <dd>
                    {? $i = 0 ?}
                    @foreach ($comp->judges as $judge)
                        {!! $i++ != 0 ? '&bull;' : '' !!}
                        @avatar($judge inline)
                    @endforeach
                </dd>
                @endif
            </dl>

        </div>
        <div class="col-lg-8 col-lg-pull-4 col-md-6 col-md-pull-6">
            <div class="bbcode">{!! $comp->brief_html !!}</div>
            @if ($comp->brief_attachment)
                <div class="well well-sm">
                    Attached file:
                    <a href="{{ asset('uploads/competition/attachments/'.$comp->brief_attachment) }}" class="btn btn-success btn-xs">
                        <span class="glyphicon glyphicon-download-alt"></span>
                        Click to download
                    </a>
                </div>
            @endif
        </div>
    </div>

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
        @include('competitions._gallery_javascript')
    @endif
    @if ($comp->isOpen() && permission('CompetitionEnter'))
        <hr>
        <h3>{{ $user_entry ? 'Update' : 'Submit' }} Entry</h3>
        @form(competition-entry/submit upload=true)
            @include('competitions.entry._entry-form-fields', [ 'comp' => $comp, 'entry' => $user_entry ])
        @endform
    @endif
    @if ($comp->isClosed())

        <hc>
            <h1>
                Results
                @if ($comp->isJudged() && $comp->judges->count() > 0)
                    <small class="pull-right">
                        Judged By:
                        {? $i = 0; ?}
                        @foreach ($comp->judges as $judge)
                            {!! $i++ == 0 ? '' : ' &bull; ' !!}
                            @avatar($judge inline)
                        @endforeach
                    </small>
                @endif
            </h1>
        </hc>


        @if ($comp->results_intro_html)
            <div class="bbcode">{!! $comp->results_intro_html !!}</div>
        @endif
        <ul class="media-list">
            {? $prev_rank = -1; ?}
            @foreach ($comp->getEntriesForResults() as $entry)
                {? $result = $comp->results->where('entry_id', $entry->id)->first(); ?}
                @if ($prev_rank != 0 && $result->rank == 0)
                    </ul>
                    <hr/>
                    <h3>Other Entries</h3>
                    <ul class="media-list">
                @endif
                {? $shot = $entry->screenshots->first(); ?}
                {? $prev_rank = $result->rank; ?}
                <li class="media media-panel" data-id="{{ $entry->id }}">
                    <div class="media-heading">
                        {? $result = $comp->results->where('entry_id', $entry->id)->first(); ?}
                        @if ($result->rank == 1)
                            <h2>1st Place</h2>
                        @elseif ($result->rank == 2)
                            <h2>2nd Place</h2>
                        @elseif ($result->rank == 3)
                            <h2>3rd Place</h2>
                        @endif
                        <h3>{{ $entry->title }}</h3>
                        <h5>
                            By @avatar($entry->user inline)
                            @if ($entry->file_location)
                                <a href="{{ $entry->getLinkUrl() }}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-download-alt"></span> Download</a>
                            @endif
                        </h5>
                    </div>
                    <div class="media-body">
                        <div class="visible-sm-block visible-xs-block text-center">
                            <div style="display: inline-block;">
                                <a href="#" class="gallery-button img-thumbnail">
                                    @if ($shot)
                                        <img class="media-object" src="{{ asset('uploads/competition/'.$shot->image_thumb) }}" alt="Screenshot" />
                                    @else
                                        <img class="media-object" src="{{ asset('images/no-screenshot-320.png') }}" alt="Screenshot" />
                                    @endif
                                </a>
                                @if ($entry->screenshots->count() > 1)
                                    <button class="btn btn-info btn-block gallery-button" type="button">
                                        <span class="glyphicon glyphicon-picture"></span>
                                        + {{ $entry->screenshots->count()-1 }} more screenshot{{ $entry->screenshots->count() == 2 ? '' : 's' }}
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="bbcode">{!! $result->content_html !!}</div>
                    </div>
                    <div class="media-right hidden-xs hidden-sm">
                        <a href="#" class="gallery-button img-thumbnail">
                            @if ($shot)
                                <img class="media-object" src="{{ asset('uploads/competition/'.$shot->image_thumb) }}" alt="Screenshot" />
                            @else
                                <img class="media-object" src="{{ asset('images/no-screenshot-320.png') }}" alt="Screenshot" />
                            @endif
                        </a>
                        @if ($entry->screenshots->count() > 1)
                            <button class="btn btn-info btn-block gallery-button" type="button">
                                <span class="glyphicon glyphicon-picture"></span>
                                + {{ $entry->screenshots->count()-1 }} more screenshot{{ $entry->screenshots->count() == 2 ? '' : 's' }}
                            </button>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
        @if ($comp->results_outro_html)
            <div class="bbcode">{!! $comp->results_outro_html !!}</div>
        @endif


    @endif
@endsection

@section('scripts')
    <script type="text/javascript">
        $('#countdown').countdown({until: new Date({{ $comp->getCloseTime()->format('U') }} * 1000), description: 'Until the competition is closed'});
    </script>
@endsection