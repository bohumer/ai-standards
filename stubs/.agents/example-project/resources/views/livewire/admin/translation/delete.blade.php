
{{-- Delete Modal --}}
<div>
	@if ($showModal)
		<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
			wire:keydown.escape.window="closeModal"
			wire:click.self="closeModal">

			{{-- Modal container --}}
			<div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">

				{{-- Modal Header --}}
				<div class="flex items-center mb-4">
					<x-svg.base.warning class="w-8 h-8 text-red-500 mr-3" />
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">
						{{ t('confirm_deletion') }}
					</h3>
				</div>

				{{-- Modal Body --}}
				<div class="mb-6">
					<p class="text-gray-700 dark:text-gray-300">
						{{ t('are_you_sure_delete') }} 
						<strong>"{{ $translationKey }}"</strong>
						<br>
						({{ t('language_' . $translationLang) }})?
					</p>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
						{{ t('this_action_is_pemanent') }}
					</p>
				</div>

				{{-- Modal Footer --}}
				<div class="flex justify-end space-x-3">

					{{-- No Button --}}
					<x-form.button type="button" variant="secondary" wire:click="closeModal"
						:text="t('no')" :title="t('cancel')" />

					{{-- Yes Button --}}
					<x-form.button type="button" variant="danger" wire:click="deleteTranslation"
						:text="t('yes')" :title="t('confirm_deletion')" />
				</div>
			</div>
		</div>
	@endif
</div>
