<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use App\Support\BaseLivewireComponent;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class Delete extends BaseLivewireComponent
{
    #[Locked]
    public ?int $productId = null;

    #[Locked]
    public string $productName = '';

    #[Locked]
    public bool $redirectAfterDelete = false;

    public bool $open = false;

    #[On('confirm-product-delete')]
    public function confirmDelete(int $productId, bool $redirect = false): void
    {
        $product = Product::query()->findOrFail($productId);
        $this->authorize('delete', $product);

        $this->productId = $product->id;
        $this->productName = $product->name;
        $this->redirectAfterDelete = $redirect;
        $this->open = true;
    }

    public function delete(): mixed
    {
        if (! $this->productId) {
            return null;
        }

        $product = Product::query()->findOrFail($this->productId);
        $this->authorize('delete', $product);

        $product->delete();
        $this->open = false;
        $this->flashSuccess(t('admin.product.messages.deleted'));

        if ($this->redirectAfterDelete) {
            return $this->redirect(route('admin.product.index'), navigate: true);
        }

        $this->dispatch('product-deleted');

        return null;
    }

    public function render(): View
    {
        return view('livewire.admin.product.delete');
    }
}
