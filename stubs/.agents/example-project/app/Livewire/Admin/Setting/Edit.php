<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Setting;
use App\Support\BaseLivewireComponent;
use App\Livewire\Traits\HasDrafts;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.admin')]
class Edit extends BaseLivewireComponent
{
    use HasDrafts;

    public $settingId;
    public $modelName = '';
    public $category = '';
    public $flag = '';

    #[Validate('required|string|max:100')]
    public $key = '';
    #[Validate('required|string|max:255')]
    public $value = '';

    public function mount(Setting $setting)
    {
        $this->settingId = $setting->id;
        $this->flag = $setting->flag;
        
        $parts = explode('.', $this->flag);
        $this->modelName = strtolower($parts[0] ?? '');
        $this->category = strtolower($parts[1] ?? '');

        $this->setTitle(t('admin.settings.edit') . ' - ' . $this->flag . ' (' . $setting->key . ')');
        
        if (!$this->loadDraft(Setting::class, $this->settingId)) {
            $this->fill([
                'key' => $setting->key,
                'value' => $setting->value,
            ]);
        }
    }

    public function updated()
    {
        $this->saveDraft(Setting::class, $this->settingId, $this->only(['key', 'value']));
    }

    public function save()
    {
        $this->validate();

        // Check for unique key within the same flag
        $exists = Setting::where('flag', $this->flag)
            ->where('key', $this->key)
            ->where('id', '!=', $this->settingId)
            ->exists();

        if ($exists) {
            $this->addError('key', 'Tento kľúč už v danej kategórii existuje.');
            return;
        }

        Setting::find($this->settingId)->update([
            'key' => $this->key,
            'value' => $this->value,
        ]);
        
        $this->clearDraft(Setting::class, $this->settingId);
        
        $this->flashSuccess('Položka bola upravená.');
        return $this->redirect(route('admin.settings.show', ['modelName' => $this->modelName, 'category' => $this->category]), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.setting.edit');
    }
}
