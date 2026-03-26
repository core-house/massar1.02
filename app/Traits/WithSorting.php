<?php
namespace App\Traits;

trait WithSorting
{
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function applySorting($query)
    {
        // نأكد إن الترتيب بيحصل على مستوى الجدول الأساسي لتجنب Ambiguous columns
        return $query->orderBy($this->sortField, $this->sortDirection);
    }
}