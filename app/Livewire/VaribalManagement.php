<?php

namespace App\Livewire;

use App\Models\Varibal;
use Livewire\Component;
use Livewire\WithPagination;

class VaribalManagement extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $name;
    public $description;
    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $deleteId = null;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ];

        if ($this->editingId) {
            $rules['name'] .= '|unique:varibals,name,' . $this->editingId;
        } else {
            $rules['name'] .= '|unique:varibals,name';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'name.required' => __('validation.name_required'),
            'name.unique'   => __('validation.name_already_exists'),
            'name.max'      => __('validation.name_max_length'),
            'description.max' => __('items.description_max_length'),
        ];
    }

    public function mount()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->editingId = null;
        $this->showModal = false;
        $this->deleteId = null;
        $this->resetErrorBag();
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $varibal = Varibal::findOrFail($id);
        $this->name = $varibal->name;
        $this->description = $varibal->description ?? '';
        $this->editingId = $id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $varibal = Varibal::findOrFail($this->editingId);
            $varibal->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            session()->flash('message', __('items.varibal_updated_successfully'));
        } else {
            Varibal::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            session()->flash('message', __('items.varibal_created_successfully'));
        }

        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function delete()
    {
        if ($this->deleteId) {
            Varibal::findOrFail($this->deleteId)->delete();
            session()->flash('message', __('items.varibal_deleted_successfully'));
            $this->deleteId = null;
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $varibals = Varibal::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.item-management.varibals.varibal', compact('varibals'));
    }
}
