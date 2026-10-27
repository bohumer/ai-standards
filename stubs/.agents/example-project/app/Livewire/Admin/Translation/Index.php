<?php

namespace App\Livewire\Admin\Translation;

use App\Models\Translation;
use App\Support\BaseLivewireComponent;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Index extends BaseLivewireComponent
{
    use WithPagination;

    #[Session]
    public string $search = '';

    #[Session]
    public string $sortDirection = 'asc';

    public function mount(): void
    {
        $this->setTitle(t('admin.translation.index.title'));
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleSort(): void
    {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->resetPage();
    }

    #[On('translation-deleted')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $languages = app_langs();
        $query = Translation::query();

        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $query->where(static function ($q) use ($term) {
                $q->where('key', 'like', $term)
                    ->orWhere('value', 'like', $term);
            });
        }

        $paginatedKeys = $query->select('key')
            ->groupBy('key')
            ->orderBy('key', $this->sortDirection === 'asc' ? 'asc' : 'desc')
            ->paginate(25);

        $rows = Translation::query()
            ->whereIn('key', $paginatedKeys->pluck('key'))
            ->get()
            ->groupBy('key')
            ->map(static fn ($items) => $items->pluck('value', 'lang')->all())
            ->all();

        return view('livewire.admin.translation.index', [
            'paginatedKeys' => $paginatedKeys,
            'translations' => $rows,
            'languages' => $languages,
        ]);
    }
}
