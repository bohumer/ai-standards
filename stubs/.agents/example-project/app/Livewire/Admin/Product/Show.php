<?php

namespace App\Livewire\Admin\Product;

use Livewire\Component;
use App\Models\Product;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Show extends Component
{
    public Product $product;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function delete()
    {
        $this->product->delete();
        session()->flash('success', t('admin.deleted'));
        return redirect()->route('admin.product.index');
    }

    public function render()
    {
        return view('livewire.admin.product.show');
    }
}
