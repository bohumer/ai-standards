<!-- TODO PRE AI: Táto šablóna je úmyselne bez dizajnu a bez špecifických Blade komponentov! Musíš na ňu aplikovať Tailwind triedy (farby, tiene, zaoblenia) a nahradiť HTML tagy za existujúce Blade komponenty (napr. inputy, tlačidlá) tak, aby 100% ladili s UI dizajnom aktuálneho projektu! -->
<div>
    <div class="border p-4 mb-4">
        <a href="{{ route('admin.translation.index') }}" wire:navigate class="p-2 border">{{ t('back') }}</a>
    </div>

    <div class="border p-4">
        <h2>{{ $page_title }}</h2>

        <form wire:submit="save" class="mt-4 space-y-4">
            
            @if( $keyDisabled )
                <div>
                    <label>{{ t('key') }}</label>
                    <input type="text" wire:model.live="key" disabled class="border w-full">
                </div>
            @else
                <div>
                    <label>{{ t('key') }}</label>
                    <input type="text" wire:model.live="key" class="border w-full">
                    @error('key') <span>{{ $message }}</span> @enderror
                </div>
            @endif

            <div>
                <label>{{ t('language') }}</label>
                <select wire:model.live="lang" {{ $langDisabled ? 'disabled' : '' }} class="border w-full">
                    @foreach(config('app.langs_available') as $code => $name)
                        <option value="{{ $code }}">{{ t('language_' . $code) }}</option>
                    @endforeach
                </select>
                @error('lang') <span>{{ $message }}</span> @enderror
            </div>

            <div>
                <label>{{ t('value') }}</label>
                <textarea wire:model.live="value" class="border w-full" rows="4"></textarea>
                @error('value') <span>{{ $message }}</span> @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="p-2 border">{{ t('save') }}</button>
            </div>
        </form>
    </div>
</div>
