<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('admin.product.index') }}" class="text-gray-500 hover:text-gray-700">&larr; {{ t('admin.back') }}</a>
            <h1 class="text-2xl font-bold mt-2">{{ t('product.show.title') }}</h1>
        </div>
        <div class="space-x-2">
            <a href="{{ route('admin.product.edit', $product) }}" class="bg-yellow-500 text-white px-4 py-2 rounded">{{ t('admin.edit') }}</a>
            <button wire:click="delete" wire:confirm="Are you sure?" class="bg-red-600 text-white px-4 py-2 rounded">{{ t('admin.delete') }}</button>
        </div>
    </div>

    <div class="bg-white p-6 rounded shadow max-w-2xl">
        <div class="mb-4 border-b pb-4">
            <p class="text-gray-500 text-sm">ID</p>
            <p class="font-medium">{{ $product->id }}</p>
        </div>
        
        <div class="mb-4 border-b pb-4">
            <p class="text-gray-500 text-sm">{{ t('product.field.name') }}</p>
            <p class="font-medium">{{ $product->name }}</p>
        </div>

        <div class="mb-4 border-b pb-4">
            <p class="text-gray-500 text-sm">{{ t('product.field.description') }}</p>
            <p class="font-medium">{{ $product->description }}</p>
        </div>

        <div class="mb-4 border-b pb-4">
            <p class="text-gray-500 text-sm">{{ t('product.field.price') }}</p>
            <p class="font-medium">{{ number_format($product->price, 2) }} &euro;</p>
        </div>

        <div class="mb-4 border-b pb-4">
            <p class="text-gray-500 text-sm">{{ t('product.field.category') }}</p>
            <p class="font-medium">{{ $product->category ? t("product.category.{$product->category}") : '' }}</p>
        </div>

        <div>
            <p class="text-gray-500 text-sm">{{ t('product.field.is_active') }}</p>
            <p class="font-medium">
                @if($product->is_active)
                    <span class="text-green-600">Aktívny</span>
                @else
                    <span class="text-red-600">Neaktívny</span>
                @endif
            </p>
        </div>
    </div>
</div>
