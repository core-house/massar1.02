@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">
                            <i class="las la-file-alt text-primary"></i>
                            {{ __('manufacturing::manufacturing.manufacturing invoice templates') }}
                        </h3>
                        <p class="text-muted mb-0 small">{{ __('manufacturing::manufacturing.manage your manufacturing invoice templates') }}</p>
                    </div>
                    <div>
                        <a href="{{ route('manufacturing.templates.create') }}" class="btn btn-primary btn-sm">
                            <i class="las la-plus-circle"></i>
                            {{ __('manufacturing::manufacturing.create new template') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="las la-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="las la-exclamation-circle"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search Filter -->
        @if(!$templates->isEmpty())
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="las la-search"></i>
                        </span>
                        <input type="text" 
                               id="template-search" 
                               class="form-control" 
                               placeholder="{{ __('manufacturing::manufacturing.search by template name...') }}"
                               autocomplete="off">
                    </div>
                </div>
            </div>
        @endif

        <!-- Templates Content -->
        <div class="row">
            <div class="col-12">
                @if($templates->isEmpty())
                    <!-- Empty State -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <div class="mb-3">
                                <i class="las la-file-alt" style="font-size: 4rem; color: #e0e0e0;"></i>
                            </div>
                            <h5 class="mb-2">{{ __('manufacturing::manufacturing.no templates found for manufacturing invoices') }}</h5>
                            <p class="text-muted mb-3 small">{{ __('manufacturing::manufacturing.you can create templates from the manufacturing invoice creation page') }}</p>
                            <a href="{{ route('manufacturing.templates.create') }}" class="btn btn-primary btn-sm">
                                <i class="las la-plus-circle"></i>
                                {{ __('manufacturing::manufacturing.create your first template') }}
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Templates Grid -->
                    <div class="row g-3" id="templates-container">
                        @foreach($templates as $template)
                            @php
                                // Use already loaded relationships
                                $allItems = $template->operationItems;
                                $products = $allItems->where('pro_tybe', 64);
                                $rawMaterials = $allItems->where('pro_tybe', 63);
                                $productsCount = $products->count();
                                $rawMaterialsCount = $rawMaterials->count();
                                $firstProduct = $products->first();
                            @endphp
                            <div class="col-md-6 col-lg-4 col-xl-3 template-card" data-template-name="{{ strtolower($template->info ?: '') }}">
                                <div class="card border-0 shadow-sm h-100 hover-shadow transition-all">
                                    <!-- Card Header -->
                                    <div class="card-header bg-gradient-primary text-white border-0 py-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1 pe-2">
                                                <h6 class="mb-0 text-white text-truncate" title="{{ $template->info ?: __('manufacturing::manufacturing.unnamed_template') }}">
                                                    <i class="las la-file-invoice"></i>
                                                    {{ Str::limit($template->info ?: __('manufacturing::manufacturing.unnamed_template'), 25) }}
                                                </h6>
                                                <small class="opacity-75">
                                                    #{{ $template->pro_id }}
                                                </small>
                                            </div>
                                            <form action="{{ route('manufacturing.templates.toggle-active', $template->id) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm {{ $template->is_manager ? 'btn-success' : 'btn-secondary' }} border-0 px-2 py-1"
                                                        title="{{ $template->is_manager ? __('manufacturing::manufacturing.active') : __('manufacturing::manufacturing.inactive') }}">
                                                    <i class="las {{ $template->is_manager ? 'la-check-circle' : 'la-times-circle' }} me-1"></i>
                                                    <span style="font-size: 0.75rem;">{{ $template->is_manager ? __('manufacturing::manufacturing.active') : __('manufacturing::manufacturing.inactive') }}</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Product Image -->
                                    @php
                                        $productImage = asset('images/no-image.svg');
                                        $productName = __('manufacturing::manufacturing.template');
                                        
                                        if ($firstProduct && $firstProduct->item) {
                                            $productName = $firstProduct->item->name;
                                            
                                            // Try to get image from media
                                            $media = $firstProduct->item->getMedia('item-thumbnail')->first();
                                            if ($media) {
                                                // Use asset() helper for proper URL generation
                                                $productImage = asset('storage/' . $media->id . '/' . $media->file_name);
                                            }
                                        }
                                    @endphp
                                    <div class="position-relative" style="height: 180px; overflow: hidden; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                                        <img src="{{ $productImage }}" 
                                             alt="{{ $productName }}"
                                             class="w-100 h-100"
                                             style="object-fit: contain; padding: 10px;"
                                             onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                        
                                        @if($firstProduct && $firstProduct->item)
                                            <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white px-2 py-1">
                                                <small class="d-block text-truncate" style="font-size: 0.7rem;">
                                                    <i class="las la-box"></i>
                                                    {{ $productName }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body p-3">
                                        <!-- Template Info -->
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">
                                                    <i class="las la-calendar"></i>
                                                    {{ __('manufacturing::manufacturing.date') }}
                                                </small>
                                                <small class="fw-bold">{{ \Carbon\Carbon::parse($template->pro_date)->format('Y-m-d') }}</small>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">
                                                    <i class="las la-money-bill-wave"></i>
                                                    {{ __('manufacturing::manufacturing.cost') }}
                                                </small>
                                                <span class="badge bg-success">{{ number_format($template->pro_value, 0) }}</span>
                                            </div>
                                            @if($template->expected_time)
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="las la-clock"></i>
                                                        {{ __('manufacturing::manufacturing.time') }}
                                                    </small>
                                                    <span class="badge bg-info">{{ $template->expected_time }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Items Count -->
                                        <div class="border-top pt-2 mb-2">
                                            <div class="row g-2 text-center">
                                                <div class="col-6">
                                                    <div class="bg-light rounded p-2">
                                                        <div class="text-primary fw-bold">{{ $productsCount }}</div>
                                                        <small class="text-muted">{{ __('manufacturing::manufacturing.products') }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="bg-light rounded p-2">
                                                        <div class="text-success fw-bold">{{ $rawMaterialsCount }}</div>
                                                        <small class="text-muted">{{ __('manufacturing::manufacturing.raw materials') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Items Preview -->
                                        @if($productsCount > 0)
                                            <div class="mb-2">
                                                <small class="text-muted d-block mb-1">
                                                    <i class="las la-box text-primary"></i>
                                                    {{ __('manufacturing::manufacturing.products') }}:
                                                </small>
                                                @foreach($products->take(2) as $product)
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge bg-primary me-1" style="font-size: 0.65rem;">{{ $loop->iteration }}</span>
                                                        <small class="text-truncate" style="font-size: 0.75rem;">{{ $product->item->name ?? '-' }}</small>
                                                    </div>
                                                @endforeach
                                                @if($productsCount > 2)
                                                    <small class="text-muted" style="font-size: 0.7rem;">+{{ $productsCount - 2 }} {{ __('manufacturing::manufacturing.more') }}</small>
                                                @endif
                                            </div>
                                        @endif

                                        @if($rawMaterialsCount > 0)
                                            <div class="mb-2">
                                                <small class="text-muted d-block mb-1">
                                                    <i class="las la-tools text-success"></i>
                                                    {{ __('manufacturing::manufacturing.raw materials') }}:
                                                </small>
                                                @foreach($rawMaterials->take(2) as $material)
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge bg-success me-1" style="font-size: 0.65rem;">{{ $loop->iteration }}</span>
                                                        <small class="text-truncate" style="font-size: 0.75rem;">{{ $material->item->name ?? '-' }}</small>
                                                    </div>
                                                @endforeach
                                                @if($rawMaterialsCount > 2)
                                                    <small class="text-muted" style="font-size: 0.7rem;">+{{ $rawMaterialsCount - 2 }} {{ __('manufacturing::manufacturing.more') }}</small>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="card-footer bg-light border-0 p-2">
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('manufacturing.create', ['template_id' => $template->id]) }}" 
                                               class="btn btn-sm btn-success flex-fill py-1"
                                               title="{{ __('manufacturing::manufacturing.load in new invoice') }}">
                                                <i class="las la-plus-circle"></i>
                                                <span class="d-none d-lg-inline">{{ __('manufacturing::manufacturing.load') }}</span>
                                            </a>
                                            <a href="{{ route('manufacturing.templates.edit', $template->id) }}" 
                                               class="btn btn-sm btn-outline-primary py-1"
                                               style="width: 40px;"
                                               title="{{ __('manufacturing::manufacturing.edit') }}">
                                                <i class="las la-edit"></i>
                                            </a>
                                            <form action="{{ route('manufacturing.templates.destroy', $template->id) }}" 
                                                  method="POST" 
                                                  style="width: 40px;"
                                                  onsubmit="return confirm('{{ __('manufacturing::manufacturing.are you sure you want to delete this template?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger w-100 py-1"
                                                        title="{{ __('manufacturing::manufacturing.delete') }}">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $templates->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .hover-shadow {
            transition: all 0.3s ease;
        }
        
        .hover-shadow:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        }
        
        .transition-all {
            transition: all 0.3s ease;
        }

        .template-card.hidden {
            display: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('template-search');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const cards = document.querySelectorAll('.template-card');
                    
                    cards.forEach(card => {
                        const templateName = card.dataset.templateName || '';
                        if (templateName.includes(searchTerm)) {
                            card.classList.remove('hidden');
                        } else {
                            card.classList.add('hidden');
                        }
                    });
                });
            }
        });
    </script>
@endsection
