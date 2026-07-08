<?php

namespace App\Livewire\Admin\Translation;

use App\Http\Requests\Admin\Translation\FormRequest;
use App\Models\Translation;
use App\Support\BaseLivewireComponent;
use App\Traits\ValidationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layout.admin')]
class Edit extends BaseLivewireComponent
{
	use ValidationTrait;

	#[Url]
	public $lang;
	#[Url]
	public $key;
	public $value = '';
	public $model = null;
	protected static $formRequest = FormRequest::class;

	// _constructor
	public function mount($lang, $key) {

		// Set page title
		$this->setTitle(t('translation_edit'));

		// Store back URL
		$this->referrer = url()->previous();

		// Decode attributes from URL
		$this->lang = urldecode($lang);
		$this->key = urldecode($key);
	}

	// Render view
	public function render() {

		// Get / Create model
		$this->model = Translation::where('key', $this->key)
			->where('lang', $this->lang)
			->first();
		if ( empty($this->model) && !$this->lang_available() ) {
			$this->flash('error', 'translation_not_found');
			return $this->redirectBackWithParams('admin.translation.index');
		}

		// Render view
		$this->value = $this->model?->value;
		return view('livewire.admin.translation.form');
	}

	// Save translations
	public function save() {
		
		// Validation
		$this->validate();

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
		elseif ( !$this->lang_available() ) {
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
		$this->validate();
		$this->model->key = $this->key;
		$this->model->save();
		$this->flash('warning', 'translation_key_changed');
		return $this->redirectRoute('admin.translation.edit', [
			'lang' => $this->lang,
			'key' => $this->key,
		], navigate: true);
	}

}

