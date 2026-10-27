<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.product.create.title') }}</h1>
        @if($hasDraft)
            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">
                {{ t('admin.common.draft_loaded') }}
            </span>
        @endif
    </div>

    <form wire:submit="save" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
        @foreach($languages as $lang)
            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200 space-y-4">
                <div class="text-xs font-bold uppercase text-gray-500">{{ strtoupper($lang) }}</div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ t('admin.product.fields.name') }} ({{ strtoupper($lang) }})</label>
                    <input type="text" wire:model.live.blur="name.{{ $lang }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm" />
                    @error("name.{$lang}") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ t('admin.product.fields.slug') }} ({{ strtoupper($lang) }})</label>
                    <input type="text" wire:model.live.blur="slug.{{ $lang }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm" />
                    @error("slug.{$lang}") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ t('admin.product.fields.description') }} ({{ strtoupper($lang) }})</label>
                    <textarea rows="3" wire:model.live.blur="description.{{ $lang }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></textarea>
                    @error("description.{$lang}") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endforeach

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ t('admin.product.fields.price') }}</label>
                <input type="number" step="0.01" wire:model.live.blur="price" class="mt-1 w-full rounded-lg border-gray-300 text-sm" />
                @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">{{ t('admin.product.fields.category') }}</label>
                <select wire:model.live.blur="category" class="mt-1 w-full rounded-lg border-gray-300 text-sm">
                    <option value="">{{ t('admin.common.select_option') }}</option>
                    @foreach($categories as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" wire:model.live.blur="is_active" class="rounded border-gray-300 text-indigo-600" />
            <span>{{ t('admin.product.fields.is_active') }}</span>
        </label>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <button
                type="button"
                wire:click="redirectBackWithParams('admin.product.index')"
                class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                {{ t('admin.common.cancel') }}
            </button>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="save">{{ t('admin.common.save') }}</span>
                <span wire:loading wire:target="save">{{ t('admin.common.saving') }}</span>
            </button>
        </div>
    </form>
</div>
