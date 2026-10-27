<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use App\Support\BaseLivewireComponent;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('components.layouts.admin')]
class Show extends BaseLivewireComponent
{
    #[Locked]
    public int $productId;

    public function mount(Product $product): void
    {
        $this->authorize('view', $product);
        $this->productId = $product->id;
        $this->referrer = request()->header('Referer');
        $this->setTitle(t('admin.product.show.title') . ' - ' . $product->name);
    }

    #[Computed]
    public function product(): Product
    {
        return Product::query()->with('user')->findOrFail($this->productId);
    }

    public function render(): View
    {
        return view('livewire.admin.product.show');
    }
}
