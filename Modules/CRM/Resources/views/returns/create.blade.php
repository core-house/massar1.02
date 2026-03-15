@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.returns'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.returns'), 'url' => route('returns.index')],
            ['label' => __('crm::crm.create')],
        ],
    ])
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('crm::crm.add_new_return') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('returns.store') }}" method="POST" id="returnForm" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Client -->
                            <div class="col-md-6 mb-3">
                                <x-dynamic-search name="client_id" :label="__('crm::crm.client')" column="cname"
                                    model="App\Models\Client" :placeholder="__('crm::crm.search_for_client')" :required="true" :class="'form-select'" />
                            </div>

                            <!-- Return Type -->
                            <div class="mb-3 col-lg-3">
                                <label for="return_type" class="form-label">{{ __('crm::crm.return_type') }}</label>
                                <select name="return_type" id="return_type" class="form-control" required>
                                    <option value="refund" {{ old('return_type') == 'refund' ? 'selected' : '' }}>
                                        {{ __('crm::crm.refund') }}</option>
                                    <option value="exchange" {{ old('return_type') == 'exchange' ? 'selected' : '' }}>
                                        {{ __('crm::crm.exchange') }}</option>
                                    <option value="credit_note" {{ old('return_type') == 'credit_note' ? 'selected' : '' }}>
                                        {{ __('crm::crm.credit_note') }}</option>
                                </select>
                                @error('return_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Return Date -->
                            <div class="mb-3 col-lg-3">
                                <label for="return_date" class="form-label">{{ __('crm::crm.return_date') }}</label>
                                <input type="date" name="return_date" id="return_date" class="form-control"
                                    value="{{ old('return_date', date('Y-m-d')) }}" required>
                                @error('return_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Original Invoice Number -->
                            <div class="mb-3 col-lg-6">
                                <label for="original_invoice_number"
                                    class="form-label">{{ __('crm::crm.original_invoice_number') }}</label>
                                <input type="text" name="original_invoice_number" id="original_invoice_number"
                                    class="form-control" value="{{ old('original_invoice_number') }}">
                                @error('original_invoice_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Original Invoice Date -->
                            <div class="mb-3 col-lg-6">
                                <label for="original_invoice_date"
                                    class="form-label">{{ __('crm::crm.original_invoice_date') }}</label>
                                <input type="date" name="original_invoice_date" id="original_invoice_date"
                                    class="form-control" value="{{ old('original_invoice_date') }}">
                                @error('original_invoice_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Reason -->
                            <div class="mb-3 col-lg-12">
                                <label for="reason" class="form-label">{{ __('crm::crm.reason_from_client') }}</label>
                                <textarea name="reason" id="reason" class="form-control" rows="2">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="mb-3 col-lg-6">
                                <label for="notes" class="form-label">{{ __('crm::crm.internal_notes') }}</label>
                                <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Attachment -->
                            <div class="mb-3 col-lg-3">
                                <label for="attachment" class="form-label">{{ __('crm::crm.attachments') }} (PDF)</label>
                                <input type="file" name="attachment" id="attachment" class="form-control" accept=".pdf">
                                <small class="text-muted">{{ __('PDF only (Max: 5MB)') }}</small>
                                @error('attachment')
                                    <small class="text-danger d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Multiple Images -->
                            <div class="mb-3 col-lg-6">
                                <label for="images" class="form-label">{{ __('crm::crm.return_images') }}</label>
                                <input type="file" name="images[]" id="images" class="form-control" 
                                       accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" 
                                       multiple
                                       onchange="previewImages(this)">
                                <small class="text-muted">{{ __('JPG, PNG, GIF, WEBP (Max: 5 images, 5MB each)') }}</small>
                                @error('images.*')
                                    <small class="text-danger d-block">{{ $message }}</small>
                                @enderror
                                
                                <!-- Image Previews -->
                                <div id="imagePreviews" class="row g-2 mt-2" style="display: none;"></div>
                            </div>

                            <div class="mb-3 col-lg-3">
                                <x-branches::branch-select :branches="$branches" />
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('crm::crm.return_items') }}</h5>
                            </div>
                            <div class="card-body">
                                <button type="button" class="btn btn-sm btn-success mb-3" id="addItemBtn">
                                    <i class="fas fa-plus"></i> {{ __('crm::crm.add_item') }}
                                </button>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('crm::crm.item') }}</th>
                                                <th>{{ __('crm::crm.quantity') }}</th>
                                                <th>{{ __('crm::crm.unit_price') }}</th>
                                                <th>{{ __('crm::crm.total') }}</th>
                                                <th>{{ __('crm::crm.condition') }}</th>
                                                <th>{{ __('crm::crm.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Save Buttons -->
                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2">
                                <i class="las la-save"></i> {{ __('crm::crm.save') }}
                            </button>
                            <a href="{{ route('returns.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('crm::crm.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemIndex = 0;
        const items = @json($items);

        document.getElementById('addItemBtn').addEventListener('click', function() {
            const tbody = document.getElementById('itemsBody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select name="items[${itemIndex}][item_id]" class="form-control" required>
                        <option value="">{{ __('crm::crm.select_item') }}</option>
                        ${items.map(item => `<option value="${item.id}">${item.name}</option>`).join('')}
                    </select>
                </td>
                <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="1" required></td>
                <td><input type="number" name="items[${itemIndex}][unit_price]" class="form-control" step="0.01" min="0" required></td>
                <td><input type="number" class="form-control total-price" readonly></td>
                <td><input type="text" name="items[${itemIndex}][item_condition]" class="form-control"></td>
                <td><button type="button" class="btn btn-danger btn-sm removeItemBtn"><i class="las la-trash"></i></button></td>
            `;
            tbody.appendChild(row);
            itemIndex++;

            // Calculate total on quantity/price change
            const qtyInput = row.querySelector('input[name*="[quantity]"]');
            const priceInput = row.querySelector('input[name*="[unit_price]"]');
            const totalInput = row.querySelector('.total-price');

            function calculateTotal() {
                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                totalInput.value = (qty * price).toFixed(2);
            }

            qtyInput.addEventListener('input', calculateTotal);
            priceInput.addEventListener('input', calculateTotal);

            // Remove row
            row.querySelector('.removeItemBtn').addEventListener('click', function() {
                row.remove();
            });
        });

        // Add first row automatically
        document.getElementById('addItemBtn').click();

        // Image preview function
        function previewImages(input) {
            const previewContainer = document.getElementById('imagePreviews');
            previewContainer.innerHTML = '';
            
            if (input.files && input.files.length > 0) {
                previewContainer.style.display = 'flex';
                
                // Limit to 5 images
                const files = Array.from(input.files).slice(0, 5);
                
                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const col = document.createElement('div');
                            col.className = 'col-md-2 col-4';
                            col.innerHTML = `
                                <div class="position-relative">
                                    <img src="${e.target.result}" 
                                         class="img-thumbnail w-100" 
                                         style="height: 100px; object-fit: cover;">
                                    <span class="badge bg-primary position-absolute top-0 start-0 m-1">${index + 1}</span>
                                </div>
                            `;
                            previewContainer.appendChild(col);
                        };
                        
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                previewContainer.style.display = 'none';
            }
        }
    </script>
@endsection
