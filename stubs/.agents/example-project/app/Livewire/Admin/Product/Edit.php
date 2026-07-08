<?php

namespace App\Livewire\Admin\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Setting;
use App\Livewire\Traits\HasDrafts;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Edit extends Component
{
    use HasDrafts;

    public $productId;
    public $name = '';
    public $description = '';
    public $price = 0;
    public $is_active = true;
    public $category = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'is_active' => 'boolean',
        'category' => 'nullable|string',
    ];

    public function mount(Product $product)
    {
        $this->productId = $product->id;
        
        if (!$this->loadDraft(Product::class, $this->productId)) {
            $this->fill([
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'is_active' => $product->is_active,
                'category' => $product->category,
            ]);
        }
    }

    public function updated()
    {
        $this->saveDraft(Product::class, $this->productId, $this->only(['name', 'description', 'price', 'is_active', 'category']));
    }

    public function save()
    {
        $validated = $this->validate();
        
        Product::find($this->productId)->update($validated);
        
        $this->clearDraft(Product::class, $this->productId);
        
        session()->flash('success', t('admin.saved'));
        return redirect()->route('admin.product.index');
    }

    public function render()
    {
        return view('livewire.admin.product.edit', [
            'categories' => Setting::getList('product', 'category')
        ]);
    }
}
