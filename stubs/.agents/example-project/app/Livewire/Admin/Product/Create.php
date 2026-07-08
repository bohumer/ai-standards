<?php

namespace App\Livewire\Admin\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Setting;
use App\Livewire\Traits\HasDrafts;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Create extends \App\Support\BaseLivewireComponent
{
    use HasDrafts;

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

    public function mount()
    {
        $this->setTitle(t('product_create'));
        $this->loadDraft(Product::class, null);
    }

    public function updated()
    {
        $this->saveDraft(Product::class, null, $this->only(['name', 'description', 'price', 'is_active', 'category']));
    }

    public function save()
    {
        $validated = $this->validate();
        
        $validated['user_id'] = auth()->id();
        
        Product::create($validated);
        
        $this->clearDraft(Product::class, null);
        
        $this->flashSuccess(t('admin.saved'));
        return $this->redirectBackWithParams('admin.product.index');
    }

    public function render()
    {
        return view('livewire.admin.product.create', [
            'categories' => Setting::getList('product', 'category')
        ]);
    }
}
