<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Session;

trait WithUniversalTable
{
    #[Session]
    public string $search = '';

    #[Session]
    public string $sortField = 'created_at';

    #[Session]
    public string $sortDirection = 'desc';

    #[Session]
    public array $tableFilters = [];

    #[Session]
    public array $tableDateFilters = [];

    #[Session]
    public array $tableRangeFilters = [];

    #[Session]
    public string $perPage = '10';

    public array $selectedRows = [];
    public bool $selectAll = false;

    #[Locked]
    public string $tableModelClass = '';

    /**
     * Explicitly allowed bulk actions (e.g. ['delete', 'update_is_active']).
     */
    #[Locked]
    public array $allowedBulkActions = [];

    public function initFilters(string $modelClass, array $allowedBulkActions = []): void
    {
        $this->tableModelClass = $modelClass;
        $this->allowedBulkActions = $allowedBulkActions;

        $model = new $modelClass;
        $filterable = property_exists($model, 'filterable') ? $model->filterable : [];

        foreach ($filterable as $field) {
            if (! isset($this->tableFilters[$field])) {
                $this->tableFilters[$field] = method_exists($model, 'getFilterOptions')
                    ? array_map('strval', array_keys($model::getFilterOptions($field)))
                    : [];
            }
        }
    }

    public function updatedSearch(): void
    {
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function updatedTableFilters(): void
    {
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function updatedPerPage(): void
    {
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function resetAllFilters(): void
    {
        $this->search = '';
        $this->tableDateFilters = [];
        $this->tableRangeFilters = [];
        $this->tableFilters = [];

        if ($this->tableModelClass !== '') {
            $this->initFilters($this->tableModelClass, $this->allowedBulkActions);
        }

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function sortBy(string $field): void
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
        $sortable = property_exists($model, 'sortable') ? $model->sortable : ['id', 'created_at'];
        $filterable = property_exists($model, 'filterable') ? $model->filterable : [];
        $translatable = property_exists($model, 'translatable') ? $model->translatable : [];

        // Exact list filters (whitelisted via $model->filterable)
        foreach ($filterable as $field) {
            if (isset($this->tableFilters[$field]) && is_array($this->tableFilters[$field])) {
                $query->whereIn($field, $this->tableFilters[$field]);
            }
        }

        // Date range filters (whitelisted via $sortable or $filterable)
        $allowedColumns = array_unique(array_merge($sortable, $filterable));
        foreach ($this->tableDateFilters as $field => $range) {
            if (! in_array($field, $allowedColumns, true)) {
                continue;
            }
            if (! empty($range['from'])) {
                $query->whereDate($field, '>=', $range['from']);
            }
            if (! empty($range['to'])) {
                $query->whereDate($field, '<=', $range['to']);
            }
        }

        // Numeric range filters (whitelisted)
        foreach ($this->tableRangeFilters as $field => $range) {
            if (! in_array($field, $allowedColumns, true)) {
                continue;
            }
            if (isset($range['min']) && $range['min'] !== '') {
                $query->where($field, '>=', $range['min']);
            }
            if (isset($range['max']) && $range['max'] !== '') {
                $query->where($field, '<=', $range['max']);
            }
        }

        // Search across whitelisted scalar, JSON, and relation columns
        if ($this->search !== '' && ! empty($searchable)) {
            $term = '%' . $this->search . '%';
            $query->where(function (Builder $q) use ($searchable, $translatable, $term) {
                foreach ($searchable as $field) {
                    if (str_contains($field, '.')) {
                        $parts = explode('.', $field);
                        $column = array_pop($parts);
                        $relation = implode('.', $parts);
                        $q->orWhereHas($relation, static fn (Builder $rq) => $rq->where($column, 'like', $term));
                    } elseif (in_array($field, $translatable, true)) {
                        foreach (app_langs() as $lang) {
                            $q->orWhere("{$field}->{$lang}", 'like', $term);
                        }
                    } else {
                        $q->orWhere($field, 'like', $term);
                    }
                }
            });
        }

        // Strict whitelist for ORDER BY column
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';
        if (in_array($this->sortField, $sortable, true)) {
            if (in_array($this->sortField, $translatable, true)) {
                $locale = app_locale();
                $query->orderBy("{$this->sortField}->{$locale}", $direction);
            } else {
                $query->orderBy($this->sortField, $direction);
            }
        }

        $query->orderBy($model->getKeyName(), 'asc');

        return $query;
    }

    public function getPerPageLimit(): int
    {
        return $this->perPage === 'all' ? 500 : max(1, min(100, (int) $this->perPage));
    }

    /**
     * Execute a whitelisted and authorized bulk action on selected records.
     */
    public function handleBulkAction(string $action, mixed $value = null): void
    {
        if (empty($this->selectedRows) || $this->tableModelClass === '') {
            return;
        }

        if (! in_array($action, $this->allowedBulkActions, true)) {
            abort(403, 'Unauthorized bulk action.');
        }

        $records = $this->tableModelClass::query()
            ->whereIn('id', array_map('intval', $this->selectedRows))
            ->get();

        foreach ($records as $record) {
            if ($action === 'delete') {
                Gate::authorize('delete', $record);
                $record->delete();
            } elseif (str_starts_with($action, 'update_')) {
                Gate::authorize('update', $record);
                $column = substr($action, 7);
                $record->update([$column => $value]);
            }
        }

        if ($records->isNotEmpty() && method_exists($this, 'flashSuccess')) {
            $this->flashSuccess(t($action === 'delete' ? 'admin.table.bulk_delete_success' : 'admin.table.bulk_update_success'));
        }

        $this->selectedRows = [];
        $this->selectAll = false;
    }
}
