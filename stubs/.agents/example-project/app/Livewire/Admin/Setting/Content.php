<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Translation;
use App\Support\BaseLivewireComponent;
use Livewire\Attributes\Layout;

class Content extends BaseLivewireComponent
{
    public $activeTab = 'history';
    public $translations = [];
    public $languages = ['sk', 'fr', 'en'];

    public function mount()
    {
        $this->loadTranslations();
    }

    public function loadTranslations()
    {
        $keys = $this->getAvailableKeys();
        $dbTranslations = Translation::whereIn('key', $keys)->get();

        foreach ($keys as $key) {
            foreach ($this->languages as $lang) {
                $this->translations[$key][$lang] = $dbTranslations->where('key', $key)->where('lang', $lang)->first()?->value ?? '';
            }
        }
    }

    public function save()
    {
        foreach ($this->translations as $key => $values) {
            foreach ($values as $lang => $value) {
                Translation::updateOrCreate(
                    ['key' => $key, 'lang' => $lang],
                    ['value' => $value]
                );
            }
        }

        $this->flash('success', 'Nastavenia boli úspešne uložené.');
    }

    public function getAvailableKeys()
    {
        $allKeys = [];
        foreach ($this->getPages() as $page) {
            $allKeys = array_merge($allKeys, array_keys($page['keys']));
        }
        return array_unique($allKeys);
    }

    public function getPages()
    {
        return [
            'history' => [
                'name' => 'O nás / História',
                'keys' => [
                    'history_meta_title' => ['name' => 'Hlavný nadpis', 'type' => 'input'],
                    'history_intro_text' => ['name' => 'Úvodný text', 'type' => 'editor'],
                    'history_rebirth_title' => ['name' => 'Nadpis: Znovuzrodenie', 'type' => 'input'],
                    'history_rebirth_text_1' => ['name' => 'Text: Znovuzrodenie 1', 'type' => 'editor'],
                    'history_rebirth_text_2' => ['name' => 'Text: Znovuzrodenie 2', 'type' => 'editor'],
                    'history_modern_title' => ['name' => 'Nadpis: Súčasné pôsobenie', 'type' => 'input'],
                    'history_modern_text_1' => ['name' => 'Text: Súčasné pôsobenie 1', 'type' => 'editor'],
                    'history_modern_text_2' => ['name' => 'Text: Súčasné pôsobenie 2', 'type' => 'editor'],
                    'history_modern_text_3' => ['name' => 'Text: Súčasné pôsobenie 3', 'type' => 'editor'],
                ]
            ],
            'membership' => [
                'name' => 'Členstvo',
                'keys' => [
                    'membership_title' => ['name' => 'Nadpis', 'type' => 'input'],
                    'membership_subtitle' => ['name' => 'Podnadpis', 'type' => 'editor'],
                    'membership_benefits_title' => ['name' => 'Nadpis výhod', 'type' => 'input'],
                    'membership_reminder' => ['name' => 'Pripomienka (dole)', 'type' => 'editor'],
                ]
            ],
            'library' => [
                'name' => 'Mediatéka',
                'keys' => [
                    'library_title' => ['name' => 'Nadpis', 'type' => 'input'],
                    'library_subtitle' => ['name' => 'Podnadpis', 'type' => 'editor'],
                    'library_pricing_title' => ['name' => 'Nadpis cenníka', 'type' => 'input'],
                    'library_literature_title' => ['name' => 'Literatúra - Nadpis', 'type' => 'input'],
                    'library_literature_desc' => ['name' => 'Literatúra - Text', 'type' => 'editor'],
                    'library_multimedia_title' => ['name' => 'Multimédiá - Nadpis', 'type' => 'input'],
                    'library_multimedia_desc' => ['name' => 'Multimédiá - Text', 'type' => 'editor'],
                    'library_press_title' => ['name' => 'Tlač - Nadpis', 'type' => 'input'],
                    'library_press_desc' => ['name' => 'Tlač - Text', 'type' => 'editor'],
                    'library_kids_title' => ['name' => 'Pre deti - Nadpis', 'type' => 'input'],
                    'library_kids_desc' => ['name' => 'Pre deti - Text', 'type' => 'editor'],
                    'library_pedagogy_title' => ['name' => 'Pedagogický kútik - Nadpis', 'type' => 'input'],
                    'library_pedagogy_desc' => ['name' => 'Pedagogický kútik - Text', 'type' => 'editor'],
                ]
            ],
            'team' => [
                'name' => 'Náš tím',
                'keys' => [
                    'team_title' => ['name' => 'Hlavný nadpis', 'type' => 'input'],
                    'team_subtitle' => ['name' => 'Podnadpis', 'type' => 'editor'],
                    'team_section_leadership' => ['name' => 'Sekcia: Vedenie', 'type' => 'input'],
                    'team_section_lecturers' => ['name' => 'Sekcia: Lektori', 'type' => 'input'],
                    'team_section_committee' => ['name' => 'Sekcia: Výbor', 'type' => 'input'],
                    'team_section_former' => ['name' => 'Sekcia: Bývalí', 'type' => 'input'],
                ]
            ],
        ];
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $this->setTitle('Nastavenia obsahu');
        return view('livewire.admin.setting.content', [
            'pages' => $this->getPages()
        ]);
    }
}
