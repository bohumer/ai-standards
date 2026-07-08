<?php

namespace App\Livewire\Admin\Product;

use Livewire\Component;
use App\Models\Product;
use Livewire\Attributes\Layout;
use App\Livewire\Traits\WithUniversalTable;

#[Layout('layouts.admin')]
class Index extends \App\Support\BaseLivewireComponent
{
    use \Livewire\WithPagination;
    use WithUniversalTable;

    public function mount()
    {
        $this->setTitle(t('product_index'));
        $this->initFilters(Product::class);
    }

    public function render()
    {
        return view('livewire.admin.product.index', [
            'products' => $this->applyUniversalSearchAndSort(Product::query())->paginate(10),
        ]);
    }
}
