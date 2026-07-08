<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ t('product.index.title') }}</h1>
        <a href="{{ route('admin.product.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">{{ t('admin.create') }}</a>
    </div>

    <!-- Search & Filter Area -->
    <div class="mb-4 flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4 bg-white p-4 rounded shadow">
        <div class="w-full md:w-1/3">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ t('admin.search') }}" class="border rounded px-3 py-2 w-full">
        </div>
        
        <div class="w-full md:w-1/4 flex items-center space-x-2">
            <label class="text-gray-600 text-sm">Status:</label>
            <select wire:model.live="tableFilters.is_active" class="border rounded px-3 py-2 w-full bg-white">
                <option value="">Všetky</option>
                <option value="1">Aktívne</option>
                <option value="0">Neaktívne</option>
            </select>
        </div>

        @if(isset($tableFilters['category']))
        <div class="w-full md:w-1/4 flex items-center space-x-2">
            <label class="text-gray-600 text-sm">Kategória:</label>
            <select wire:model.live="tableFilters.category" class="border rounded px-3 py-2 w-full bg-white">
                <option value="">Všetky</option>
                @foreach(App\Models\Product::getFilterOptions('category') as $key => $val)
                    <option value="{{ $key }}">{{ t("product.category.{$val}") }}</option>
                @endforeach
            </select>
        </div>
        @endif
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-3 cursor-pointer" wire:click="sortBy('id')">ID</th>
                    <th class="p-3 cursor-pointer" wire:click="sortBy('name')">{{ t('product.field.name') }}</th>
                    <th class="p-3 cursor-pointer" wire:click="sortBy('price')">{{ t('product.field.price') }}</th>
                    <th class="p-3">{{ t('product.field.category') }}</th>
                    <th class="p-3">{{ t('product.field.is_active') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr class="border-b hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.product.show', $product) }}'">
                    <td class="p-3">{{ $product->id }}</td>
                    <td class="p-3">{{ $product->name }}</td>
                    <td class="p-3">{{ number_format($product->price, 2) }} &euro;</td>
                    <td class="p-3">{{ $product->category ? t("product.category.{$product->category}") : '' }}</td>
                    <td class="p-3">
                        @if($product->is_active)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Aktívny</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Neaktívny</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
