<!-- TODO PRE AI: Táto šablóna je úmyselne bez dizajnu a bez špecifických Blade komponentov! Musíš na ňu aplikovať Tailwind triedy (farby, tiene, zaoblenia) a nahradiť HTML tagy za existujúce Blade komponenty (napr. inputy, tlačidlá) tak, aby 100% ladili s UI dizajnom aktuálneho projektu! -->
<div>
    <div class="mb-4">
        <a href="{{ route('admin.product.index') }}" wire:navigate class="p-2 border">Späť na zoznam</a>
    </div>

    <div class="border p-4">
        <h2>{{ $page_title ?? 'Úprava produktu' }}</h2>

        <form wire:submit="save" class="mt-4 space-y-4">
            
            <div>
                <label>Názov</label>
                <input type="text" wire:model.live.blur="name" class="border w-full">
                @error('name') <span>{{ $message }}</span> @enderror
            </div>

            <div>
                <label>Kategória</label>
                <select wire:model.live.blur="category" class="border w-full">
                    <option value="">Vyberte kategóriu</option>
                    @foreach($categories ?? [] as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('category') <span>{{ $message }}</span> @enderror
            </div>

            <div>
                <label>Popis</label>
                <textarea wire:model.live.blur="description" class="border w-full" rows="4"></textarea>
                @error('description') <span>{{ $message }}</span> @enderror
            </div>

            <div>
                <label>Cena (€)</label>
                <input type="number" step="0.01" wire:model.live.blur="price" class="border w-full">
                @error('price') <span>{{ $message }}</span> @enderror
            </div>

            <div>
                <label>
                    <input type="checkbox" wire:model.live.blur="is_active">
                    Aktívny produkt
                </label>
                @error('is_active') <span>{{ $message }}</span> @enderror
            </div>

            <div class="mt-4 pt-4 border-t flex space-x-2">
                <button type="submit" class="p-2 border bg-blue-500 text-white">Uložiť zmeny</button>
                <a href="{{ route('admin.product.index') }}" wire:navigate class="p-2 border">Zrušiť</a>
            </div>
        </form>
    </div>
</div>
