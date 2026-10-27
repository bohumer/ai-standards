<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Setting;
use App\Support\BaseLivewireComponent;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class Delete extends BaseLivewireComponent
{
    #[Locked]
    public ?int $settingId = null;

    #[Locked]
    public string $settingLabel = '';

    #[Locked]
    public bool $redirectAfterDelete = false;

    public bool $open = false;

    #[On('confirm-setting-delete')]
    public function confirmDelete(int $settingId, bool $redirect = false): void
    {
        $setting = Setting::query()->findOrFail($settingId);

        $this->settingId = $setting->id;
        $this->settingLabel = $setting->label;
        $this->redirectAfterDelete = $redirect;
        $this->open = true;
    }

    public function delete(): mixed
    {
        if (! $this->settingId) {
            return null;
        }

        $setting = Setting::query()->findOrFail($this->settingId);
        $setting->delete();

        $this->open = false;
        $this->flashSuccess(t('admin.setting.messages.deleted'));

        if ($this->redirectAfterDelete) {
            return $this->redirect(route('admin.setting.index'), navigate: true);
        }

        $this->dispatch('setting-deleted');

        return null;
    }

    public function render(): View
    {
        return view('livewire.admin.setting.delete');
    }
}
