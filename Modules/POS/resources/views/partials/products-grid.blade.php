{{-- Products Grid: 2/3 of screen --}}
<div class="pos-product-grid" style="flex: 2; overflow-y: auto; padding: 1.5rem; background: #f5f5f5; border-left: 1px solid #e0e0e0;">
    <div class="row g-3" id="productsGrid">
        @foreach($items as $item)
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="product-card card h-100" 
                     data-item-id="{{ $item->id }}"
                     style="border: none; border-radius: 15px; overflow: hidden;">
                    <div class="product-image" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                        <i class="fas {{ $item->is_weight_scale ? 'fa-weight' : 'fa-box' }} fa-4x text-white opacity-50"></i>
                        @if($item->is_weight_scale)
                            <span class="badge bg-warning position-absolute top-0 start-0 m-2">
                                <i class="fas fa-weight"></i> ميزان
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-3">
                        <h6 class="card-title mb-2" style="font-size: 0.95rem; font-weight: 600; color: #333;">
                            {{ $item->name }}
                        </h6>
                        <div class="product-footer" style="height: 4px; background: #FFD700; border-radius: 2px;"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
