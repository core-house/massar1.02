@props(['template', 'section', 'content' => null])

@if($template && $template->hasSectionEnabled($section))
    <div class="print-section print-section-{{ $section }}">
        {{ $content ?? $slot }}
    </div>
@endif
