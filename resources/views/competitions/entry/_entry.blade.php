{? $shot = $entry->screenshots->first(); ?}
<div class="media" data-id="{{ $entry->id }}">
    <div class="media-left">
        <a href="#" class="gallery-button img-thumbnail">
            <img class="media-object" src="{{asset( $shot ? 'uploads/competition/'.$shot->image_thumb : 'images/no-screenshot-320.png' ) }}" alt="Entry">
        </a>
        @if ($entry->screenshots->count() > 1)
            <button class="btn btn-info btn-block gallery-button" type="button">
                <span class="glyphicon glyphicon-picture"></span>
                + {{ $entry->screenshots->count()-1 }} more screenshot{{ $entry->screenshots->count() == 2 ? '' : 's' }}
            </button>
        @endif
    </div>
    <div class="media-body">
        <h3 class="media-heading">
            {{ $entry->title }} &mdash; By @avatar($entry->user inline)</small>
            @if (!isset($deleting) || !$deleting)
                @if (permission('CompetitionAdmin') || ( permission('CompetitionEnter') && Auth::user()->id == $entry->user_id && $comp->canEnter() ))
                    <a href="{{ act('competition-entry', 'delete', $entry->id) }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> Delete Entry</a>
                    <a href="{{ act('competition-entry', 'manage', $entry->id) }}" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-picture"></span> Edit Screenshots</a>
                @endif
            @endif
        </h3>
        <div class="bbcode">
            {!! $entry->content_html ? $entry->content_html : '<em>No Description</em>' !!}
        </div>
        @if ($entry->getLinkUrl())
        <p>
            <a href="{{ $entry->getLinkUrl() }}" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-download-alt"></span> Download</a>
        </p>
        @endif
    </div>
</div>