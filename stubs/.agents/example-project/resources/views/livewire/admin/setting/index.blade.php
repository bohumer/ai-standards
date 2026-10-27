<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900">{{ t('admin.setting.index.title') }}</h1>

        <a
            href="{{ route('admin.setting.create', array_filter(['flag' => $activeFlag, 'key' => $activeKey])) }}"
            wire:navigate
            class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700"
        >
            {{ t('admin.setting.index.add_new') }}
        </a>
    </div>

    <div class="flex flex-wrap gap-2">
        <button
            type="button"
            wire:click="$set('activeFlag', ''); $set('activeKey', '')"
            class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $activeFlag === '' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-700' }}"
        >
            {{ t('admin.common.all') }}
        </button>

        @foreach($structure as $flag => $keys)
            @foreach($keys as $key)
                <button
                    type="button"
                    wire:click="$set('activeFlag', '{{ $flag }}'); $set('activeKey', '{{ $key }}')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $activeFlag === $flag && $activeKey === $key ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-700' }}"
                >
                    {{ $flag }}.{{ $key }}
                </button>
            @endforeach
        @endforeach
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
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ t('admin.setting.fields.group') }}</th>
                    <th wire:click="sortBy('value')" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer">{{ t('admin.setting.fields.value') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ t('admin.setting.fields.label') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ t('admin.common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($settings as $item)
                    <tr
                        onclick="window.location='{{ route('admin.setting.show', $item) }}'"
                        class="hover:bg-gray-50 cursor-pointer transition-colors"
                    >
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->flag }}.{{ $item->key }}</td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $item->value }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->label }}</td>
                        <td class="px-6 py-4 text-right text-sm space-x-3" onclick="event.stopPropagation()">
                            <a href="{{ route('admin.setting.edit', $item) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 font-medium">
                                {{ t('admin.common.edit') }}
                            </a>
                            <button
                                type="button"
                                wire:click="$dispatch('confirm-setting-delete', { settingId: {{ $item->id }}, redirect: false })"
                                class="text-red-600 hover:text-red-900 font-medium"
                            >
                                {{ t('admin.common.delete') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">{{ t('admin.common.no_records') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $settings->links() }}</div>

    <livewire:admin.setting.delete />
</div>
