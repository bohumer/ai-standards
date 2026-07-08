<!-- TODO PRE AI: Táto šablóna je úmyselne bez dizajnu a bez špecifických Blade komponentov! Musíš na ňu aplikovať Tailwind triedy (farby, tiene, zaoblenia) a nahradiť HTML tagy za existujúce Blade komponenty tak, aby 100% ladili s UI dizajnom aktuálneho projektu! -->
<div>
    <div class="mb-4">
        <a href="{{ route('admin.product.index') }}" wire:navigate class="p-2 border">Späť na zoznam</a>
    </div>

    <div class="border p-4">
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <h2>{{ $page_title ?? 'Detail produktu' }}</h2>
            <div class="space-x-2">
                <a href="{{ route('admin.product.edit', $product->id) }}" wire:navigate class="p-2 border">Upraviť</a>
                <button wire:click="delete" wire:confirm="Naozaj zmazať?" class="p-2 border text-red-500">Zmazať</button>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <strong>ID:</strong>
                <p>{{ $product->id }}</p>
            </div>
            <div>
                <strong>Názov:</strong>
                <p>{{ $product->name }}</p>
            </div>
            <div>
                <strong>Kategória:</strong>
                <p>{{ $product->category }}</p>
            </div>
            <div>
                <strong>Cena:</strong>
                <p>{{ number_format($product->price, 2) }} €</p>
            </div>
            <div>
                <strong>Stav:</strong>
                <p>{{ $product->is_active ? 'Aktívny' : 'Neaktívny' }}</p>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t">
            <strong>Popis:</strong>
            <p class="whitespace-pre-line">{{ $product->description }}</p>
        </div>
    </div>
</div>
