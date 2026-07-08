<!-- TODO PRE AI: Táto šablóna je úmyselne bez dizajnu! Musíš na ňu aplikovať Tailwind triedy (farby, tiene, zaoblenia, veľkosti) tak, aby 100% ladili s existujúcim UI dizajnom aktuálneho projektu! -->
<div class="space-y-4">
    @php
        $breadcrumbs = [
            'Nastavenia' => route('admin.settings.index'),
            ucfirst($modelName) => route('admin.settings.model', ['modelName' => $modelName]),
            $category => route('admin.settings.show', ['modelName' => $modelName, 'category' => $category]),
            'Úprava položky' => null,
        ];
    @endphp
    
    <!-- Ak v projekte neexistuje komponent x-admin.breadcrumbs, nahraď ho vlastným štandardným breadcrumb zobrazením -->
    <x-admin.breadcrumbs :links="$breadcrumbs" />

    <div class="border">
        <div class="p-4 border-b">
            <h2>Úprava položky: {{ $key }}</h2>
        </div>

        <form wire:submit="save">
            <div class="p-4 space-y-4">
                <div>
                    <!-- Ak v projekte neexistujú form komponenty, použi klasické <label> a <input> s triedami podľa dizajnu -->
                    <x-input-label for="key" value="Kľúč (ID pre systém)" />
                    <x-text-input id="key" type="text" class="w-full" wire:model.live="key" placeholder="napr. news, a1, adults..." />
                    <x-input-error :messages="$errors->get('key')" />
                    <p class="text-xs mt-1">Kľúč by mal obsahovať iba malé písmená, čísla a pomlčky bez medzier.</p>
                </div>

                <div>
                    <x-input-label for="value" value="Zobrazená hodnota" />
                    <x-text-input id="value" type="text" class="w-full" wire:model.live="value" placeholder="napr. Novinky, Úroveň A1..." />
                    <x-input-error :messages="$errors->get('value')" />
                </div>
            </div>

            <div class="p-4 border-t flex space-x-2">
                <!-- Ak neexistujú x-form.button, použi obyčajné tlačidlá s dizajnom podľa projektu -->
                <x-primary-button>
                    Uložiť
                </x-primary-button>
                <x-secondary-button href="{{ route('admin.settings.show', ['modelName' => $modelName, 'category' => $category]) }}" wire:navigate>
                    Zrušiť
                </x-secondary-button>
            </div>
        </form>
    </div>
</div>
