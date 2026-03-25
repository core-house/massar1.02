<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4 class="mb-0">{{ __('items.varibals_management') }}</h4>
                    <p class="text-muted mb-0">{{ __('items.manage_your_varibals') }}</p>
                </div>
            </div>
            <!-- Search and Filters -->
            <div class="d-flex row justify-content-between">
                <div class="col-md-6">
                    @can('create varibals')
                        <button wire:click="create" class="btn btn-main">
                            <i class="fas fa-plus"></i> {{ __('items.add_new_varibale') }}
                        </button>
                    @endcan

                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                            placeholder="{{ __('items.search_varibals') }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('common.description') }}</th>
                            <th>{{ __('common.created_at') }}</th>
                            @canany(['edit varibals', 'delete varibals'])
                                <th class="text-center">{{ __('Actions') }}</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($varibals as $varibal)
                            <tr>
                                <td>
                                    <strong>{{ $varibal->name }}</strong>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ $varibal->description ? Str::limit($varibal->description, 50) : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $varibal->created_at->format('Y-m-d H:i') }}
                                    </small>
                                </td>
                                @canany(['edit varibals', 'delete varibals'])
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @can('edit varibals')
                                                <button wire:click="edit({{ $varibal->id }})"
                                                    class="btn btn-success m-1 btn-icon-square-sm">
                                                    <i class="las la-edit fa-lg"></i>
                                                </button>
                                            @endcan
                                            @can('delete varibals')
                                                <a wire:click="confirmDelete({{ $varibal->id }})"
                                                    class="btn btn-danger m-1 btn-icon-square-sm">
                                                    <i class="las la-trash fa-lg"></i>
                                                    </button>
                                                @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>{{ __('items.no_varibals_found') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($varibals->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $varibals->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered my-3">
                <div class="modal-content">
                    <div class="modal-header p-3 border-bottom">
                        <h5 class="modal-title">
                            @if ($editingId)
                                {{ __('items.edit_varibal') }}
                            @else
                                {{ __('items.create_new_varibal') }}
                            @endif
                        </h5>
                        <button type="button" wire:click="resetForm" class="btn-close"></button>
                    </div>
                    <div class="modal-body p-4 p-md-4 p-sm-3" style="max-height: 80vh; overflow-y: auto;">
                        <form wire:submit.prevent="save">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="name" class="form-label mb-2">{{ __('common.name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" wire:model="name"
                                        class="form-control py-2 px-3 @error('name') is-invalid @enderror"
                                        id="name" placeholder="{{ __('items.enter_varibal_name') }}">
                                    @error('name')
                                        <div class="invalid-feedback mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label for="description"
                                        class="form-label mb-2">{{ __('common.description') }}</label>
                                    <textarea wire:model="description" class="form-control py-2 px-3 @error('description') is-invalid @enderror"
                                        id="description" rows="3" placeholder="{{ __('items.enter_varibal_description') }}"></textarea>
                                    @error('description')
                                        <div class="invalid-feedback mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer p-3 border-top d-flex gap-2 flex-column flex-sm-row">
                        <button type="button" wire:click="resetForm" class="btn btn-secondary px-3 py-2">
                            {{ __('common.cancel') }}
                        </button>
                        <button type="button" wire:click="save" class="btn btn-main px-3 py-2">
                            @if ($editingId)
                                {{ __('common.update') }}
                            @else
                                {{ __('common.create') }}
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($deleteId)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered my-3">
                <div class="modal-content">
                    <div class="modal-header p-3 border-bottom">
                        <h5 class="modal-title">{{ __('common.confirm_delete') }}</h5>
                        <button type="button" wire:click="deleteId = null" class="btn-close"></button>
                    </div>
                    <div class="modal-body p-4 p-md-4 p-sm-3" style="max-height: 80vh; overflow-y: auto;">
                        <p>{{ __('items.confirm_delete_varibal') }}</p>
                    </div>
                    <div class="modal-footer p-3 border-top d-flex gap-2 flex-column flex-sm-row">
                        <button type="button" wire:click="deleteId = null" class="btn btn-secondary px-3 py-2">
                            {{ __('common.cancel') }}
                        </button>
                        <button type="button" wire:click="delete" class="btn btn-danger px-3 py-2">
                            {{ __('common.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
