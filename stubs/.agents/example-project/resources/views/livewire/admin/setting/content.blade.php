<div class="space-y-8">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center mb-2">
                <a href="{{ route('admin.settings.index') }}" wire:navigate class="text-slate-400 hover:text-af-blue transition flex items-center text-sm font-bold uppercase tracking-wider">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Späť na nastavenia
                </a>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase">Nastavenia obsahu</h1>
            <p class="text-slate-500 font-medium">Upravte texty na statických podstránkach webu.</p>
        </div>
        <x-form.button wire:click="save" variant="primary" text="Uložiť zmeny" icon="hero-check" />
    </div>

    {{-- Tabs --}}
    <div class="flex space-x-2 p-1 bg-slate-100 rounded-2xl w-fit">
        @foreach($pages as $id => $page)
            <button wire:click="$set('activeTab', '{{ $id }}')" 
                @class([
                    'px-6 py-2.5 rounded-xl text-sm font-bold transition-all',
                    'bg-white shadow-sm text-slate-900' => $activeTab === $id,
                    'text-slate-500 hover:text-slate-700' => $activeTab !== $id,
                ])>
                {{ $page['name'] }}
            </button>
        @endforeach
    </div>

    {{-- Content --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 md:p-12 space-y-12">
            @php $activePage = $pages[$activeTab]; @endphp

            @foreach($activePage['keys'] as $key => $config)
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                            <span class="text-[10px] font-black">{{ strtoupper(substr($activeTab, 0, 1)) }}</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">{{ $config['name'] }}</h3>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        @foreach($languages as $lang)
                            <div class="space-y-2">
                                <label class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">
                                    <span @class([
                                        'w-5 h-3 rounded-sm',
                                        'bg-blue-600' => $lang == 'sk',
                                        'bg-blue-900' => $lang == 'fr',
                                        'bg-red-600' => $lang == 'en',
                                    ])></span>
                                    <span>{{ strtoupper($lang) }}</span>
                                </label>

                                @if($config['type'] == 'input')
                                    <input type="text" wire:model="translations.{{ $key }}.{{ $lang }}"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-slate-100 bg-slate-50 focus:border-blue-400 focus:bg-white outline-none transition-all text-sm font-medium">
                                @elseif($config['type'] == 'textarea')
                                    <textarea wire:model="translations.{{ $key }}.{{ $lang }}" rows="4"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-slate-100 bg-slate-50 focus:border-blue-400 focus:bg-white outline-none transition-all text-sm font-medium"></textarea>
                                @elseif($config['type'] == 'editor')
                                    <x-form.text-editor 
                                        :id="'editor-' . $key . '-' . $lang"
                                        :name="'translations.' . $key . '.' . $lang"
                                        wire:model="translations.{{ $key }}.{{ $lang }}"
                                    />
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                
                @if(!$loop->last)
                    <div class="h-px bg-slate-100"></div>
                @endif
            @endforeach
        </div>
        
        {{-- Footer Save Bar --}}
        <div class="bg-slate-50 p-6 flex justify-end border-t border-slate-100">
            <x-form.button wire:click="save" variant="primary" text="Uložiť všetky zmeny" />
        </div>
    </div>
</div>
