<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.translation.index') }}" wire:navigate class="text-sm text-gray-500 hover:text-gray-800">
                &larr; {{ t('admin.common.back_to_list') }}
            </a>
            <h1 class="mt-1 text-2xl font-bold font-mono text-gray-900">{{ $translationKey }}</h1>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('admin.translation.edit', ['key' => $translationKey]) }}"
                wire:navigate
                class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700"
            >
                {{ t('admin.common.edit') }}
            </a>
            <button
                type="button"
                wire:click="$dispatch('confirm-translation-delete', { key: '{{ addslashes($translationKey) }}', redirect: true })"
                class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700"
            >
                {{ t('admin.common.delete') }}
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        @foreach($languages as $lang)
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase">{{ strtoupper($lang) }}</dt>
                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $this->values[$lang] ?? '-' }}</dd>
            </div>
        @endforeach
    </div>

    <livewire:admin.translation.delete />
</div>
