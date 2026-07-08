<div>
    <div class="mb-6">
        <a href="{{ route('admin.setting.index') }}" class="text-gray-500 hover:text-gray-700">&larr; {{ t('admin.back') }}</a>
        <h1 class="text-2xl font-bold mt-2">{{ t('admin.create') }} Setting</h1>
    </div>

    <form wire:submit.prevent="save" class="bg-white p-6 rounded shadow max-w-2xl">
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Flag</label>
            <input wire:model.live.blur="flag" type="text" class="w-full border rounded px-3 py-2">
            @error('flag') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Key</label>
            <input wire:model.live.blur="key" type="text" class="w-full border rounded px-3 py-2">
            @error('key') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Option Value -->
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">{{ t('setting.field.value') }} (Internal DB Key)</label>
            <input wire:model.live.blur="value" type="text" class="w-full border rounded px-3 py-2" required>
            @error('value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Value SK -->
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">{{ t('setting.field.value_sk') }} (Slovak)</label>
            <input wire:model.live.blur="value_sk" type="text" class="w-full border rounded px-3 py-2" required>
            @error('value_sk') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Value EN -->
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">{{ t('setting.field.value_en') }} (English)</label>
            <input wire:model.live.blur="value_en" type="text" class="w-full border rounded px-3 py-2" required>
            @error('value_en') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">{{ t('admin.save') }}</button>
        </div>
    </form>
</div>
