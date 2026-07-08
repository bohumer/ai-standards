<?php

namespace App\Livewire\Admin\Translation;

use App\Http\Requests\Admin\Translation\FormRequest;
use App\Models\Translation;
use App\Support\BaseLivewireComponent;
use App\Traits\ValidationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

#[Layout('layout.admin')]
class Create extends BaseLivewireComponent
{
	use ValidationTrait;

	public $lang = 'sk';
	public $key = '';
	public $value = '';
	public $model = null;
	protected static $formRequest = FormRequest::class;

	// _constructor
	public function mount() {

		// Set page title
		$this->setTitle(t('translation_create'));

		// Store back URL
		$this->referrer = url()->previous();
	}

	// Render view
	public function render(): View {
		return view('livewire.admin.translation.form');
	}

	// Save new translation
	public function save() {

		// Validation Trait
		$this->key = $this->sanitizeKey($this->key);
		$this->validate();

		// Check if Translation already exists
		$this->model = Translation::where('key', $this->key)
			->where('lang', $this->lang)
			->first();

		// Create model
		if ( empty($this->model) ) {
			$this->model = Translation::create([
				'key' => $this->key,
				'lang' => $this->lang,
				'value' => $this->value,
			]);
			$this->flash('success', 'translation_created_successfully');
			return $this->redirectBackWithParams('admin.translation.index');
		}

		// Unavailable lang provided
		if ( !$this->lang_available() ) {
			$this->flash('error', 'translation_not_found');
			return $this->redirectBackWithParams('admin.translation.index');
		}

		// Update model value
		$this->model->value = $this->value;
		$this->model->save();
		$this->flash('success', 'translation_updated_successfully');
		return $this->redirectBackWithParams('admin.translation.index');
	}

	// Cancel and go back
	public function cancel() {
		return $this->redirectBackWithParams('admin.translation.index');
	}

	// Check if lang is available
	public function lang_available() {
		$langs = config('app.langs_available'); // only available languages
		return in_array($this->lang, array_keys($langs));
	}

	// On key change
	public function updateKey() {
		$this->key = $this->sanitizeKey($this->key);
		$model = Translation::where('key', $this->key)
			->where('lang', $this->lang)
			->first();
		if ( empty($model) ) return;
		$this->flash('warning', 'translation_key_exists');
		return $this->redirectRoute('admin.translation.edit', [
			'lang' => $this->lang,
			'key' => $this->key,
		], navigate: true);

	}

}

