<div class="space-y-6">
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
    <x-admin.breadcrumbs :links="$breadcrumbs" />

    @if(!$modelName)
        {{-- Level 1: Models --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('admin.settings.content') }}" wire:navigate class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all group flex flex-col items-center justify-center text-center">
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-widest">Obsah</h2>
                <p class="text-sm text-slate-500 mt-2">Všeobecný obsah</p>
            </a>
            @foreach($settingsStructure as $model => $categories)
                <a href="{{ route('admin.settings.model', ['modelName' => strtolower($model)]) }}" wire:navigate class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all group flex flex-col items-center justify-center text-center">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-widest">{{ $model }}</h2>
                    <p class="text-sm text-slate-500 mt-2">{{ count($categories) }} sekcií</p>
                </a>
            @endforeach
        </div>
    @elseif($modelName && !$category)
        {{-- Level 2: Categories --}}
        <div class="mb-4">
            <x-form.button href="{{ route('admin.settings.index') }}" wire:navigate variant="secondary" icon="hero-arrow-left" text="Späť na modely" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($settingsStructure[$modelName] ?? [] as $catKey => $label)
                <a href="{{ route('admin.settings.show', ['modelName' => strtolower($modelName), 'category' => $catKey]) }}" wire:navigate class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all group flex flex-col items-center justify-center text-center">
                    <h2 class="text-lg font-black text-slate-900 uppercase tracking-widest">{{ $label }}</h2>
                </a>
            @endforeach
        </div>
    @else
        {{-- Level 3: Data Table --}}
        <div class="mb-4">
            <x-form.button href="{{ route('admin.settings.model', ['modelName' => strtolower($modelName)]) }}" wire:navigate variant="secondary" icon="hero-arrow-left" text="Späť na kategórie" />
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-widest">{{ $settingsStructure[$modelName][$category] ?? $flag }}</h2>
                <x-form.button wire:click="create" variant="primary" icon="hero-plus" text="Pridať položku" />
            </div>
            
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kľúč (ID)</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Zobrazená hodnota</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Akcie</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-slate-500">{{ $item->key }}</td>
                            <td class="px-6 py-4 text-sm font-black text-slate-900">{{ $item->value }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button wire:click="edit({{ $item->id }})" class="p-2 text-slate-400 hover:text-indigo-600 transition" title="Upraviť">
                                        <x-hero-pencil class="w-5 h-5" />
                                    </button>
                                    <button wire:click="delete({{ $item->id }})" class="p-2 text-slate-400 hover:text-red-600 transition" title="Zmazať" wire:confirm="Naozaj chcete zmazať túto položku?">
                                        <x-hero-trash class="w-5 h-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="text-slate-400 font-bold mb-2">Zatiaľ žiadne záznamy</div>
                                <div class="text-sm text-slate-400">Pridajte prvú položku do tejto kategórie kliknutím na tlačidlo hore.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- Modal pre pridanie/úpravu --}}
    <x-modal name="setting-modal" maxWidth="md">
        <form wire:submit="save">
            <div class="px-6 py-4">
                <div class="text-lg font-medium text-slate-900 uppercase font-black tracking-widest">
                    {{ $settingId ? 'Úprava položky' : 'Nová položka' }}
                </div>

                <div class="mt-4 space-y-4">
                    <div>
                        <x-input-label for="key" value="Kľúč (ID pre systém)" />
                        <x-text-input id="key" type="text" class="mt-1 block w-full" wire:model="key" placeholder="napr. news, a1, adults..." />
                        <x-input-error :messages="$errors->get('key')" class="mt-2" />
                        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider">Kľúč by mal obsahovať iba malé písmená, čísla a pomlčky bez medzier.</p>
                    </div>

                    <div>
                        <x-input-label for="value" value="Zobrazená hodnota" />
                        <x-text-input id="value" type="text" class="mt-1 block w-full" wire:model="value" placeholder="napr. Novinky, Úroveň A1..." />
                        <x-input-error :messages="$errors->get('value')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 text-right flex justify-end space-x-2 rounded-b-lg">
                <x-secondary-button x-on:click="$dispatch('close-modal', 'setting-modal')">
                    Zrušiť
                </x-secondary-button>
                <x-primary-button>
                    Uložiť
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
