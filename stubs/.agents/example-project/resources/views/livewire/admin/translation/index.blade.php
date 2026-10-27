<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.translation.index.title') }}</h1>

        <a
            href="{{ route('admin.translation.create') }}"
            wire:navigate
            class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700"
        >
            {{ t('admin.translation.index.add_new') }}
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <input
            type="search"
            wire:model.live.debounce.300ms="search"
            placeholder="{{ t('admin.common.search_placeholder') }}"
            class="w-full sm:w-80 rounded-lg border-gray-300 text-sm"
        />
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="toggleSort" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer">
                        Key
                    </th>
                    @foreach($languages as $lang)
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ strtoupper($lang) }}</th>
                    @endforeach
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ t('admin.common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($paginatedKeys as $row)
                    <tr
                        onclick="window.location='{{ route('admin.translation.show', ['key' => $row->key]) }}'"
                        class="hover:bg-gray-50 cursor-pointer transition-colors"
                    >
                        <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $row->key }}</td>
                        @foreach($languages as $lang)
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $translations[$row->key][$lang] ?? '-' }}
                            </td>
                        @endforeach
                        <td class="px-6 py-4 text-right text-sm space-x-3" onclick="event.stopPropagation()">
                            <a href="{{ route('admin.translation.edit', ['key' => $row->key]) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 font-medium">
                                {{ t('admin.common.edit') }}
                            </a>
                            <button
                                type="button"
                                wire:click="$dispatch('confirm-translation-delete', { key: '{{ addslashes($row->key) }}', redirect: false })"
                                class="text-red-600 hover:text-red-900 font-medium"
                            >
                                {{ t('admin.common.delete') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($languages) + 2 }}" class="px-6 py-10 text-center text-sm text-gray-500">
                            {{ t('admin.common.no_records') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $paginatedKeys->links() }}</div>

    <livewire:admin.translation.delete />
</div>
