{{-- Products Grid: 2/3 of screen --}}
<div class="pos-product-grid" style="flex: 2; overflow-y: auto; padding: 1rem; background: #f5f5f5; border-left: 1px solid #e0e0e0;">
    <div class="row g-2" id="productsGrid">
        @foreach($items as $item)
            @php
                $imageUrl = $item->getFirstMediaUrl('item-images', 'thumb')
                    ?: $item->getFirstMediaUrl('item-thumbnail', 'thumb');
            @endphp
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <div class="product-card card h-100" 
                     data-item-id="{{ $item->id }}"
                     style="border: none; border-radius: 10px; overflow: hidden; cursor: pointer;">
                    <div class="product-image" style="height: 100px; background: linear-gradient(135deg, #e0e0e0 0%, #9e9e9e 100%); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $item->name }}"
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 loading="lazy"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center;">
                                <i class="fas {{ $item->is_weight_scale ? 'fa-weight' : 'fa-box' }} fa-2x text-secondary"></i>
                            </div>
                        @else
                            <i class="fas {{ $item->is_weight_scale ? 'fa-weight' : 'fa-box' }} fa-2x text-secondary"></i>
                        @endif
                        @if($item->is_weight_scale)
                            <span class="badge bg-warning position-absolute top-0 start-0 m-1" style="font-size: 0.65rem;">
                                <i class="fas fa-weight"></i> {{ __('pos.weight_scale') }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="card-title mb-0" style="font-size: 0.8rem; font-weight: 600; color: #333; flex: 1; line-height: 1.2;">
                                {{ $item->name }}
                            </h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold" style="font-size: 0.85rem;">
                            {{ number_format($item->sale_price ?? 0, 2) }}
                            </span>
                            <button class="btn btn-sm btn-outline-primary product-details-btn" 
                                    data-item-id="{{ $item->id }}"
                                    style="font-size: 0.7rem; padding: 0.15rem 0.4rem; border-radius: 5px;"
                                    title="{{ __('common.details') }}">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
