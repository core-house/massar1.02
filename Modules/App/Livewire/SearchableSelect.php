<?php

namespace Modules\App\Livewire;

use Livewire\Component;
use Illuminate\Database\Eloquent\Model;

class SearchableSelect extends Component
{
    public $search = '';
    public $selectedId = null;
    public $selectedText = '';
    public $showDropdown = false;
    public $items = [];
    public $filteredItems = [];

    // الخصائص المطلوبة
    public $model; // اسم الموديل مثل: App\Models\Client
    public $labelField; // اسم الحقل المعروض مثل: cname
    public $valueField = 'id'; // اسم حقل القيمة
    public $placeholder = 'ابحث أو أضف جديد...';
    public $label; // العنوان مثل: العميل
    public $wireModel; // اسم المتغير في الكومبوننت الأب
    public $additionalData = []; // بيانات إضافية عند الإنشاء
    public $where = []; // شروط إضافية للفلترة مثل: ['type' => 'client']
    public $with = []; // العلاقات المطلوب تحميلها
    public $searchFields = []; // حقول البحث الإضافية

    protected $listeners = ['refreshItems', 'contactAdded' => 'refreshItems'];

    public function mount()
    {
        $this->loadItems();

        if ($this->selectedId) {
            $this->loadSelectedItem();
            $this->search = $this->selectedText; // عرض الاسم المختار
        }
    }

    public function loadItems()
    {
        $query = app($this->model)::query();

        // تحميل العلاقات إذا كانت محددة
        if (!empty($this->with)) {
            $query->with($this->with);
        }

        // تطبيق الشروط الإضافية
        foreach ($this->where as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        $this->items = $query->get()->map(function ($item) {
            return [
                'id' => $item->{$this->valueField},
                'text' => $this->formatItemText($item),
                'raw' => $item // حفظ الكائن الكامل للاستخدام في العرض
            ];
        })->toArray();

        $this->filteredItems = $this->items;
    }

    private function formatItemText($item)
    {
        $text = $item->{$this->labelField};

        // إذا كان Contact، أضف معلومات إضافية
        if (
            $this->model === 'Modules\Inquiries\Models\Contact' ||
            strpos($this->model, 'Contact') !== false
        ) {

            if (isset($item->type) && $item->type === 'company') {
                $text .= ' (' . __('Company') . ')';
            }

            if (isset($item->parent) && $item->parent) {
                $text .= ' - ' . $item->parent->name;
            }
        }

        return $text;
    }

    public function loadSelectedItem()
    {
        $query = app($this->model)::query();

        if (!empty($this->with)) {
            $query->with($this->with);
        }

        $item = $query->find($this->selectedId);

        if ($item) {
            $this->selectedText = $this->formatItemText($item);
        }
    }

    public function updatedSearch($value)
    {
        $this->showDropdown = !empty($value);

        if (empty($value)) {
            $this->filteredItems = $this->items;
            return;
        }

        $this->filteredItems = array_filter($this->items, function ($item) use ($value) {
            // البحث في النص الأساسي
            if (stripos($item['text'], $value) !== false) {
                return true;
            }

            // البحث في الحقول الإضافية
            if (!empty($this->searchFields) && isset($item['raw'])) {
                foreach ($this->searchFields as $field) {
                    if (
                        isset($item['raw']->{$field}) &&
                        stripos($item['raw']->{$field}, $value) !== false
                    ) {
                        return true;
                    }
                }
            }

            return false;
        });
    }

    public function selectItem($id, $text)
    {
        $this->selectedId = $id;
        $this->selectedText = $text;
        $this->search = $text; // عرض الاسم المختار في حقل البحث
        $this->showDropdown = false;

        // إرسال القيمة للكومبوننت الأب
        $this->dispatch('itemSelected', [
            'wireModel' => $this->wireModel,
            'value' => $id
        ]);
    }

    public function updatedSelectedId($value)
    {
        if ($value) {
            $this->loadSelectedItem();
            $this->search = $this->selectedText; // عرض الاسم في حقل البحث
        } else {
            $this->search = '';
            $this->selectedText = '';
        }
    }

    public function refreshItems()
    {
        $this->loadItems();

        // إذا كان هناك عنصر محدد، تحديث النص المعروض
        if ($this->selectedId) {
            $this->loadSelectedItem();
            $this->search = $this->selectedText;
        }
    }

    public function createNew()
    {
        // التحقق من عدم وجود العنصر
        $exists = collect($this->items)->contains('text', $this->search);

        if ($exists) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'هذا العنصر موجود بالفعل'
            ]);
            return;
        }

        try {
            // دمج البيانات الأساسية مع الإضافية
            $data = array_merge(
                [$this->labelField => $this->search],
                $this->additionalData
            );

            // إنشاء العنصر الجديد
            $newItem = app($this->model)::create($data);

            // تحديث القائمة
            $this->loadItems();

            // اختيار العنصر الجديد
            $this->selectItem($newItem->{$this->valueField}, $this->formatItemText($newItem));

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'تم إنشاء العنصر بنجاح'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء الإنشاء: ' . $e->getMessage()
            ]);
        }
    }

    public function clearSelection()
    {
        $this->selectedId = null;
        $this->selectedText = '';
        $this->search = '';
        $this->showDropdown = false;

        $this->dispatch('itemSelected', [
            'wireModel' => $this->wireModel,
            'value' => null
        ]);
    }

    public function render()
    {
        return view('app::livewire.searchable-select');
    }
}
