<?php

namespace App\Livewire\Admin\Setting;

use Livewire\Component;
use App\Models\Setting;
use App\Livewire\Traits\HasDrafts;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Edit extends Component
{
    use HasDrafts;

    public $settingId;
    public $flag = '';
    public $key = '';
    public $value = '';
    public $value_sk = '';
    public $value_en = '';

    protected $rules = [
        'flag' => 'required|string|max:255',
        'key' => 'required|string|max:255',
        'value' => 'required|string|max:255',
        'value_sk' => 'required|string',
        'value_en' => 'required|string',
    ];

    public function mount(Setting $setting)
    {
        $this->settingId = $setting->id;
        
        if (!$this->loadDraft(Setting::class, $this->settingId)) {
            $this->flag = $setting->flag;
            $this->key = $setting->key;
            $this->value = $setting->value;

            $transKey = "{$this->flag}.{$this->key}.{$this->value}";
            
            $transSk = \App\Models\Translation::where('key', $transKey)->where('lang', 'sk')->first();
            $this->value_sk = $transSk ? $transSk->value : '';

            $transEn = \App\Models\Translation::where('key', $transKey)->where('lang', 'en')->first();
            $this->value_en = $transEn ? $transEn->value : '';
        }
    }

    public function updated()
    {
        $this->saveDraft(Setting::class, $this->settingId, $this->only(['flag', 'key', 'value', 'value_sk', 'value_en']));
    }

    public function save()
    {
        $this->validate();

        $exists = Setting::where('flag', $this->flag)
            ->where('key', $this->key)
            ->where('value', $this->value)
            ->where('id', '!=', $this->settingId)
            ->exists();
            
        if ($exists) {
            $this->addError('value', t('setting.error.value_exists'));
            return;
        }

        $setting = Setting::findOrFail($this->settingId);
        
        $setting->update([
            'flag' => $this->flag,
            'key' => $this->key,
            'value' => $this->value,
        ]);
        
        $transKey = "{$this->flag}.{$this->key}.{$this->value}";
        \App\Models\Translation::updateOrCreate(['key' => $transKey, 'lang' => 'sk'], ['value' => $this->value_sk]);
        \App\Models\Translation::updateOrCreate(['key' => $transKey, 'lang' => 'en'], ['value' => $this->value_en]);

        $this->clearDraft(Setting::class, $this->settingId);
        
        session()->flash('success', t('admin.saved'));
        return redirect()->route('admin.setting.index');
    }

    public function render()
    {
        return view('livewire.admin.setting.edit');
    }
}
