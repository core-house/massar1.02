<?php

use Livewire\Volt\Component;
use App\Models\Varibal;

new class extends Component {
    public $varibals;

    public function mount()
    {
        $this->varibals = Varibal::orderBy('id', 'asc')->pluck('name', 'id');
    }
}; ?>

<div>
    @foreach ($varibals as $varibalId => $name)
        @can('view varibalsValues')
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('varibalValues.index') && request()->route('varibal') == $varibalId ? 'active' : '' }}" 
                   href="{{ route('varibalValues.index', $varibalId) }}"
                   style="{{ request()->routeIs('varibalValues.index') && request()->route('varibal') == $varibalId ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
                    <i class="las la-list font-18"></i>{{ $name }}
                </a>
            </li>
        @endcan
    @endforeach
</div>
