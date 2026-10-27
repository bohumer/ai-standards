<?php

namespace App\Livewire\Admin\Setting;

use App\Livewire\Traits\HasDrafts;
use App\Models\Setting;
use App\Support\BaseLivewireComponent;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;

#[Layout('components.layouts.admin')]
class Edit extends BaseLivewireComponent
{
    use HasDrafts;

    #[Locked]
    public int $settingId;

    #[Validate('required|string|max:100')]
    public string $flag = '';

    #[Validate('required|string|max:100')]
    public string $key = '';

    public string $value = '';

    public array $label = [];

    #[Computed]
    public function setting(): Setting
    {
        return Setting::query()->findOrFail($this->settingId);
    }

    protected function rules(): array
    {
        $rules = [
            'value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('settings', 'value')
                    ->where(fn ($q) => $q->where('flag', $this->flag)->where('key', $this->key))
                    ->ignore($this->settingId),
            ],
            'label.sk' => ['required', 'string', 'max:255'],
        ];

        foreach (app_langs() as $lang) {
            if ($lang !== 'sk') {
                $rules["label.{$lang}"] = ['nullable', 'string', 'max:255'];
            }
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'value.unique' => t('admin.setting.validation.exists'),
            'label.sk.required' => t('admin.setting.validation.label_required'),
        ];
    }

    public function mount(Setting $setting): void
    {
        $this->settingId = $setting->id;
        $this->referrer = request()->header('Referer');
        $this->setTitle(t('admin.setting.edit.title') . ' - ' . $setting->label);

        $this->flag = $setting->flag;
        $this->key = $setting->key;
        $this->value = $setting->value;

        $labels = $setting->getTranslations('label');
        foreach (app_langs() as $lang) {
            $this->label[$lang] = $labels[$lang] ?? '';
        }

        $this->loadDraft(Setting::class, $this->settingId);
    }

    public function updated(): void
    {
        $this->saveDraft(
            Setting::class,
            $this->settingId,
            $this->only(['flag', 'key', 'value', 'label'])
        );
    }

    public function save(): mixed
    {
        $this->validate();

        $this->setting->update([
            'flag' => strtolower($this->flag),
            'key' => strtolower($this->key),
            'value' => $this->value,
            'label' => $this->label,
        ]);

        $this->clearDraft(Setting::class, $this->settingId);
        $this->flashSuccess(t('admin.setting.messages.updated'));

        return $this->redirectBackWithParams('admin.setting.index');
    }

    public function render(): View
    {
        return view('livewire.admin.setting.edit', [
            'languages' => app_langs(),
        ]);
    }
}
