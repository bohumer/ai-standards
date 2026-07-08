<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Setting;
use App\Support\BaseLivewireComponent;
use App\Livewire\Traits\HasDrafts;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.admin')]
class Create extends BaseLivewireComponent
{
    use HasDrafts;

    public $modelName = '';
    public $category = '';
    public $flag = '';

    #[Validate('required|string|max:100')]
    public $key = '';
    #[Validate('required|string|max:255')]
    public $value = '';

    public function mount($modelName, $category)
    {
        $this->modelName = strtolower($modelName);
        $this->category = strtolower($category);
        $this->flag = ucfirst($this->modelName) . '.' . $this->category;
        
        $this->setTitle(t('admin.settings.create_new') . ' - ' . $this->flag);
        
        $this->loadDraft(Setting::class, null);
    }

    public function updated()
    {
        $this->saveDraft(Setting::class, null, $this->only(['key', 'value', 'modelName', 'category', 'flag']));
    }

    public function save()
    {
        $this->validate();

        // Check for unique key within the same flag
        $exists = Setting::where('flag', $this->flag)
            ->where('key', $this->key)
            ->exists();

        if ($exists) {
            $this->addError('key', 'Tento kľúč už v danej kategórii existuje.');
            return;
        }

        Setting::create([
            'flag' => $this->flag,
            'lang' => 'sk', // Default to SK for now
            'key' => $this->key,
            'value' => $this->value,
            'user_id' => auth()->id() ?? 1,
        ]);
        
        $this->clearDraft(Setting::class, null);
        
        $this->flashSuccess('Nová položka bola pridaná.');
        return $this->redirect(route('admin.settings.show', ['modelName' => $this->modelName, 'category' => $this->category]), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.setting.create');
    }
}
