<?php

namespace App\Livewire\Traits;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Attributes\Session;

trait WithUniversalTable
{
    #[Url]
    #[Session]
    public $search = '';

    #[Url]
    #[Session]
    public $sortField = 'created_at';

    #[Url]
    #[Session]
    public $sortDirection = 'desc';

    #[Url]
    #[Session]
    public array $tableFilters = [];

    public function updatedSearch()
    {
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function updatedTableFilters()
    {
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function initFilters(string $modelClass)
    {
        $model = new $modelClass;
        $filterable = property_exists($model, 'filterable') ? $model->filterable : [];
        foreach ($filterable as $field) {
            if (!isset($this->tableFilters[$field])) {
                if (method_exists($model, 'getFilterOptions')) {
                    $this->tableFilters[$field] = array_keys($model::getFilterOptions($field));
                } else {
                    $this->tableFilters[$field] = [];
                }
            }
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function applyUniversalSearchAndSort(Builder $query): Builder
    {
        $model = $query->getModel();
        $searchable = property_exists($model, 'searchable') ? $model->searchable : [];
        $sortable = property_exists($model, 'sortable') ? $model->sortable : [];
        $filterable = property_exists($model, 'filterable') ? $model->filterable : [];

        // Apply filters
        if (!empty($filterable)) {
            foreach ($filterable as $field) {
                if (isset($this->tableFilters[$field]) && !empty($this->tableFilters[$field])) {
                    // Check if it's an array for whereIn, otherwise string where
                    if(is_array($this->tableFilters[$field])) {
                        $query->whereIn($field, $this->tableFilters[$field]);
                    } else {
                        $query->where($field, $this->tableFilters[$field]);
                    }
                }
            }
        }

        // Apply search
        if (!empty($this->search) && !empty($searchable)) {
            $query->where(function ($q) use ($searchable) {
                foreach ($searchable as $field) {
                    if (str_contains($field, '.')) {
                        $parts = explode('.', $field);
                        $column = array_pop($parts);
                        $relation = implode('.', $parts);
                        
                        $q->orWhereHas($relation, function ($rq) use ($column) {
                            $rq->where($column, 'like', '%' . $this->search . '%');
                        });
                    } else {
                        $q->orWhere($field, 'like', '%' . $this->search . '%');
                    }
                }
            });
        }

        // Apply sorting
        if (!empty($this->sortField)) {
            if (!empty($sortable) && in_array($this->sortField, $sortable)) {
                $query->orderBy($this->sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc');
            } elseif (empty($sortable)) {
                $query->orderBy($this->sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc');
            }
        }

        return $query;
    }
}
