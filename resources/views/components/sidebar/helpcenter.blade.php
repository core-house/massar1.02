<li class="nav-item">
    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
       href="{{ route('helpcenter.index') }}">
        <i class="las la-book" style="color:#34d3a3;"></i>
        {{ __('helpcenter::helpcenter.title') }}
    </a>
</li>

@can('manage helpcenter')
<li class="nav-item">
    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
       href="{{ route('helpcenter.admin.articles') }}">
        <i class="las la-edit" style="color:#34d3a3;"></i>
        {{ __('helpcenter::helpcenter.manage') }}
    </a>
</li>
@endcan
