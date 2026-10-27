<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.product.index') }}" wire:navigate class="text-sm text-gray-500 hover:text-gray-800">
                &larr; {{ t('admin.common.back_to_list') }}
            </a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $this->product->name }}</h1>
        </div>

        <div class="flex items-center gap-3">
            @can('update', $this->product)
                <a
                    href="{{ route('admin.product.edit', $this->product) }}"
                    wire:navigate
                    class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700"
                >
                    {{ t('admin.common.edit') }}
                </a>
            @endcan

            @can('delete', $this->product)
                <button
                    type="button"
                    wire:click="$dispatch('confirm-product-delete', { productId: {{ $this->product->id }}, redirect: true })"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700"
                >
                    {{ t('admin.common.delete') }}
                </button>
            @endcan
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
            <dt class="text-xs font-semibold text-gray-500 uppercase">{{ t('admin.product.fields.name') }}</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $this->product->name }}</dd>
        </div>

        <div>
            <dt class="text-xs font-semibold text-gray-500 uppercase">{{ t('admin.product.fields.price') }}</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $this->product->formatted_price }}</dd>
        </div>

        <div>
            <dt class="text-xs font-semibold text-gray-500 uppercase">{{ t('admin.product.fields.category') }}</dt>
            <dd class="mt-1 text-sm text-gray-900">
                {{ \App\Models\Setting::getList('product', 'category')[$this->product->category] ?? '-' }}
            </dd>
        </div>

        <div>
            <dt class="text-xs font-semibold text-gray-500 uppercase">{{ t('admin.product.fields.author') }}</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $this->product->user?->name ?? '-' }}</dd>
        </div>

        <div class="sm:col-span-2">
            <dt class="text-xs font-semibold text-gray-500 uppercase">{{ t('admin.product.fields.description') }}</dt>
            <dd class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $this->product->description ?: '-' }}</dd>
        </div>
    </div>

    <livewire:admin.product.delete />
</div>
