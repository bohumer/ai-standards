<?php

namespace App\Livewire\Admin\Translation;

use App\Livewire\Traits\HasDrafts;
use App\Models\Translation;
use App\Support\BaseLivewireComponent;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('components.layouts.admin')]
class Edit extends BaseLivewireComponent
{
    use HasDrafts;

    #[Locked]
    public string $translationKey;

    public array $values = [];

    protected function rules(): array
    {
        $rules = [
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
            'values.sk.required' => t('admin.translation.validation.value_required'),
        ];
    }

    public function mount(string $key): void
    {
        $existing = Translation::query()->where('key', $key)->pluck('value', 'lang')->all();
        abort_if(empty($existing), 404);

        $this->translationKey = $key;
        $this->referrer = request()->header('Referer');
        $this->setTitle(t('admin.translation.edit.title') . ' - ' . $key);

        foreach (app_langs() as $lang) {
            $this->values[$lang] = $existing[$lang] ?? '';
        }

        $this->loadDraft(Translation::class, null, $this->translationKey);
    }

    public function updated(): void
    {
        $this->saveDraft(Translation::class, null, $this->only(['values']), $this->translationKey);
    }

    public function save(): mixed
    {
        $this->validate();

        foreach (app_langs() as $lang) {
            $val = $this->values[$lang] ?? '';
            if ($val !== '') {
                Translation::query()->updateOrCreate(
                    ['lang' => $lang, 'key' => $this->translationKey],
                    ['value' => $val]
                );
            } else {
                Translation::query()
                    ->where('lang', $lang)
                    ->where('key', $this->translationKey)
                    ->delete();
            }
        }

        $this->clearDraft(Translation::class, null, $this->translationKey);
        $this->flashSuccess(t('admin.translation.messages.updated'));

        return $this->redirectBackWithParams('admin.translation.index');
    }

    public function render(): View
    {
        return view('livewire.admin.translation.edit', [
            'languages' => app_langs(),
        ]);
    }
}
