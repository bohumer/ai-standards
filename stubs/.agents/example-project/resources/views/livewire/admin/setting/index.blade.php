<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ t('setting.index.title') }}</h1>
        <a href="{{ route('admin.setting.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">{{ t('admin.create') }}</a>
    </div>

    <!-- Search & Filter Area -->
    <div class="mb-4 flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ t('admin.search') }}" class="border rounded px-3 py-2 w-full md:w-1/3">
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-3 cursor-pointer" wire:click="sortBy('id')">ID</th>
                    <th class="p-3 cursor-pointer" wire:click="sortBy('flag')">Flag</th>
                    <th class="p-3 cursor-pointer" wire:click="sortBy('key')">Key</th>
                    <th class="p-3">Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($settings as $setting)
                <tr class="border-b hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.setting.show', $setting) }}'">
                    <td class="p-3">{{ $setting->id }}</td>
                    <td class="p-3">{{ $setting->flag }}</td>
                    <td class="p-3">{{ $setting->key }}</td>
                    <td class="p-3">{{ t($setting->flag . '.' . $setting->key . '.' . $setting->value) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $settings->links() }}
    </div>
</div>
