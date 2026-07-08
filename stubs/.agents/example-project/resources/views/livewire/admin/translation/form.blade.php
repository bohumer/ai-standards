
{{-- Title --}}
@push('title'){{ $page_title }} - @endpush

{{-- Translation Container --}}
<div class="container mx-auto p-4">

	{{-- Header --}}
	<div class="flex justify-between items-center mb-4">
		<h1 class="text-2xl font-bold">{{ t('translation_create') }}</h1>
		<a href="{{ route('admin.translation.index') }}" wire:navigate>
			<x-form.button type="button" :text="t('translation_index')" :title="t('translation_index')" />
		</a>
	</div>

	{{-- Translation form --}}
	<form id="admin-translation-form" wire:submit.prevent="save" class="max-w-lg">

		{{-- Translation key --}}
		<div class="translation-key mb-4">
			<x-form.input 
				id="translation_key" 
				name="key" 
				:value="$key" 
				:label="t('translation_key')" 
				wire:model="key" 
				wire:change="updateKey"
				required 
			/>
		</div>

		{{-- Translation Language --}}
		<div class="language mb-4 max-w-3xs">
			<x-form.dropdown
				name="lang"
				:value="$lang"
				:label="t('translation_language')"
				:options="config('app.langs_available')"
				:icons="collect(config('app.langs_available'))->mapWithKeys(fn($v, $k) => [$k => 'svg.flag.' . $k])->toArray()"
				required
				wire:model="lang"
			/>
		</div>

		{{-- Translation value --}}
		<div class="translation-value mb-4">
			<x-form.textarea name="value" :label="t('translation_value')" wire:model="value" rows="2" required />
		</div>

		{{-- Form buttons --}}
		<div class="flex space-x-3 space-between">
			<x-form.button type="button" variant="secondary" :text="t('cancel')" wire:click="cancel" />
			<x-form.button type="submit" :text="t('save')" :title="t('save')" />
		</div>
	</form>
</div>
