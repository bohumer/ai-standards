<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.setting.index') }}" wire:navigate class="text-sm text-gray-500 hover:text-gray-800">
                &larr; {{ t('admin.common.back_to_list') }}
            </a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $this->setting->label }}</h1>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('admin.setting.edit', $this->setting) }}"
                wire:navigate
                class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700"
            >
                {{ t('admin.common.edit') }}
            </a>

            <button
                type="button"
                wire:click="$dispatch('confirm-setting-delete', { settingId: {{ $this->setting->id }}, redirect: true })"
                class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700"
            >
                {{ t('admin.common.delete') }}
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase">Flag</dt>
                <dd class="mt-1 text-sm font-mono text-gray-900">{{ $this->setting->flag }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase">Key</dt>
                <dd class="mt-1 text-sm font-mono text-gray-900">{{ $this->setting->key }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase">{{ t('admin.setting.fields.value') }}</dt>
                <dd class="mt-1 text-sm font-mono text-gray-900">{{ $this->setting->value }}</dd>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($languages as $lang)
                <div>
                    <dt class="text-xs font-semibold text-gray-500 uppercase">{{ t('admin.setting.fields.label') }} ({{ strtoupper($lang) }})</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $this->setting->getTranslations('label')[$lang] ?? '-' }}</dd>
                </div>
            @endforeach
        </div>
    </div>

    <livewire:admin.setting.delete />
</div>
