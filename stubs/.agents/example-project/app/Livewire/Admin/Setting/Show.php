<?php

namespace App\Livewire\Admin\Setting;

use Livewire\Component;
use App\Models\Setting;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Show extends Component
{
    public Setting $setting;

    public function delete()
    {
        $this->setting->delete();
        session()->flash('success', t('admin.deleted'));
        return redirect()->route('admin.setting.index');
    }

    public function render()
    {
        return view('livewire.admin.setting.show');
    }
}
