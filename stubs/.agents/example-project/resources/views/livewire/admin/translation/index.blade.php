
{{-- Title --}}
@push('title'){{ $page_title }} - @endpush

{{-- Translation Index --}}
<div class="relative container mx-auto p-4">

	{{-- Header --}}
	<div class="flex justify-between items-center mt-2 mb-4">
		<h1 class="text-h1 font-bold">{{ $page_title }}</h1>
		<a href="{{ route('admin.translation.create') }}" wire:navigate>
			<x-form.button type="button" :text="t('Translation_create')" :title="t('translation_create')" />
		</a>
	</div>

	{{-- Search filter --}}
	<div class="mb-4 flex items-center space-x-2">

		{{-- Search input --}}
		<div class="flex w-full md:w-1/2">
			<x-form.input id="translation_index_search" name="search" wire:model.live.debounce.500ms="search" :placeholder="t('translations_search')"
				:showLabel="false" :showIcon="false" :showError="false" :resetErrorOnFocus="false" autocomplete="off" />
		</div>

		{{-- Reset button --}}
		@if( $search )
			<x-svg.menu.close size="22" :base="false"
				class="rounded transition duration-75 cursor-pointer hover:bg-white dark:hover:bg-dark"
				wire:click="resetFilters" :title="t('search_clear')" />
		@endif
	</div>

	{{-- Translations Empty --}}
	@if( $items->isEmpty() )
		<div class="flex flex-col items-center justify-center py-16 px-4">

			{{-- Large translate icon --}}
			<x-svg.menu.translate class="w-24 h-24 text-gray-400 dark:text-gray-600 mb-6" />
			
			{{-- Empty state text --}}
			<h2 class="text-h2 font-semibold text-gray-700 dark:text-gray-300 mb-2">
				{{ t('translations_not_found') }}
			</h2>

			{{-- Search no result message --}}
			@if( $search )
				<h3 class="text-h3 text-gray-500 dark:text-gray-400 text-center max-w-md mb-6">
					{{ t('items_not_found_message') }}
				</h3>
			@endif
		</div>
	@else

		{{-- Pagination --}}
		@include('layout.paginate-top')

		{{-- Translations container --}}
		<div class="overflow-x-auto">
			<table class="w-full table-auto bg-white dark:bg-dark rounded">

				{{-- Table header --}}
				<thead class="text-h3">
					<tr class="text-left border-b border-gray-500">
						<th class="px-4 py-2 select-none">{{ t('languages') }}</th>
						<th class="px-4 py-2 cursor-pointer select-none" wire:click="sortBy('key')">
							{{ t('key') }}&nbsp;
							@if($sortField === 'key')
								<span>{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
							@endif
						</th>
						<th class="px-4 py-2 cursor-pointer select-none" wire:click="sortBy('value')">
							{{ t('value') }}&nbsp;
							@if($sortField === 'value')
								<span>{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
							@endif
						</th>
						<th class="px-4 py-2 select-none">{{ t('actions') }}</th>
					</tr>
				</thead>

				{{-- Table body --}}
				<tbody>
					@foreach($items as $item)
						<tr class="hover:bg-light dark:hover:bg-darker border-b border-gray-300 dark:border-gray-700">

							{{-- Languages --}}
							<td class="px-4">
								<div class="flex items-center space-x-2">
									@foreach( array_keys(config('app.langs_available')) as $langCode )

										{{-- Flag Title --}}
										@php
											$hasTranslation = in_array($langCode, $item['present_langs']);
											$editTitle = $hasTranslation 
												? t('translation_edit_for_' . $langCode)
												: t('translation_create_for_' . $langCode);
											$opacityClasses = $hasTranslation
												? 'opacity-70 hover:opacity-100'
												: 'opacity-20 hover:opacity-70';
										@endphp

										{{-- Flag Link --}}
										<a href="{{ route('admin.translation.edit', ['lang' => $langCode, 'key' => $item['key']]) }}" wire:navigate 
											class="inline-block {{ $opacityClasses }} transition-opacity duration-200 cursor-pointer">
											<x-dynamic-component :component="'svg.flag.' . $langCode" 
												class="w-9 px-2 p-2 rounded-sm cursor-pointer hover:bg-white dark:hover:bg-gray-700" 
												:title="$editTitle" :style="$hasTranslation ? '' : 'filter: grayscale(100%);'"
											/>
										</a>
									@endforeach
								</div>
							</td>

							{{-- Key --}}
							<td class="px-4">{{ $item['key'] }}</td>

							{{-- Value --}}
							<td class="px-4" title="{{ $item['key'] }}">
								{{ Str::limit(t($item['key']), 70) }}
							</td>

							{{-- Action buttons --}}
							<td class="px-4">

								{{-- Edit and Delete buttons --}}
								@if(in_array(app()->getLocale(), $item['present_langs']))
									<div class="flex items-center space-x-2">

										{{-- Edit --}}
										<a href="{{ route('admin.translation.edit', [ 'lang' => app()->getLocale(), 'key' => $item['key'] ]) }}" 
											class="text-hover hover:text-theme" title="{{ t('edit') }}" wire:navigate >
											<x-svg.base.edit size="small" class="m-2" />
										</a>

										{{-- Delete --}}
										<span title="{{ t('delete') . ' (' . t('language_' . app()->getLocale()) . ')' }}"
											class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300"
											wire:click="$dispatch('confirm-delete', { key: '{{ $item['key'] }}', lang: '{{ app()->getLocale() }}' })">
										<x-svg.base.delete size="small" class="m-2" />
										</span>
									</div>
								@endif
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		{{-- Pagination --}}
		<div class="mt-4">
			@include('layout.paginate-bottom')
		</div>
	@endif

	{{-- Delete Confirmation Component --}}
	@livewire('admin.translation.delete')
</div>
