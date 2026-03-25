<div class="container-fluid p-3 p-md-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title font-hold fw-bold mb-0">{{ __('edit_project') }}</h3>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <label for="name" class="form-label font-hold fw-bold">{{ __('project_name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name"
                            id="name" placeholder="{{ __('enter_project_name') }}">
                        @error('name')
                            <div class="invalid-feedback font-hold">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                        <label for="start_date" class="form-label font-hold fw-bold">{{ __('start_date') }}</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                            wire:model="start_date" id="start_date">
                        @error('start_date')
                            <div class="invalid-feedback font-hold">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                        <label for="end_date" class="form-label font-hold fw-bold">{{ __('expected_end_date') }}</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                            wire:model="end_date" id="end_date">
                        @error('end_date')
                            <div class="invalid-feedback font-hold">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                        <label for="actual_end_date" class="form-label font-hold fw-bold">{{ __('actual_end_date') }}</label>
                        <input type="date" class="form-control @error('actual_end_date') is-invalid @enderror"
                            wire:model="actual_end_date" id="actual_end_date">
                        @error('actual_end_date')
                            <div class="invalid-feedback font-hold">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                        <label for="status" class="form-label font-hold fw-bold">{{ __('project_status') }}</label>
                        <select class="form-select font-hold fw-bold @error('status') is-invalid @enderror"
                            wire:model="status" id="status">
                            <option value="pending">{{ __('status_pending') }}</option>
                            <option value="in_progress">{{ __('status_in_progress') }}</option>
                            <option value="completed">{{ __('status_completed') }}</option>
                            <option value="cancelled">{{ __('status_cancelled') }}</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback font-hold">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                        <label for="priority" class="form-label font-hold fw-bold">{{ __('priority') }}</label>
                        <select class="form-select font-hold fw-bold @error('priority') is-invalid @enderror"
                            wire:model="priority" id="priority">
                            <option value="low">{{ __('priority_low') }}</option>
                            <option value="medium">{{ __('priority_medium') }}</option>
                            <option value="high">{{ __('priority_high') }}</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback font-hold">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                        <label for="budget" class="form-label font-hold fw-bold">{{ __('budget') }}</label>
                        <input type="number" step="0.01" class="form-control @error('budget') is-invalid @enderror"
                            wire:model="budget" id="budget" placeholder="{{ __('budget') }}">
                        @error('budget')
                            <div class="invalid-feedback font-hold">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="description" class="form-label font-hold fw-bold">{{ __('project_description') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" wire:model="description" id="description"
                            rows="4" placeholder="{{ __('enter_project_description') }}"></textarea>
                        @error('description')
                            <div class="invalid-feedback font-hold">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer bg-transparent border-top-0">
                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary order-2 order-sm-1">
                            <i class="las la-times"></i> {{ __('cancel') }}
                        </a>
                        <button type="submit" class="btn btn-main order-1 order-sm-2">
                            <i class="las la-save"></i> {{ __('save_changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
