@can('view quality')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('quality.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>{{ trans_str('quality dashboard') }}</span>
        </a>
    </li>
@endcan

@canany(['view inspections', 'create inspections'])
    <li class="nav-item">
        <a class="nav-link collapsed" href="#quality-inspections" data-bs-toggle="collapse" role="button" aria-expanded="false"
            aria-controls="quality-inspections">
            <i class="fas fa-clipboard-check"></i>
            <span>{{ trans_str('quality inspections') }}</span>
            <i class="ti-angle-down"></i>
        </a>
        <div class="collapse" id="quality-inspections">
            <ul class="nav flex-column sub-menu">
                @can('view inspections')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('quality.inspections.index') }}">
                            <i class="las la-list"></i>{{ trans_str('all inspections') }}
                        </a>
                    </li>
                @endcan
                @can('create inspections')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('quality.inspections.create') }}">
                            <i class="las la-plus-circle"></i>{{ trans_str('new inspection') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </li>
@endcanany

@can('view standards')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('quality.standards.index') }}">
            <i class="fas fa-ruler-combined"></i>
            <span>{{ trans_str('quality standards') }}</span>
        </a>
    </li>
@endcan

@canany(['view ncr', 'create ncr'])
    <li class="nav-item">
        <a class="nav-link collapsed" href="#quality-ncr" data-bs-toggle="collapse" role="button" aria-expanded="false"
            aria-controls="quality-ncr">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ trans_str('non-conformance (ncr)') }}</span>
            <i class="ti-angle-down"></i>
        </a>
        <div class="collapse" id="quality-ncr">
            <ul class="nav flex-column sub-menu">
                @can('view ncr')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('quality.ncr.index') }}">
                            <i class="las la-list"></i>{{ trans_str('all reports') }}
                        </a>
                    </li>
                @endcan
                @can('create ncr')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('quality.ncr.create') }}">
                            <i class="las la-plus-circle"></i>{{ trans_str('new report') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </li>
@endcanany

@can('view capa')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('quality.capa.index') }}">
            <i class="fas fa-tools"></i>
            <span>{{ trans_str('corrective actions (capa)') }}</span>
        </a>
    </li>
@endcan

@can('view batches')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('quality.batches.index') }}">
            <i class="fas fa-barcode"></i>
            <span>{{ trans_str('batch tracking') }}</span>
        </a>
    </li>
@endcan

@can('view rateSuppliers')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('quality.suppliers.index') }}">
            <i class="fas fa-star"></i>
            <span>{{ trans_str('supplier ratings') }}</span>
        </a>
    </li>
@endcan

@can('view certificates')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('quality.certificates.index') }}">
            <i class="fas fa-certificate"></i>
            <span>{{ trans_str('certificates & compliance') }}</span>
        </a>
    </li>
@endcan

@can('view audits')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('quality.audits.index') }}">
            <i class="fas fa-search"></i>
            <span>{{ trans_str('internal audit') }}</span>
        </a>
    </li>
@endcan
