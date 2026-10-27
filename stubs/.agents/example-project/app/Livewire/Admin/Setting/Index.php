<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Setting;
use App\Support\BaseLivewireComponent;
use App\Traits\WithUniversalTable;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Index extends BaseLivewireComponent
{
    use WithPagination;
    use WithUniversalTable;

    /**
     * Example dictionary groups for the reference project.
     * In real projects, define only flags and keys matching actual project models.
     */
    protected array $settingsStructure = [
        'product' => ['category'],
        'system' => ['language'],
    ];

    public string $activeFlag = '';
    public string $activeKey = '';

    public function mount(?string $flag = null, ?string $key = null): void
    {
        $this->setTitle(t('admin.setting.index.title'));
        $this->activeFlag = $flag ?? '';
        $this->activeKey = $key ?? '';
        $this->initFilters(Setting::class, ['delete']);
    }

    #[On('setting-deleted')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Setting::query();

        if ($this->activeFlag !== '') {
            $query->where('flag', $this->activeFlag);
        }
        if ($this->activeKey !== '') {
            $query->where('key', $this->activeKey);
        }

        return view('livewire.admin.setting.index', [
            'settings' => $this->applyUniversalSearchAndSort($query)->paginate($this->getPerPageLimit()),
            'structure' => $this->settingsStructure,
        ]);
    }
}
