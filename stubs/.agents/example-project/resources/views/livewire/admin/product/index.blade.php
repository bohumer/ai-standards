<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.product.index.title') }}</h1>

        @can('create', \App\Models\Product::class)
            <a
                href="{{ route('admin.product.create') }}"
                wire:navigate
                class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700"
            >
                {{ t('admin.product.index.add_new') }}
            </a>
        @endcan
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col sm:flex-row gap-4">
        <input
            type="search"
            wire:model.live.debounce.300ms="search"
            placeholder="{{ t('admin.common.search_placeholder') }}"
            class="w-full sm:w-80 rounded-lg border-gray-300 text-sm"
        />

        <button
            type="button"
            wire:click="resetAllFilters"
            class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900"
        >
            {{ t('admin.common.reset_filters') }}
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('id')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer">ID</th>
                    <th wire:click="sortBy('name')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer">{{ t('admin.product.fields.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ t('admin.product.fields.category') }}</th>
                    <th wire:click="sortBy('price')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer">{{ t('admin.product.fields.price') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ t('admin.product.fields.status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ t('admin.common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr
                        onclick="window.location='{{ route('admin.product.show', $product) }}'"
                        class="hover:bg-gray-50 cursor-pointer transition-colors"
                    >
                        <td class="px-6 py-4 text-sm text-gray-500">#{{ $product->id }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \App\Models\Setting::getList('product', 'category')[$product->category] ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $product->formatted_price }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ $product->is_active ? t('admin.common.active') : t('admin.common.inactive') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm space-x-3" onclick="event.stopPropagation()">
                            @can('update', $product)
                                <a href="{{ route('admin.product.edit', $product) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    {{ t('admin.common.edit') }}
                                </a>
                            @endcan
                            @can('delete', $product)
                                <button
                                    type="button"
                                    wire:click="$dispatch('confirm-product-delete', { productId: {{ $product->id }}, redirect: false })"
                                    class="text-red-600 hover:text-red-900 font-medium"
                                >
                                    {{ t('admin.common.delete') }}
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                            {{ t('admin.common.no_records') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $products->links() }}
    </div>

    <livewire:admin.product.delete />
</div>
