<div>
    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('myresources.search') }}">
        </div>
        <div class="col-md-2">
            <select wire:model.live="categoryFilter" class="form-control">
                <option value="">{{ __("myresources.all_categories") }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select wire:model.live="typeFilter" class="form-control">
                <option value="">{{ __("myresources.all_types") }}</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}">{{ $type->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select wire:model.live="statusFilter" class="form-control">
                <option value="">{{ __("myresources.all_statuses") }}</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}">{{ $status->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button wire:click="resetFilters" class="btn btn-secondary">
                <i class="fas fa-redo"></i> {{ __("myresources.reset") }}
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>{{ __("myresources.code") }}</th>
                    <th>{{ __("myresources.name") }}</th>
                    <th>{{ __("myresources.category") }}</th>
                    <th>{{ __("myresources.type") }}</th>
                    <th>{{ __("myresources.status") }}</th>
                    <th>{{ __("myresources.branch") }}</th>
                    <th>{{ __("myresources.actions") }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resources as $resource)
                    <tr>
                        <td>{{ $resource->code }}</td>
                        <td>{{ $resource->name }}</td>
                        <td>{{ $resource->category->display_name ?? '-' }}</td>
                        <td>{{ $resource->type->display_name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $resource->status->color ?? 'secondary' }}">
                                {{ $resource->status->display_name ?? '-' }}
                            </span>
                        </td>
                        <td>{{ $resource->branch->name ?? '-' }}</td>
                        <td>
                            @can('view MyResources')
                            <a href="{{ route('myresources.show', $resource) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan
                            @can('edit MyResources')
                            <a href="{{ route('myresources.edit', $resource) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                            @can('delete MyResources')
                            <button wire:click="deleteResource({{ $resource->id }})" 
                                    wire:confirm="{{ __("myresources.are_you_sure_delete") }}"
                                    class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">{{ __("myresources.no_resources_found") }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $resources->links() }}
    </div>
</div>

