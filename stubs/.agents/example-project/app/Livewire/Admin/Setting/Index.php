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
