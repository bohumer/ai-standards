<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Locked;
use Livewire\Component;

class BaseLivewireComponent extends Component
{
    #[Locked]
    public string $page_title = '';

    #[Locked]
    public ?string $referrer = null;

    /**
     * Set dynamic page title rendered by the layout.
     */
    public function setTitle(string $title): void
    {
        $this->page_title = $title;
    }

    /**
     * Write an audit log entry into the logs table.
     */
    public function log(string|array $level, ?string $key = null, ?string $message = null, ?string $link = null): void
    {
        $data = is_array($level) ? (object) $level : (object) [];

        $location = request()->fullUrl();
        if (request()->routeIs('livewire.update') || request()->routeIs('*.livewire.update')) {
            $location = url()->previous();
        }
        $location = str_replace(url('/'), '', $location);

        DB::table('logs')->insert([
            'level' => $data->level ?? $level,
            'user_id' => Auth::id() ?? 0,
            'key' => $data->key ?? $key,
            'link' => $data->link ?? $link,
            'message' => $data->message ?? $message,
            'location_from' => ltrim($location, '/'),
            'ip' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Store flash message in session, dispatch UI event, and write audit log.
     */
    public function flash(string $type, string|false $key = false, string|false $message = false, ?string $link = null): void
    {
        if (! $key && ! $message) {
            throw new \InvalidArgumentException('Flash message requires either a translation key or a message string.');
        }

        $level = match ($type) {
            'success' => '1',
            'info' => '2',
            'warning' => '3',
            'error' => '4',
            default => '5',
        };

        if ($key && ! $message) {
            $message = t($key);
        }

        $messages = Session::get('flash_messages', []);
        $messages[] = ['type' => $type, 'text' => $message];
        Session::put('flash_messages', $messages);
        Session::flash('status', $message);

        $this->dispatch('flash-message', id: uniqid('', true), type: $type, text: $message);

        $this->log([
            'level' => $level,
            'key' => $key ?: null,
            'message' => $message,
            'link' => $link,
        ]);
    }

    public function flashSuccess(string $message): void
    {
        $this->flash('success', message: $message);
    }

    public function flashError(string $message): void
    {
        $this->flash('error', message: $message);
    }

    /**
     * Safely redirect back to the index route while preserving query parameters.
     */
    public function redirectBackWithParams(string $routeName): mixed
    {
        $expectedBase = route($routeName);

        if ($this->referrer && str_starts_with($this->referrer, $expectedBase)) {
            return $this->redirect($this->referrer, navigate: true);
        }

        return $this->redirect($expectedBase, navigate: true);
    }

    public function resetError(string $field): void
    {
        $this->resetErrorBag($field);
    }
}
