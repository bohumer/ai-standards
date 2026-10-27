<div>
    @if($open)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
                <h2 class="text-lg font-bold text-gray-900">{{ t('admin.common.confirm_delete_title') }}</h2>
                <p class="text-sm text-gray-600">
                    {{ t('admin.setting.delete.confirm_text', ['label' => $settingLabel]) }}
                </p>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('open', false)" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700">
                        {{ t('admin.common.cancel') }}
                    </button>
                    <button type="button" wire:click="delete" wire:loading.attr="disabled" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium">
                        {{ t('admin.common.delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
