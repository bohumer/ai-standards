<?php

namespace App\Livewire\Admin\Product;

use App\Livewire\Traits\HasDrafts;
use App\Models\Product;
use App\Models\Setting;
use App\Support\BaseLivewireComponent;
use Illuminate\Support\Str;
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
    public int $productId;

    public array $name = [];
    public array $slug = [];
    public array $description = [];

    #[Validate('required|numeric|min:0')]
    public float|string $price = 0;

    #[Validate('boolean')]
    public bool $is_active = true;

    #[Validate('nullable|string|max:255')]
    public string $category = '';

    #[Computed]
    public function product(): Product
    {
        return Product::query()->findOrFail($this->productId);
    }

    protected function rules(): array
    {
        $rules = [
            'name.sk' => ['required', 'string', 'max:255'],
            'slug.sk' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug->sk')->ignore($this->productId),
            ],
        ];

        foreach (app_langs() as $lang) {
            if ($lang !== 'sk') {
                $rules["name.{$lang}"] = ['nullable', 'string', 'max:255'];
                $rules["slug.{$lang}"] = [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('products', "slug->{$lang}")->ignore($this->productId),
                ];
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

    public function mount(Product $product): void
    {
        $this->authorize('update', $product);
        $this->productId = $product->id;
        $this->referrer = request()->header('Referer');
        $this->setTitle(t('admin.product.edit.title') . ' - ' . $product->name);

        $names = $product->getTranslations('name');
        $slugs = $product->getTranslations('slug');
        $descriptions = $product->getTranslations('description');

        foreach (app_langs() as $lang) {
            $this->name[$lang] = $names[$lang] ?? '';
            $this->slug[$lang] = $slugs[$lang] ?? '';
            $this->description[$lang] = $descriptions[$lang] ?? '';
        }

        $this->price = (float) $product->price;
        $this->is_active = (bool) $product->is_active;
        $this->category = (string) ($product->category ?? '');

        $this->loadDraft(Product::class, $this->productId);
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
            $this->productId,
            $this->only(['name', 'slug', 'description', 'price', 'is_active', 'category'])
        );
    }

    public function save(): mixed
    {
        $product = $this->product;
        $this->authorize('update', $product);
        $this->validate();

        $product->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'is_active' => $this->is_active,
            'category' => $this->category !== '' ? $this->category : null,
        ]);

        $this->clearDraft(Product::class, $this->productId);
        $this->flashSuccess(t('admin.product.messages.updated'));

        return $this->redirectBackWithParams('admin.product.index');
    }

    public function render(): View
    {
        return view('livewire.admin.product.edit', [
            'categories' => Setting::getList('product', 'category'),
            'languages' => app_langs(),
        ]);
    }
}
