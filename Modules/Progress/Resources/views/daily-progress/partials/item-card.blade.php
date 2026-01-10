<div class="card border border-light shadow-sm bg-white item-card h-100">
    <div class="card-body py-3 px-3">
        <div class="row align-items-center">
            
            <!-- Work Item Info -->
            <div class="col-md-6 border-end-md"> <!-- Use border-end-md to show border only on medium+ screens -->
                <div class="d-flex flex-column">
                    <span class="fw-bold text-dark fs-6 mb-1" x-text="item.work_item.name"></span>
                    
                    <div class="d-flex align-items-center text-muted small mb-2">
                        <i class="fas fa-folder me-1 text-warning"></i> 
                        <span x-text="item.work_item.category" class="me-3"></span>
                        
                        <i class="fas fa-ruler-combined me-1 text-info"></i> 
                        <span x-text="item.work_item.unit"></span>
                    </div>

                    <div class="d-flex gap-2">
                         <!-- Measurable Badge -->
                        <template x-if="item.is_measurable">
                            <span class="badge bg-success rounded-pill" style="font-size: 0.7rem;">
                                <i class="fas fa-check me-1"></i> {{ __('general.measurable') }}
                            </span>
                        </template>
                         <template x-if="!item.is_measurable">
                            <span class="badge bg-secondary rounded-pill" style="font-size: 0.7rem;">
                                <i class="fas fa-minus me-1"></i> {{ __('general.not_measurable') }}
                            </span>
                        </template>

                        <!-- Daily Expected -->
                        <template x-if="item.daily_quantity > 0">
                             <span class="badge bg-info text-white rounded-pill" style="font-size: 0.7rem;">
                                {{ __('general.expected_daily') }}: <span x-text="item.daily_quantity"></span>/{{ __('general.day') }}
                            </span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Subproject Info -->
            <div class="col-md-3 text-center border-end-md py-2 py-md-0">
                 <template x-if="item.subproject_name">
                    <div class="fw-bold text-dark">
                        <i class="fas fa-cube me-1 text-muted"></i>
                        <span x-text="item.subproject_name"></span>
                    </div>
                 </template>
                 <template x-if="!item.subproject_name">
                     <span class="text-muted small">-</span>
                 </template>
            </div>

            <!-- Quantity Input -->
            <div class="col-md-3 px-md-4" x-data="{ localQty: '' }">
                <div class="input-group input-group-lg">
                    <input type="number" 
                           :name="'quantities[' + item.id + ']'" 
                           x-model="localQty"
                           class="form-control text-center fw-bold text-primary quantity-input border-primary"
                           placeholder="0" 
                           step="0.01" min="0"
                           @keydown.enter.prevent="focusNext($el)">
                </div>
                 <template x-if="item.total_quantity > 0 && Number(localQty) > (item.total_quantity - item.completed_quantity)">
                    <small class="text-danger fw-bold d-block mt-1 animate__animated animate__fadeIn">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <span x-text="'{{ __('general.quantity_exceeds_remaining', ['remaining' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', parseFloat((item.total_quantity - item.completed_quantity).toFixed(2)))"></span>
                    </small>
                </template>
            </div>

        </div>
    </div>
</div>
