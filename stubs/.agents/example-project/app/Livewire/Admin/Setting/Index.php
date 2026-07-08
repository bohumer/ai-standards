<?php

namespace App\Livewire\Admin\Setting;

use Livewire\Component;
use App\Models\Setting;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Index extends Component
{
    use \Livewire\WithPagination;
    use \App\Livewire\Traits\WithUniversalTable;

    public function mount()
    {
        $this->initFilters(Setting::class);
    }

    public function render()
    {
        return view('livewire.admin.setting.index', [
            'settings' => $this->applyUniversalSearchAndSort(Setting::query())->paginate(20),
        ]);
    }
}
