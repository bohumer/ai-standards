<div class="max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.setting.edit.title') }}</h1>
        @if($hasDraft)
            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">{{ t('admin.common.draft_loaded') }}</span>
        @endif
    </div>

    <form wire:submit="save" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Flag</label>
                <input type="text" wire:model.live.blur="flag" class="mt-1 w-full rounded-lg border-gray-300 text-sm" />
                @error('flag') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Key</label>
                <input type="text" wire:model.live.blur="key" class="mt-1 w-full rounded-lg border-gray-300 text-sm" />
                @error('key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">{{ t('admin.setting.fields.value') }}</label>
                <input type="text" wire:model.live.blur="value" class="mt-1 w-full rounded-lg border-gray-300 text-sm" />
                @error('value') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="space-y-4 pt-2 border-t border-gray-100">
            @foreach($languages as $lang)
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        {{ t('admin.setting.fields.label') }} ({{ strtoupper($lang) }})
                    </label>
                    <input type="text" wire:model.live.blur="label.{{ $lang }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm" />
                    @error("label.{$lang}") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            @endforeach
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <button type="button" wire:click="redirectBackWithParams('admin.setting.index')" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700">
                {{ t('admin.common.cancel') }}
            </button>
            <button type="submit" wire:loading.attr="disabled" wire:target="save" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium">
                <span wire:loading.remove wire:target="save">{{ t('admin.common.save') }}</span>
                <span wire:loading wire:target="save">{{ t('admin.common.saving') }}</span>
            </button>
        </div>
    </form>
</div>
