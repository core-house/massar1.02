<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Modules\HR\Models\Town;
use Modules\HR\Models\City;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public string $title = '';
    public ?int $city_id = null;
    public ?float $distance = null;
    public ?int $townId = null;
    public bool $showModal = false;
    public bool $isEdit = false;
    public string $search = '';

    /**
     * Get validation rules for town form.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:2|max:200|unique:towns,title,' . $this->townId,
            'city_id' => 'required|exists:cities,id',
            'distance' => 'nullable|numeric|min:0|max:999999.99',
        ];
    }

    /**
     * Initialize component on mount.
     */
    public function mount(): void
    {
        // Component initialized
    }

    /**
     * Reset pagination when search changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Get all cities for dropdown.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, City>
     */
    #[Computed]
    public function cities()
    {
        return City::query()->orderBy('title')->get();
    }

    /**
     * Get filtered towns list.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Town>
     */
    #[Computed]
    public function towns()
    {
        return Town::query()
            ->with('city')
            ->when($this->search, fn($query) => $query->where('title', 'like', "%{$this->search}%"))
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Open create modal and reset form.
     */
    public function create(): void
    {
        $this->resetValidation();
        $this->reset(['title', 'city_id', 'townId', 'distance']);
        $this->isEdit = false;
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    /**
     * Open edit modal and load town data.
     *
     * @param int $id
     */
    public function edit(int $id): void
    {
        $this->resetValidation();
        $town = Town::findOrFail($id);
        $this->townId = $town->id;
        $this->title = $town->title;
        $this->city_id = $town->city_id;
        $this->distance = $town->distance !== null ? (float) $town->distance : null;
        $this->isEdit = true;
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    /**
     * Save town (create or update).
     */
    public function save(): void
    {
        $validated = $this->validate();

        if ($this->isEdit) {
            Town::findOrFail($this->townId)->update($validated);
            session()->flash('success', __('hr::hr.town_updated_successfully'));
        } else {
            Town::create($validated);
            session()->flash('success', __('hr::hr.town_created_successfully'));
        }

        $this->showModal = false;
        $this->dispatch('closeModal');
        $this->reset(['title', 'city_id', 'townId', 'distance', 'isEdit']);
    }

    /**
     * Delete town.
     *
     * @param int $id
     */
    public function delete(int $id): void
    {
        $town = Town::findOrFail($id);
        $town->delete();
        session()->flash('success', __('hr::hr.town_deleted_successfully'));
    }
}; ?>

<div class="towns-management" style="font-family: 'Cairo', sans-serif; direction: rtl;">
    <div class="row">
        @if (session()->has('success'))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    @can('create Towns')
                        <button wire:click="create" type="button" class="btn btn-main font-hold fw-bold">
                            <i class="fas fa-plus me-2"></i>
                            {{ __('hr::hr.add_town') }}
                        </button>
                    @endcan
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           class="form-control w-auto"
                           style="min-width: 200px;"
                           placeholder="{{ __('hr::hr.search_by_name') }}">
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th class="font-hold fw-bold">#</th>
                                    <th class="font-hold fw-bold">{{ __('hr::hr.name') }}</th>
                                    <th class="font-hold fw-bold">{{ __('hr::hr.city') }}</th>
                                    <th class="font-hold fw-bold">{{ __('hr::hr.distance_km') }}</th>
                                    @canany(['edit Towns', 'delete Towns'])
                                        <th class="font-hold fw-bold">{{ __('hr::hr.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($this->towns as $town)
                                    <tr>
                                        <td class="font-hold fw-bold text-center">{{ $loop->iteration }}</td>
                                        <td class="font-hold fw-bold text-center">{{ $town->title }}</td>
                                        <td class="font-hold fw-bold text-center">{{ $town->city->title ?? '-' }}</td>
                                        <td class="font-hold fw-bold text-center">{{ $town->distance ? number_format((float) $town->distance, 2) : '-' }}</td>
                                        @canany(['edit Towns', 'delete Towns'])
                                            <td class="font-hold fw-bold text-center">
                                                <div class="btn-group" role="group">
                                                    @can('edit Towns')
                                                        <button type="button"
                                                                wire:click="edit({{ $town->id }})"
                                                                class="btn btn-success btn-sm"
                                                                title="{{ __('hr::hr.edit') }}">
                                                            <i class="las la-edit"></i>
                                                        </button>
                                                    @endcan
                                                    @can('delete Towns')
                                                        <button type="button"
                                                                wire:click="delete({{ $town->id }})"
                                                                wire:confirm="{{ __('hr::hr.are_you_sure_you_want_to_delete_this_town') }}"
                                                                class="btn btn-danger btn-sm"
                                                                title="{{ __('hr::hr.delete') }}">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->canany(['edit Towns', 'delete Towns']) ? '5' : '4' }}"
                                            class="text-center font-hold fw-bold py-4">
                                            <div class="alert alert-info mb-0">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('hr::hr.no_towns_found') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal (Create/Edit) -->
    <div class="modal fade"
         wire:ignore.self
         id="townModal"
         tabindex="-1"
         aria-labelledby="townModalLabel"
         aria-hidden="true"
         data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-hold fw-bold" id="townModalLabel" style="color: white !important;">
                        {{ $isEdit ? __('hr::hr.edit_town') : __('hr::hr.add_town') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label for="title" class="form-label font-hold fw-bold">
                                {{ __('hr::hr.name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror font-hold fw-bold"
                                   id="title"
                                   wire:model.blur="title"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="city_id" class="form-label font-hold fw-bold">
                                {{ __('hr::hr.city') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('city_id') is-invalid @enderror font-hold fw-bold"
                                    id="city_id"
                                    wire:model.blur="city_id"
                                    required>
                                <option value="">{{ __('hr::hr.select_city') }}</option>
                                @foreach ($this->cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->title }}</option>
                                @endforeach
                            </select>
                            @error('city_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="distance" class="form-label font-hold fw-bold">
                                {{ __('hr::hr.distance_km') }}
                            </label>
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   max="999999.99"
                                   class="form-control @error('distance') is-invalid @enderror font-hold fw-bold"
                                   id="distance"
                                   wire:model.defer="distance">
                            @error('distance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">
                                {{ __('hr::hr.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-main">
                                {{ $isEdit ? __('hr::hr.update') : __('hr::hr.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            let modalInstance = null;
            const modalElement = document.getElementById('townModal');

            if (!modalElement) {
                return;
            }

            Livewire.on('showModal', () => {
                if (!modalInstance) {
                    modalInstance = new bootstrap.Modal(modalElement);
                }
                modalInstance.show();
            });

            Livewire.on('closeModal', () => {
                if (modalInstance) {
                    modalInstance.hide();
                }
            });

            modalElement.addEventListener('hidden.bs.modal', () => {
                modalInstance = null;
                @this.call('$refresh');
            });
        });
    </script>
</div>
