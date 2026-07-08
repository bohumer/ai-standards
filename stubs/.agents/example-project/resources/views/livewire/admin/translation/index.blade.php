<!-- TODO PRE AI: Táto šablóna je úmyselne bez dizajnu a bez špecifických Blade komponentov! Musíš na ňu aplikovať Tailwind triedy (farby, tiene, zaoblenia) a nahradiť HTML tagy za existujúce Blade komponenty (napr. tlačidlá, inputy, ikony) tak, aby 100% ladili s UI dizajnom aktuálneho projektu! -->
<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
        <h1>{{ $page_title }}</h1>
        <a href="{{ route('admin.translation.create') }}" wire:navigate class="p-2 border">
            {{ t('Translation_create') }}
        </a>
    </div>

    {{-- Search filter --}}
    <div class="mb-4 flex items-center space-x-2">
        <input type="text" wire:model.live.debounce.500ms="search" placeholder="{{ t('translations_search') }}" class="border p-2">
        @if( $search )
            <button wire:click="resetFilters" class="p-2 border">{{ t('search_clear') }}</button>
        @endif
    </div>

    {{-- Translations Empty --}}
    @if( $items->isEmpty() )
        <div class="p-4 text-center border">
            <h2>{{ t('translations_not_found') }}</h2>
            @if( $search )
                <h3>{{ t('items_not_found_message') }}</h3>
            @endif
        </div>
    @else
        {{-- Pagination Top --}}
        <!-- Vlož štandardné stránkovanie pre projekt -->

        {{-- Translations table --}}
        <div class="overflow-x-auto border">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="p-4 border-b">{{ t('languages') }}</th>
                        <th class="p-4 border-b cursor-pointer" wire:click="sortBy('key')">
                            {{ t('key') }} @if($sortField === 'key') {{ $sortDirection === 'asc' ? '▲' : '▼' }} @endif
                        </th>
                        <th class="p-4 border-b cursor-pointer" wire:click="sortBy('value')">
                            {{ t('value') }} @if($sortField === 'value') {{ $sortDirection === 'asc' ? '▲' : '▼' }} @endif
                        </th>
                        <th class="p-4 border-b">{{ t('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td class="p-4 border-b">
                                <div class="flex items-center space-x-2">
                                    @foreach( array_keys(config('app.langs_available')) as $langCode )
                                        @php
                                            $hasTranslation = in_array($langCode, $item['present_langs']);
                                            $editTitle = $hasTranslation ? t('translation_edit_for_' . $langCode) : t('translation_create_for_' . $langCode);
                                        @endphp
                                        <a href="{{ route('admin.translation.edit', ['lang' => $langCode, 'key' => $item['key']]) }}" wire:navigate title="{{ $editTitle }}">
                                            {{ strtoupper($langCode) }} {{ $hasTranslation ? '(✓)' : '(+)' }}
                                        </a>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-4 border-b">{{ $item['key'] }}</td>
                            <td class="p-4 border-b" title="{{ $item['key'] }}">{{ Str::limit(t($item['key']), 70) }}</td>
                            <td class="p-4 border-b">
                                @if(in_array(app()->getLocale(), $item['present_langs']))
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.translation.edit', [ 'lang' => app()->getLocale(), 'key' => $item['key'] ]) }}" wire:navigate title="{{ t('edit') }}">
                                            Upraviť
                                        </a>
                                        <button wire:click="$dispatch('confirm-delete', { key: '{{ $item['key'] }}', lang: '{{ app()->getLocale() }}' })" title="{{ t('delete') }}">
                                            Zmazať
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination Bottom --}}
        <!-- Vlož štandardné stránkovanie pre projekt -->
    @endif

    {{-- Delete Confirmation Component --}}
    @livewire('admin.translation.delete')
</div>
