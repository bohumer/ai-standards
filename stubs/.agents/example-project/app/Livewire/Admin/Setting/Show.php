<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Setting;
use App\Support\BaseLivewireComponent;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('components.layouts.admin')]
class Show extends BaseLivewireComponent
{
    #[Locked]
    public int $settingId;

    public function mount(Setting $setting): void
    {
        $this->settingId = $setting->id;
        $this->referrer = request()->header('Referer');
        $this->setTitle(t('admin.setting.show.title') . ' - ' . $setting->label);
    }

    #[Computed]
    public function setting(): Setting
    {
        return Setting::query()->findOrFail($this->settingId);
    }

    public function render(): View
    {
        return view('livewire.admin.setting.show', [
            'languages' => app_langs(),
        ]);
    }
}
