<?php

namespace App\Livewire\Admin\Translation;

use App\Models\Translation;
use App\Support\BaseLivewireComponent;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class Delete extends BaseLivewireComponent
{
    #[Locked]
    public string $translationKey = '';

    #[Locked]
    public bool $redirectAfterDelete = false;

    public bool $open = false;

    #[On('confirm-translation-delete')]
    public function confirmDelete(string $key, bool $redirect = false): void
    {
        $this->translationKey = $key;
        $this->redirectAfterDelete = $redirect;
        $this->open = true;
    }

    public function delete(): mixed
    {
        if ($this->translationKey === '') {
            return null;
        }

        Translation::query()->where('key', $this->translationKey)->delete();

        $this->open = false;
        $this->flashSuccess(t('admin.translation.messages.deleted'));

        if ($this->redirectAfterDelete) {
            return $this->redirect(route('admin.translation.index'), navigate: true);
        }

        $this->dispatch('translation-deleted');

        return null;
    }

    public function render(): View
    {
        return view('livewire.admin.translation.delete');
    }
}
