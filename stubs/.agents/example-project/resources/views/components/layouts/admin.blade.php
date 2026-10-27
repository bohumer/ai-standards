<!DOCTYPE html>
<html lang="{{ app_locale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ !empty($page_title) ? $page_title . ' | ' . config('app.name') : config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    {{-- Global toast notification listener for Livewire flash-message events and session flashes --}}
    <div
        x-data="{
            toasts: @js(session()->pull('flash_messages', [])),
            add(e) {
                this.toasts.push({ id: e.detail.id || Date.now(), type: e.detail.type, text: e.detail.text });
                setTimeout(() => this.toasts.shift(), 4500);
            }
        }"
        x-on:flash-message.window="add($event)"
        class="fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"
    >
        <template x-for="(toast, index) in toasts" :key="index">
            <div
                class="pointer-events-auto rounded-lg px-4 py-3 text-sm font-medium shadow-lg border"
                :class="toast.type === 'error'
                    ? 'bg-red-50 text-red-800 border-red-200'
                    : 'bg-emerald-50 text-emerald-800 border-emerald-200'"
                x-text="toast.text"
            ></div>
        </template>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    @stack('scripts')
</body>
</html>
