<div>
    <div class="form-group">
        @if ($label)
            <label class="form-label fw-bold">{{ $label }}</label>
        @endif

        <div class="searchable-select-container position-relative">
            <!-- حقل البحث -->
            <div class="input-group">
                <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                    placeholder="{{ $placeholder }}" autocomplete="off" wire:focus="$set('showDropdown', true)"
                    @blur="setTimeout(() => { $wire.showDropdown = false }, 200)">

                @if ($selectedId)
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary" wire:click="clearSelection"
                            title="{{ __('Clear') }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
            </div>

            <!-- قائمة نتائج البحث -->
            @if ($showDropdown && $search && !$selectedId)
                <div class="dropdown-menu show w-100" style="max-height: 300px; overflow-y: auto;">
                    @if (count($filteredItems) > 0)
                        <!-- العناصر الموجودة -->
                        @foreach ($filteredItems as $item)
                            <button type="button" class="dropdown-item d-flex align-items-start py-2"
                                wire:click="selectItem({{ $item['id'] }}, '{{ addslashes($item['text']) }}')">
                                <div class="me-2 mt-1">
                                    @if (isset($item['raw']->type))
                                        @if ($item['raw']->type === 'company')
                                            <i class="fas fa-building text-primary"></i>
                                        @else
                                            <i class="fas fa-user text-secondary"></i>
                                        @endif
                                    @else
                                        <i class="fas fa-check-circle text-muted"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $item['text'] }}</div>
                                    @if (isset($item['raw']))
                                        <div class="small text-muted">
                                            @if (isset($item['raw']->phone_1) && $item['raw']->phone_1)
                                                <span>📞 {{ $item['raw']->phone_1 }}</span>
                                            @endif
                                            @if (isset($item['raw']->email) && $item['raw']->email)
                                                <span class="ms-2">✉️ {{ $item['raw']->email }}</span>
                                            @endif
                                        </div>
                                        @if (isset($item['raw']->roles) && $item['raw']->roles->count() > 0)
                                            <div class="mt-1">
                                                @foreach ($item['raw']->roles as $role)
                                                    <span class="badge bg-info text-dark me-1"
                                                        style="font-size: 0.7rem;">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    @endif

                    <!-- زر إنشاء عنصر جديد -->
                    <!-- زر إنشاء عنصر جديد -->
                    @if ($allowCreate && $search && !collect($filteredItems)->contains('text', $search))
                        <button type="button" class="dropdown-item d-flex align-items-center border-top text-success"
                            wire:click="createNew">
                            <i class="fas fa-plus-circle me-2"></i>
                            <span>{{ __('Create new') }}: "<strong>{{ $search }}</strong>"</span>
                        </button>
                    @endif

                    <!-- رسالة عدم وجود نتائج -->
                    @if (count($filteredItems) === 0 && !collect($filteredItems)->contains('text', $search))
                        <div class="dropdown-item text-muted">
                            <i class="fas fa-search me-2"></i>
                            <span>{{ __('No results found') }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- مؤشر العنصر المحدد -->
            @if ($selectedId && $selectedText)
                <small class="text-success mt-1 d-block">
                    <i class="fas fa-check-circle"></i>
                    {{ __('Selected') }}: {{ $selectedText }}
                </small>
            @endif
        </div>
    </div>

    <style>
        .searchable-select-container .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .searchable-select-container .dropdown-item {
            cursor: pointer;
            padding: 0.75rem 1rem;
            transition: background-color 0.2s;
        }

        .searchable-select-container .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .searchable-select-container .badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
        }
    </style>
</div>
