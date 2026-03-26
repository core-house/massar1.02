<div>
    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="save">
                <!-- معلومات الحملة الأساسية -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3">{{ __('crm::crm.campaign_information') }}</h5>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('crm::crm.campaign_title') }} <span class="text-danger">*</span></label>
                        <input type="text" wire:model="title"
                            class="form-control @error('title') is-invalid @enderror"
                            placeholder="{{ __('crm::crm.enter_campaign_title') }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('crm::crm.email_subject') }} <span class="text-danger">*</span></label>
                        <input type="text" wire:model="subject"
                            class="form-control @error('subject') is-invalid @enderror"
                            placeholder="{{ __('crm::crm.enter_email_subject') }}">
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            {{ __('crm::crm.you_can_use_variables_like') }}: {اسم_العميل}, {العنوان}, {البريد}, {الهاتف},
                            {الشركة}
                        </small>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">{{ __('crm::crm.campaign_message') }} <span
                                class="text-danger">*</span></label>
                        <textarea wire:model="message" rows="6" class="form-control @error('message') is-invalid @enderror"
                            placeholder="{{ __('crm::crm.enter_campaign_message') }}"></textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            {{ __('crm::crm.you_can_use_variables_like') }}: {اسم_العميل}, {العنوان}, {الهاتف}, {البريد},
                            {الشركة}
                        </small>
                    </div>
                </div>

                <hr>

                <!-- فلاتر الاستهداف -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3">{{ __('crm::crm.target_customers') }}</h5>
                        <p class="text-muted">{{ __('crm::crm.select_filters_to_target_specific_customers') }}</p>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('crm::crm.address') }}</label>
                        <input type="text" wire:model="address" class="form-control"
                            placeholder="{{ __('crm::crm.enter_address') }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('crm::crm.client_type') }}</label>
                        <select wire:model="clientTypeId" class="form-select">
                            <option value="">{{ __('crm::crm.all_types') }}</option>
                            @foreach ($clientTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('crm::crm.client_category') }}</label>
                        <select wire:model="clientCategoryId" class="form-select">
                            <option value="">{{ __('crm::crm.all_categories') }}</option>
                            @foreach ($clientCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('crm::crm.last_purchase_days') }}</label>
                        <select wire:model="lastPurchaseDays" class="form-select">
                            <option value="">{{ __('crm::crm.any_time') }}</option>
                            <option value="30">{{ __('crm::crm.last_30_days') }}</option>
                            <option value="60">{{ __('crm::crm.last_60_days') }}</option>
                            <option value="90">{{ __('crm::crm.last_90_days') }}</option>
                            <option value="180">{{ __('crm::crm.last_6_months') }}</option>
                            <option value="365">{{ __('crm::crm.last_year') }}</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('crm::crm.minimum_total_purchases') }}</label>
                        <input type="number" wire:model="totalPurchasesMin" class="form-control" step="0.01"
                            placeholder="{{ __('crm::crm.enter_minimum_amount') }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('crm::crm.client_status') }}</label>
                        <select wire:model="isActive" class="form-select">
                            <option value="">{{ __('crm::crm.all_clients') }}</option>
                            <option value="1">{{ __('crm::crm.active_only') }}</option>
                            <option value="0">{{ __('crm::crm.inactive_only') }}</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button type="button" wire:click="previewCustomers" class="btn btn-outline-primary">
                            <i class="las la-eye me-2"></i>
                            {{ __('crm::crm.preview_target_customers') }}
                        </button>
                    </div>
                </div>

                <hr>

                <!-- أزرار الحفظ -->
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="las la-save me-2"></i>
                                {{ $isEdit ? __('crm::crm.update_campaign') : __('crm::crm.save_as_draft') }}
                            </span>
                            <span wire:loading>
                                <i class="las la-spinner la-spin me-2"></i>
                                {{ __('crm::crm.saving') }}
                            </span>
                        </button>

                        <a href="{{ route('campaigns.index') }}" class="btn btn-secondary">
                            <i class="las la-times me-2"></i>
                            {{ __('crm::crm.cancel') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal معاينة العملاء -->
    @if ($showPreview)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('crm::crm.target_customers_preview') }}</h5>
                        <button type="button" class="btn-close" wire:click="closePreview"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="las la-info-circle me-2"></i>
                            {{ __('crm::crm.total_customers_matching') }}: <strong>{{ $previewTotal }}</strong>
                        </div>

                        @if (count($previewClients) > 0)
                            <h6>{{ __('crm::crm.first_10_customers') }}:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('crm::crm.name') }}</th>
                                            <th>{{ __('crm::crm.email') }}</th>
                                            <th>{{ __('crm::crm.address') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($previewClients as $client)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $client['name'] }}</td>
                                                <td>{{ $client['email'] ?? '-' }}</td>
                                                <td>{{ $client['address'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                {{ __('crm::crm.no_customers_found') }}
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closePreview">
                            {{ __('crm::crm.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
