{{-- Category Filter Bar --}}
<div class="pos-category-bar bg-white shadow-sm" style="padding: 0.75rem 1rem; border-bottom: 1px solid #e0e0e0;">
    <div class="d-flex gap-2">
        <button type="button" 
                class="btn category-btn active"
                data-category=""
                style="border-radius: 20px; padding: 0.5rem 1.5rem; border: 2px solid #e0e0e0; background: white; color: #333;">
            الكل
        </button>
        @foreach($categories as $category)
            <button type="button" 
                    class="btn category-btn"
                    data-category="{{ $category->id }}"
                    style="border-radius: 20px; padding: 0.5rem 1.5rem; border: 2px solid #e0e0e0; background: white; color: #333;">
                {{ $category->name }}
            </button>
        @endforeach
    </div>
</div>
