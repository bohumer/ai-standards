<!-- TODO PRE AI: Táto šablóna je úmyselne bez dizajnu! Musíš na ňu aplikovať Tailwind triedy (farby, tiene, zaoblenia, veľkosti) tak, aby 100% ladili s existujúcim UI dizajnom aktuálneho projektu! -->
<div class="space-y-4">
    @php
        $breadcrumbs = ['Nastavenia' => $modelName ? route('admin.settings.index') : null];
        if ($modelName && !$category) {
            $breadcrumbs[$modelName] = null;
        }
        if ($modelName && $category) {
            $breadcrumbs[$modelName] = route('admin.settings.model', ['modelName' => strtolower($modelName)]);
            $breadcrumbs[$settingsStructure[$modelName][$category] ?? $flag] = null;
        }
    @endphp
    
    <!-- Ak v projekte neexistuje komponent x-admin.breadcrumbs, nahraď ho vlastným štandardným breadcrumb zobrazením -->
    <x-admin.breadcrumbs :links="$breadcrumbs" />

    @if(!$modelName)
        {{-- Level 1: Models --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.settings.content') }}" wire:navigate class="p-4 border text-center">
                <h2>Obsah</h2>
                <p>Všeobecný obsah</p>
            </a>
            @foreach($settingsStructure as $model => $categories)
                <a href="{{ route('admin.settings.model', ['modelName' => strtolower($model)]) }}" wire:navigate class="p-4 border text-center">
                    <h2>{{ $model }}</h2>
                    <p>{{ count($categories) }} sekcií</p>
                </a>
            @endforeach
        </div>
    @elseif($modelName && !$category)
        {{-- Level 2: Categories --}}
        <div class="mb-4">
            <!-- Ak neexistuje x-form.button, použi obyčajný <a href="..."> so správnym dizajnom -->
            <x-form.button href="{{ route('admin.settings.index') }}" wire:navigate variant="secondary" icon="hero-arrow-left" text="Späť na modely" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($settingsStructure[$modelName] ?? [] as $catKey => $label)
                <a href="{{ route('admin.settings.show', ['modelName' => strtolower($modelName), 'category' => $catKey]) }}" wire:navigate class="p-4 border text-center">
                    <h2>{{ $label }}</h2>
                </a>
            @endforeach
        </div>
    @else
        {{-- Level 3: Data Table --}}
        <div class="mb-4">
            <!-- Ak neexistuje x-form.button, použi obyčajný <a href="..."> so správnym dizajnom -->
            <x-form.button href="{{ route('admin.settings.model', ['modelName' => strtolower($modelName)]) }}" wire:navigate variant="secondary" icon="hero-arrow-left" text="Späť na kategórie" />
        </div>
        <div class="border">
            <div class="p-4 border-b flex justify-between">
                <h2>{{ $settingsStructure[$modelName][$category] ?? $flag }}</h2>
                <!-- Ak neexistuje x-form.button, použi obyčajný <a href="..."> -->
                <x-form.button href="{{ route('admin.settings.create', ['modelName' => strtolower($modelName), 'category' => $category]) }}" wire:navigate variant="primary" icon="hero-plus" text="Pridať položku" />
            </div>
            
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="p-4 border-b">Kľúč (ID)</th>
                        <th class="p-4 border-b">Zobrazená hodnota</th>
                        <th class="p-4 border-b text-right">Akcie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="p-4 border-b">{{ $item->key }}</td>
                            <td class="p-4 border-b">{{ $item->value }}</td>
                            <td class="p-4 border-b text-right space-x-2">
                                <a href="{{ route('admin.settings.edit', $item->id) }}" wire:navigate title="Upraviť">Upraviť</a>
                                <button wire:click="$dispatch('confirm-delete', { id: {{ $item->id }} })" title="Zmazať">Zmazať</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-4 text-center">
                                <div>Zatiaľ žiadne záznamy</div>
                                <div>Pridajte prvú položku do tejto kategórie kliknutím na tlačidlo hore.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- Delete Confirmation Component (Modal) --}}
    <!-- TODO PRE AI: Tu umiestni vlastný dizajnový Livewire modál pre potvrdzovanie zmazania (napr. na základe eventu 'confirm-delete'). NESMIEŠ použiť natívny wire:confirm! -->
</div>
