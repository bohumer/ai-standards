<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('admin.setting.index') }}" class="text-gray-500 hover:text-gray-700">&larr; {{ t('admin.back') }}</a>
            <h1 class="text-2xl font-bold mt-2">Setting Detail</h1>
        </div>
        <div class="space-x-2">
            <a href="{{ route('admin.setting.edit', $setting) }}" class="bg-yellow-500 text-white px-4 py-2 rounded">{{ t('admin.edit') }}</a>
            <button wire:click="delete" wire:confirm="Are you sure?" class="bg-red-600 text-white px-4 py-2 rounded">{{ t('admin.delete') }}</button>
        </div>
    </div>

    <div class="bg-white p-6 rounded shadow max-w-2xl">
        <div class="mb-4 border-b pb-4">
            <p class="text-gray-500 text-sm">ID</p>
            <p class="font-medium">{{ $setting->id }}</p>
        </div>
        
        <div class="mb-4 border-b pb-4">
            <p class="text-gray-500 text-sm">Flag</p>
            <p class="font-medium">{{ $setting->flag }}</p>
        </div>

        <div class="mb-4 border-b pb-4">
            <p class="text-gray-500 text-sm">Key</p>
            <p class="font-medium">{{ $setting->key }}</p>
        </div>

        <div class="mb-4">
            <p class="text-gray-500 text-sm">Value (Internal DB Key)</p>
            <p class="font-medium">{{ $setting->value }}</p>
        </div>

        <div>
            <p class="text-gray-500 text-sm">Translated Text</p>
            <p class="font-medium">{{ t($setting->flag . '.' . $setting->key . '.' . $setting->value) }}</p>
        </div>
    </div>
</div>
