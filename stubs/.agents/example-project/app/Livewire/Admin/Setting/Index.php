<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Setting;
use App\Support\BaseLivewireComponent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

class Index extends BaseLivewireComponent
{
    public $modelName = '';
    public $category = '';
    public $flag = '';

    // Create/Edit form
    public $settingId = null;
    #[Validate('required|string|max:100')]
    public $key = '';
    #[Validate('required|string|max:255')]
    public $value = '';

    // Defined settings structure for sidebar
    public $settingsStructure = [
        'Article' => [
            'type' => 'Typy článkov',
            'category' => 'Kategórie článkov',
        ],
        'Course' => [
            'level' => 'Úrovne kurzov',
            'age_group' => 'Vekové skupiny',
        ],
        'StudyGroup' => [
            'day_of_week' => 'Dni v týždni',
        ],
    ];

    public function mount($modelName = null, $category = null)
    {
        if ($modelName) {
            // Find correct case for model from structure
            $matchedModel = collect(array_keys($this->settingsStructure))
                ->first(fn($key) => strtolower($key) === strtolower($modelName));
                
            $this->modelName = $matchedModel ?? ucfirst($modelName);
            
            if ($category) {
                $this->category = strtolower($category);
                $this->flag = $this->modelName . '.' . $this->category;
                $this->setTitle('Nastavenia: ' . ($this->settingsStructure[$this->modelName][$this->category] ?? $this->flag));
            } else {
                $this->setTitle('Nastavenia: ' . $this->modelName);
            }
        } else {
            $this->setTitle('Nastavenia systému');
        }
    }

    public function create()
    {
        $this->resetValidation();
        $this->settingId = null;
        $this->key = '';
        $this->value = '';
        $this->dispatch('open-modal', 'setting-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $setting = Setting::findOrFail($id);
        $this->settingId = $setting->id;
        $this->key = $setting->key;
        $this->value = $setting->value;
        $this->dispatch('open-modal', 'setting-modal');
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

        if ($this->settingId) {
            Setting::find($this->settingId)->update([
                'key' => $this->key,
                'value' => $this->value,
            ]);
            $this->flash('success', 'Položka bola upravená.');
        } else {
            Setting::create([
                'flag' => $this->flag,
                'lang' => 'sk', // Default to SK for now
                'key' => $this->key,
                'value' => $this->value,
            ]);
            $this->flash('success', 'Nová položka bola pridaná.');
        }

        $this->dispatch('close-modal', 'setting-modal');
    }

    public function delete($id)
    {
        Setting::findOrFail($id)->delete();
        $this->flash('success', 'Položka bola zmazaná.');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $items = [];
        if ($this->flag) {
            $items = Setting::where('flag', $this->flag)->orderBy('key')->get();
        }

        return view('livewire.admin.setting.index', [
            'items' => $items,
        ]);
    }
}
