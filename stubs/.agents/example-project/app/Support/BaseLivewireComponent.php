<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class BaseLivewireComponent extends Component
{
    public string $page_title = '';
    public $referrer = false;

    /**
     * Set page Title
     */
    public function setTitle($title)
    {
        $this->page_title = $title;
    }

    /**
     * Store log info into database
     */
    public function log($level, $key = null, $message = null, $link = null)
    {
        $data = (object) [];
        if (is_array($level)) {
            $data = (object) $level;
        }

        $location = request()->fullUrl();
        
        // Handle Livewire update requests to log the originating page instead
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
        ]);
    }

    /**
     * Store flash message in session and dispatch event
     */
    public function flash($type, $key = false, $message = false, $link = null)
    {
        if (!$key && !$message) {
            throw new \Exception("Error Processing Flash message", 1);
        }

        $messages = Session::get('flash_messages', []);

        switch ($type) {
            case 'success':
                $level = "1";
                break;
            case 'info':
                $level = "2";
                break;
            case 'warning':
                $level = "3";
                break;
            case 'error':
                $level = "4";
                break;
            default:
                $level = "5";
                break;
        }

        if ($key && !$message) {
            $message = t($key);
        }

        $messages[] = ['type' => $type, 'text' => $message];
        Session::put('flash_messages', $messages);

        $id = uniqid('', true);
        $this->dispatch('flash-message', id: $id, type: $type, text: $message);

        $this->log(compact('level', 'key', 'message', 'link'));
    }

    public function flashSuccess($message)
    {
        $this->flash('success', message: $message);
    }

    public function flashError($message)
    {
        $this->flash('error', message: $message);
    }

    /**
     * Redirect back with preserved filters
     */
    public function redirectBackWithParams($routeName)
    {
        $referrer = $this->referrer;
        if ($referrer && str_contains($referrer, route($routeName))) {
            return $this->redirect($referrer, navigate: true);
        }

        return $this->redirect(route($routeName), navigate: true);
    }

    /**
     * Reset error for specific field
     */
    public function resetError($field): void
    {
        $this->resetErrorBag($field);
    }
}
