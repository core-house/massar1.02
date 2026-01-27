<div class="modal fade" id="renewModal{{ $sub->id }}" tabindex="-1" aria-labelledby="renewModalLabel{{ $sub->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renewModalLabel{{ $sub->id }}">
                    <i class="fas fa-sync me-2"></i>{{ __('Renew Subscription') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <p>{{ __('Choose renewal option for') }} <strong>{{ $sub->plan->name ?? $sub->plan_id }}</strong>:</p>
                
                <div class="d-grid gap-3">
                    <!-- Option 1: Same Duration & Amount -->
                    <form action="{{ route('subscriptions.renew', $sub->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-history me-2"></i>
                            {{ __('Renew with Same Duration & Amount') }}
                            <span class="d-block small opacity-75">({{ number_format((float) $sub->paid_amount, 2) }})</span>
                        </button>
                    </form>

                    <hr class="my-2">

                    <!-- Option 2: Custom Amount -->
                    <form action="{{ route('subscriptions.renew-with-amount', $sub->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="paid_amount" class="form-label">{{ __('New Paid Amount') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input type="number" step="0.01" name="paid_amount" class="form-control" 
                                       placeholder="{{ __('0.00') }}" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-plus-circle me-2"></i>
                            {{ __('Renew with Custom Amount') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </div>
</div>
