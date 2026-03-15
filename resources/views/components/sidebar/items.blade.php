<li class="menu-title mt-2">{{ __('navigation.item_management') }}</li>

@can('view item-statistics')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('items.statistics') ? 'active' : '' }}" 
           href="{{ route('items.statistics') }}"
           style="{{ request()->routeIs('items.statistics') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-chart-pie font-18"></i>{{ __('navigation.statistics') }}
        </a>
    </li>
@endcan

@can('view units')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('units.index') ? 'active' : '' }}" 
           href="{{ route('units.index') }}"
           style="{{ request()->routeIs('units.index') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-balance-scale font-18"></i>{{ __('navigation.units') }}
        </a>
    </li>
@endcan

@can('view items')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('items.index') ? 'active' : '' }}" 
           href="{{ route('items.index') }}"
           style="{{ request()->routeIs('items.index') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-boxes font-18"></i>{{ __('navigation.items') }}
        </a>
    </li>
@endcan

@can('edit items')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('items.manage-prices') ? 'active' : '' }}" 
           href="{{ route('items.manage-prices') }}"
           style="{{ request()->routeIs('items.manage-prices') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-tags font-18"></i>{{ __('items.manage_prices_and_groups') }}
        </a>
    </li>
@endcan

@can('view prices')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('prices.index') ? 'active' : '' }}" 
           href="{{ route('prices.index') }}"
           style="{{ request()->routeIs('prices.index') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-tags font-18"></i>{{ __('navigation.prices') }}
        </a>
    </li>
@endcan

@can('view varibals')
        @php
            $allNotes = \App\Models\Note::with(['noteDetails' => function($query) {
                $query->orderBy('id', 'asc');
            }])->orderBy('id', 'asc')->get();
        @endphp
        
        @foreach($allNotes as $note)
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('notes.noteDetails') && request()->route('id') == $note->id ? 'active' : '' }}" 
                   href="{{ route('notes.noteDetails', $note->id) }}"
                   style="{{ request()->routeIs('notes.noteDetails') && request()->route('id') == $note->id ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
                    <i class="las la-{{ $note->id == 1 ? 'folder' : ($note->id == 2 ? 'tag' : 'map-marker') }} font-18"></i>{{ $note->name }}
                </a>
            </li>
        @endforeach
    
    
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('varibals.index') ? 'active' : '' }}" 
           href="{{ route('varibals.index') }}"
           style="{{ request()->routeIs('varibals.index') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-cog font-18"></i>{{ __('navigation.varibals') }}
        </a>
    </li>
    <livewire:item-management.varibals.varibalslinks />
@endcan


@if(request()->routeIs('items.create') || request()->routeIs('items.edit'))
    @push('scripts')
    <script>
        function scrollToNote(noteId, detailId) {
            // البحث عن select element للـ note المحدد
            const noteSelect = document.getElementById('note-' + noteId);
            if (noteSelect) {
                // البحث عن الـ option المطابق
                const options = noteSelect.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].getAttribute('data-detail-id') == detailId) {
                        // تحديد العنصر في الـ select
                        noteSelect.value = options[i].value;
                        // إطلاق حدث التغيير لـ Livewire
                        noteSelect.dispatchEvent(new Event('change', { bubbles: true }));
                        
                        // Scroll إلى العنصر
                        const container = noteSelect.closest('.col-md-2');
                        if (container) {
                            container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            // إضافة تأثير بصري مؤقت
                            container.classList.add('highlight-group');
                            setTimeout(() => {
                                container.classList.remove('highlight-group');
                            }, 2000);
                        }
                        break;
                    }
                }
            }
        }
    </script>
    <style>
        .highlight-group {
            animation: highlightPulse 2s ease-in-out;
        }
        @keyframes highlightPulse {
            0%, 100% { background-color: transparent; }
            50% { background-color: rgba(52, 211, 163, 0.2); }
        }
    </style>
    @endpush
@endif
