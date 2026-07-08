<?php

namespace App\Livewire\Admin\Product;

use Livewire\Component;
use App\Models\Product;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Show extends \App\Support\BaseLivewireComponent
{
    public Product $product;

    public function mount(Product $product)
    {
        $this->setTitle(t('product_show') . ' ' . $product->name);
        $this->product = $product;
    }

    public function delete()
    {
        $this->product->delete();
        $this->flashSuccess(t('admin.deleted'));
        return $this->redirectBackWithParams('admin.product.index');
    }

    public function render()
    {
        return view('livewire.admin.product.show');
    }
}
