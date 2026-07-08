<!-- TODO PRE AI: Táto šablóna je úmyselne bez dizajnu a bez špecifických Blade komponentov! Musíš na ňu aplikovať Tailwind triedy (farby, tiene, zaoblenia) a nahradiť HTML tagy za existujúce Blade komponenty (napr. tlačidlá, inputy, ikony) tak, aby 100% ladili s UI dizajnom aktuálneho projektu! -->
<div>
    <div class="flex justify-between items-center mb-4">
        <h1>{{ $page_title ?? 'Produkty' }}</h1>
        <a href="{{ route('admin.product.create') }}" wire:navigate class="p-2 border">Pridať produkt</a>
    </div>

    {{-- Filter / Search Area --}}
    <div class="mb-4 flex space-x-2">
        <input type="text" wire:model.live.debounce.500ms="filters.search" placeholder="Hľadať..." class="border p-2">
        <select wire:model.live="filters.category" class="border p-2">
            <option value="">Všetky kategórie</option>
            @foreach($categories ?? [] as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
        <button wire:click="resetFilters" class="p-2 border">Zrušiť filtre</button>
    </div>

    {{-- Data Table --}}
    <div class="border overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr>
                    <th class="p-4 border-b cursor-pointer" wire:click="sortBy('id')">ID</th>
                    <th class="p-4 border-b cursor-pointer" wire:click="sortBy('name')">Názov</th>
                    <th class="p-4 border-b cursor-pointer" wire:click="sortBy('price')">Cena</th>
                    <th class="p-4 border-b">Stav</th>
                    <th class="p-4 border-b text-right">Akcie</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="p-4 border-b">{{ $item->id }}</td>
                        <td class="p-4 border-b">
                            <a href="{{ route('admin.product.show', $item->id) }}" wire:navigate>{{ $item->name }}</a>
                        </td>
                        <td class="p-4 border-b">{{ number_format($item->price, 2) }} €</td>
                        <td class="p-4 border-b">{{ $item->is_active ? 'Aktívny' : 'Neaktívny' }}</td>
                        <td class="p-4 border-b text-right space-x-2">
                            <a href="{{ route('admin.product.edit', $item->id) }}" wire:navigate>Upraviť</a>
                            <button wire:click="delete({{ $item->id }})" wire:confirm="Naozaj zmazať?">Zmazať</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center">Nenašli sa žiadne záznamy.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
