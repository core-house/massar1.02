<div class="row mb-3">
    <div class="col-sm-12">
        @if (isset($title))
            <h4 class="mb-2">{{ is_array($title) ? implode(', ', $title) : $title }}</h4>
        @endif
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                @foreach ($breadcrumb_items ?? [] as $breadcrumb_item)
                    @php
                        $breadcrumb_label = is_array($breadcrumb_item['label'] ?? '') ? implode(', ', $breadcrumb_item['label']) : ($breadcrumb_item['label'] ?? '');
                    @endphp
                    <li class="breadcrumb-item {{ !isset($breadcrumb_item['url']) ? 'active' : '' }}">
                        @if (isset($breadcrumb_item['url']))
                            <a href="{{ $breadcrumb_item['url'] }}">{{ $breadcrumb_label }}</a>
                        @else
                            {{ $breadcrumb_label }}
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>
</div>
