<div>
    <div class="mb-6">
        <a href="{{ route('admin.product.index') }}" class="text-gray-500 hover:text-gray-700">&larr; {{ t('admin.back') }}</a>
        <h1 class="text-2xl font-bold mt-2">{{ t('product.edit.title') }}</h1>
    </div>

    <form wire:submit.prevent="save" class="bg-white p-6 rounded shadow max-w-2xl">
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">{{ t('product.field.name') }}</label>
            <input wire:model.live.blur="name" type="text" class="w-full border rounded px-3 py-2">
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">{{ t('product.field.description') }}</label>
            <textarea wire:model.live.blur="description" class="w-full border rounded px-3 py-2" rows="4"></textarea>
            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">{{ t('product.field.price') }} (&euro;)</label>
            <input wire:model.live.blur="price" type="number" step="0.01" class="w-full border rounded px-3 py-2">
            @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">{{ t('product.field.category') }}</label>
            <select wire:model.live.blur="category" class="w-full border rounded px-3 py-2 bg-white">
                <option value="">-- Vyberte --</option>
                @foreach($categories as $key => $val)
                    <option value="{{ $key }}">{{ t("product.category.{$val}") }}</option>
                @endforeach
            </select>
            @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input wire:model.live.blur="is_active" type="checkbox" class="form-checkbox h-5 w-5 text-blue-600">
                <span class="text-gray-700">{{ t('product.field.is_active') }}</span>
            </label>
            @error('is_active') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">{{ t('admin.save') }}</button>
        </div>
    </form>
</div>
