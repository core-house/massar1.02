<div class="row">
    <div class="col-sm-12">
                        @foreach ($items ?? [] as $item)
                                @if (isset($item['url']))
                                 /   <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                                @else
                                   / {{ $item['label'] }}
                                @endif
                        @endforeach
                </div>
            </div>
