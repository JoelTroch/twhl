@extends('app')

@section('content')
    <h2>
        {{ $item->name }}
        @if (permission('VaultAdmin'))
            <a class="btn btn-xs btn-danger" href="{{ act('vault', 'delete', $item->id) }}"><span class="glyphicon glyphicon-remove"></span> Delete</a>
        @endif
        @if ($item->isEditable())
            <a class="btn btn-xs btn-info" href="{{ act('vault', 'edit-screenshots', $item->id) }}"><span class="glyphicon glyphicon-picture"></span> Edit Screenshots</a>
            <a class="btn btn-xs btn-primary" href="{{ act('vault', 'edit', $item->id) }}"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
        @endif
    </h2>
    <div class="row">
        <div class="col-md-8">
            <div id="vault-slider" class="slider">
                <div class="loading" data-u="loading">
                    Loading...
                </div>
                <div class="slides" data-u="slides">
                    @foreach($item->vault_screenshots as $sshot)
                    <div>
                        <img data-u="image" data-src2="{{ asset('uploads/vault/'.$sshot->image_large) }}" alt="Screenshot" />
                        <img data-u="thumb" data-src2="{{ asset('uploads/vault/'.$sshot->image_thumb) }}" alt="Thumbnail" />
                    </div>
                    @endforeach
                </div>
                <div data-u="thumbnavigator" class="thumbs">
                    <div data-u="slides">
                        <div data-u="prototype" class="p">
                            <div data-u="thumbnailtemplate" class="i"></div>
                        </div>
                    </div>
                </div>
                <span data-u="arrowleft" class="arrow left" style="top: 123px; left: 8px;"></span>
                <span data-u="arrowright" class="arrow right" style="top: 123px; right: 8px;"></span>
            </div>
        </div>
        <div class="col-md-4">
            By {{ $item->user->name }}<br/>
            <img src="{{ asset('images/games/' . $item->game->abbreviation . '_32.png') }}" alt="{{ $item->game->name }}" /> {{ $item->game->name }}
            <ul>
                <li>Created: {{ Date::TimeAgo($item->created_at) }}</li>
                <li>Updated: {{ Date::TimeAgo($item->updated_at) }}</li>
                <li>{{ $item->stat_views}} views</li>
                <li>{{ $item->stat_downloads}} downloads</li>
                <li>{{ $item->stat_comments}} comments</li>
            </ul>
            <a href="#">Download</a><br/>
            @if ($item->file_size > 0)
                Size: {{ format_filesize($item->file_size) }}<br/>
            @endif
            @if (count($item->vault_includes) > 0)
                Download contains: {{ implode(', ', array_map(function($x) { return $x['name']; }, $item->vault_includes->toArray())) }}
            @endif
        </div>
    </div>
    <div class="bbcode">{!! $item->content_html !!}</div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function() {
            var slider = new $JssorSlider$("vault-slider", {
                $AutoPlay: true,
                $AutoPlayInterval: 4000,
                $SlideDuration: 250,
                $FillMode: 5,

                $ThumbnailNavigatorOptions: {
                    $Class: $JssorThumbnailNavigator$,
                    $ChanceToShow: 2,
                    $SpacingX: 8,
                    $DisplayPieces: 10,
                    $ParkingPosition: 360
                },

                $ArrowNavigatorOptions: {
                    $Class: $JssorArrowNavigator$,
                    $AutoCenter: 2
                }
            });
        });
    </script>
@endsection