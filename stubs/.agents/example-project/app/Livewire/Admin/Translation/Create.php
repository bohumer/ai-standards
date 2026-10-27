<?php

namespace App\Livewire\Admin\Translation;

use App\Livewire\Traits\HasDrafts;
use App\Models\Translation;
use App\Support\BaseLivewireComponent;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class Create extends BaseLivewireComponent
{
    use HasDrafts;

    public string $key = '';
    public array $values = [];

    protected function rules(): array
    {
        $rules = [
            'key' => ['required', 'string', 'max:255', Rule::unique('translations', 'key')],
            'values.sk' => ['required', 'string'],
        ];

        foreach (app_langs() as $lang) {
            if ($lang !== 'sk') {
                $rules["values.{$lang}"] = ['nullable', 'string'];
            }
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'key.required' => t('admin.translation.validation.key_required'),
            'key.unique' => t('admin.translation.validation.key_exists'),
            'values.sk.required' => t('admin.translation.validation.value_required'),
        ];
    }

    public function mount(): void
    {
        $this->referrer = request()->header('Referer');
        $this->setTitle(t('admin.translation.create.title'));

        foreach (app_langs() as $lang) {
            $this->values[$lang] = '';
        }

        $this->loadDraft(Translation::class, null);
    }

    public function updated(): void
    {
        $this->saveDraft(Translation::class, null, $this->only(['key', 'values']));
    }

    public function save(): mixed
    {
        $this->key = trim($this->key);
        $this->validate();

        foreach (app_langs() as $lang) {
            if (isset($this->values[$lang]) && $this->values[$lang] !== '') {
                Translation::query()->updateOrCreate(
                    ['lang' => $lang, 'key' => $this->key],
                    ['value' => $this->values[$lang]]
                );
            }
        }

        $this->clearDraft(Translation::class, null);
        $this->flashSuccess(t('admin.translation.messages.created'));

        return $this->redirectBackWithParams('admin.translation.index');
    }

    public function render(): View
    {
        return view('livewire.admin.translation.create', [
            'languages' => app_langs(),
        ]);
    }
}
