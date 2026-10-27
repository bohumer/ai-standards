<?php

namespace App\Livewire\Admin\Translation;

use App\Models\Translation;
use App\Support\BaseLivewireComponent;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('components.layouts.admin')]
class Show extends BaseLivewireComponent
{
    #[Locked]
    public string $translationKey;

    public function mount(string $key): void
    {
        abort_unless(Translation::query()->where('key', $key)->exists(), 404);

        $this->translationKey = $key;
        $this->referrer = request()->header('Referer');
        $this->setTitle(t('admin.translation.show.title') . ' - ' . $key);
    }

    #[Computed]
    public function values(): array
    {
        return Translation::query()
            ->where('key', $this->translationKey)
            ->pluck('value', 'lang')
            ->all();
    }

    public function render(): View
    {
        return view('livewire.admin.translation.show', [
            'languages' => app_langs(),
        ]);
    }
}
