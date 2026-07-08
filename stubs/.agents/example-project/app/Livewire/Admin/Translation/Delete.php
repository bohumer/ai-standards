<?php

namespace App\Livewire\Admin\Translation;

use App\Models\Translation;
use App\Support\BaseLivewireComponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Livewire\Attributes\On;

class Delete extends BaseLivewireComponent
{
	public bool $showModal = false;
	public ?string $translationKey = null;
	public ?string $translationLang = null;

	// Render view
	public function render(): View {

		// Store back URL
		$this->referrer = url()->previous();
		return view('livewire.admin.translation.delete');
	}

	// Show modal
	#[On('confirm-delete')]
	public function confirmDelete(string $key, string $lang): void {
		$this->translationKey = $key;
		$this->translationLang = $lang;
		$this->showModal = true;
	}

	// Delete the specified translation
	public function deleteTranslation() {

		// Try to find and delete item
		if ( $this->translationKey && $this->translationLang ) {
			Translation::where('key', $this->translationKey)
				->where('lang', $this->translationLang)
				->delete();
			$this->flash('success', 'translation_deleted_successfully');
		} else $this->flash('error', 'translation_not_found');

		// Emit event to parent/listener
		$this->closeModal();
		return $this->redirectBackWithParams('admin.translation.index');
	}

	// Cancel the deletion and close the modal.
	public function closeModal(): void {
		$this->showModal = false;
		$this->translationKey = null;
		$this->translationLang = null;
	}


}

