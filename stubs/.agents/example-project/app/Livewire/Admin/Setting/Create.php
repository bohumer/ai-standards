<?php

namespace App\Livewire\Admin\Setting;

use App\Livewire\Traits\HasDrafts;
use App\Models\Setting;
use App\Support\BaseLivewireComponent;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('components.layouts.admin')]
class Create extends BaseLivewireComponent
{
    use HasDrafts;

    #[Validate('required|string|max:100')]
    public string $flag = '';

    #[Validate('required|string|max:100')]
    public string $key = '';

    public string $value = '';

    public array $label = [];

    protected function rules(): array
    {
        $rules = [
            'value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('settings', 'value')->where(
                    fn ($q) => $q->where('flag', $this->flag)->where('key', $this->key)
                ),
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

    public function mount(?string $flag = null, ?string $key = null): void
    {
        $this->referrer = request()->header('Referer');
        $this->flag = $flag ? strtolower($flag) : '';
        $this->key = $key ? strtolower($key) : '';
        $this->setTitle(t('admin.setting.create.title'));

        foreach (app_langs() as $lang) {
            $this->label[$lang] = '';
        }

        $this->loadDraft(Setting::class, null, "{$this->flag}.{$this->key}");
    }

    public function updated(): void
    {
        $this->saveDraft(
            Setting::class,
            null,
            $this->only(['flag', 'key', 'value', 'label']),
            "{$this->flag}.{$this->key}"
        );
    }

    public function save(): mixed
    {
        $this->validate();

        Setting::query()->create([
            'flag' => strtolower($this->flag),
            'key' => strtolower($this->key),
            'value' => $this->value,
            'label' => $this->label,
        ]);

        $this->clearDraft(Setting::class, null, "{$this->flag}.{$this->key}");
        $this->flashSuccess(t('admin.setting.messages.created'));

        return $this->redirectBackWithParams('admin.setting.index');
    }

    public function render(): View
    {
        return view('livewire.admin.setting.create', [
            'languages' => app_langs(),
        ]);
    }
}
