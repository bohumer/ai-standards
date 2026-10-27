<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use App\Support\BaseLivewireComponent;
use App\Traits\WithUniversalTable;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Index extends BaseLivewireComponent
{
    use WithPagination;
    use WithUniversalTable;

    public function mount(): void
    {
        $this->authorize('viewAny', Product::class);
        $this->setTitle(t('admin.product.index.title'));
        $this->initFilters(Product::class, ['delete', 'update_is_active']);
    }

    #[On('product-deleted')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Product::query()->with('user');

        return view('livewire.admin.product.index', [
            'products' => $this->applyUniversalSearchAndSort($query)->paginate($this->getPerPageLimit()),
        ]);
    }
}
