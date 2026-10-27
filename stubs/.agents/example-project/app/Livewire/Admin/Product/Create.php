<?php

namespace App\Livewire\Admin\Product;

use App\Livewire\Traits\HasDrafts;
use App\Models\Product;
use App\Models\Setting;
use App\Support\BaseLivewireComponent;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('components.layouts.admin')]
class Create extends BaseLivewireComponent
{
    use HasDrafts;

    public array $name = [];
    public array $slug = [];
    public array $description = [];

    #[Validate('required|numeric|min:0')]
    public float|string $price = 0;

    #[Validate('boolean')]
    public bool $is_active = true;

    #[Validate('nullable|string|max:255')]
    public string $category = '';

    protected function rules(): array
    {
        $rules = [
            'name.sk' => ['required', 'string', 'max:255'],
            'slug.sk' => ['required', 'string', 'max:255', Rule::unique('products', 'slug->sk')],
        ];

        foreach (app_langs() as $lang) {
            if ($lang !== 'sk') {
                $rules["name.{$lang}"] = ['nullable', 'string', 'max:255'];
                $rules["slug.{$lang}"] = ['nullable', 'string', 'max:255', Rule::unique('products', "slug->{$lang}")];
            }
            $rules["description.{$lang}"] = ['nullable', 'string'];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'name.sk.required' => t('admin.product.validation.name_required'),
            'slug.sk.unique' => t('admin.product.validation.slug_unique'),
            'price.min' => t('admin.product.validation.price_min'),
        ];
    }

    public function mount(): void
    {
        $this->authorize('create', Product::class);
        $this->referrer = request()->header('Referer');
        $this->setTitle(t('admin.product.create.title'));

        foreach (app_langs() as $lang) {
            $this->name[$lang] = '';
            $this->slug[$lang] = '';
            $this->description[$lang] = '';
        }

        $this->loadDraft(Product::class, null);
    }

    public function updated(string $property): void
    {
        foreach (app_langs() as $lang) {
            if ($property === "name.{$lang}" && empty($this->slug[$lang])) {
                $this->slug[$lang] = Str::slug((string) ($this->name[$lang] ?? ''));
            }
        }

        $this->saveDraft(
            Product::class,
            null,
            $this->only(['name', 'slug', 'description', 'price', 'is_active', 'category'])
        );
    }

    public function save(): mixed
    {
        $this->authorize('create', Product::class);

        foreach (app_langs() as $lang) {
            if (empty($this->slug[$lang]) && ! empty($this->name[$lang])) {
                $this->slug[$lang] = Str::slug((string) $this->name[$lang]);
            }
        }

        $this->validate();

        Product::query()->create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'is_active' => $this->is_active,
            'category' => $this->category !== '' ? $this->category : null,
            'published_at' => $this->is_active ? now() : null,
        ]);

        $this->clearDraft(Product::class, null);
        $this->flashSuccess(t('admin.product.messages.created'));

        return $this->redirectBackWithParams('admin.product.index');
    }

    public function render(): View
    {
        return view('livewire.admin.product.create', [
            'categories' => Setting::getList('product', 'category'),
            'languages' => app_langs(),
        ]);
    }
}
