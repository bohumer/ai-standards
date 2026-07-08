<?php

namespace App\Livewire\Admin\Translation;

use App\Http\Requests\Admin\Translation\IndexRequest;
use App\Models\Translation;
use App\Support\BaseLivewireComponent;
use App\Traits\ValidationTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layout.admin')]
class Index extends BaseLivewireComponent
{
	use ValidationTrait, WithPagination;

	#[Url]
	public $search = '';
	#[Url]
	public $sortField = 'key';
	#[Url]
	public $sortDirection = 'asc';
	protected static $formRequest = IndexRequest::class;

	// _constructor
	public function mount() {

		// Set page title
		$this->setTitle(t('translations'));
	}

	// Render the translations index view
	public function render(): View {

		// Get all translations, optionally filtered by search
		$query = Translation::query();
		if ( $this->search ) {
			$matchingKeys = Translation::where('key', 'like', '%' . $this->search . '%')
				->orWhere('value', 'like', '%' . $this->search . '%')
				->pluck('key')->unique();

			// No results, return empty paginator
			if ( $matchingKeys->isEmpty() ) {
				$items = new LengthAwarePaginator([], 0, 100, $this->getPage(), [
						'path' => request()->url(),
						'pageName' => 'page',
					]
				);
				return view('livewire.admin.translation.index', [
					'items' => $items,
				]);
			}

			// Query with results
			$query->whereIn('key', $matchingKeys);
		}

		// Structure data for the view
		$structuredItems = [];
		$grouped = $query->get()->groupBy('key');
		foreach ($grouped as $key => $translations) {
			$values = $translations->pluck('value', 'lang');
			$structuredItems[$key] = [
				'key' => $key,
				'present_langs' => $values->keys()->toArray(),
				'values' => $values->toArray(),
			];
		}

		// Sort items
		if ( $this->sortField === 'key' ) {
			$this->sortDirection === 'asc' ? ksort($structuredItems) : krsort($structuredItems);
		} elseif ( $this->sortField === 'value' ) {
			$currentLocale = app()->getLocale();
			uasort($structuredItems, function ($a, $b) use ($currentLocale) {
				$valA = $a['values'][$currentLocale] ?? '';
				$valB = $b['values'][$currentLocale] ?? '';
				return $this->sortDirection === 'asc' ? strcmp($valA, $valB) : strcmp($valB, $valA);
			});
		}

		// Manually paginate the array
		$perPage = 100;
		$currentPage = $this->getPage();
		$totalItems = count($structuredItems);
		$currentPageItems = array_slice($structuredItems, ($currentPage - 1) * $perPage, $perPage, true);
		$items = new LengthAwarePaginator( $currentPageItems, $totalItems, $perPage, $currentPage, [
				'path' => request()->url(),
				'pageName' => 'page',
			]
		);

		// If requested page is out of range, reset to last page
		if ($items->lastPage() > 0 && $items->currentPage() > $items->lastPage()) {
			$this->setPage($items->lastPage());
			$currentPage = $this->getPage();
			$currentPageItems = array_slice($structuredItems, ($currentPage - 1) * $perPage, $perPage, true);
			$items = new LengthAwarePaginator($currentPageItems, $totalItems, $perPage, $currentPage, [
				'path' => request()->url(),
				'pageName' => 'page',
			]);
		}

		// Return view with modal state
		return view('livewire.admin.translation.index', [
			'items' => $items,
		]);
	}

	// Reset page when search changes
	public function updatedSearch() {
		$this->resetPage();
	}

	// Reset filters
	public function resetFilters() {
		$this->search = '';
		$this->resetPage();
	}

	// Sort by field
	public function sortBy($field) {
		if ( !in_array($field, ['key', 'value']) ) {
			return;
		} elseif ($this->sortField === $field) {
			$this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
		} else {
			$this->sortField = $field;
			$this->sortDirection = 'asc';
		}
		$this->resetPage();
	}


}

