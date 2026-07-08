<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Translation extends Model
{

	// Base table name
	protected $table = 'translations';

	// The attributes that are mass assignable.
	protected $fillable = [ 'key', 'lang', 'value', ];

	// Turn off tikmestamp columns
	public $timestamps = false;


	// Get translated value
	public static function translate($key, $flag=false): String {

		// Get model
		$actual = App::getLocale();
		$trans = self::where('key', strtolower($key))
			->where('lang', $actual)
			->first();

		// Check model
		if ( empty($trans) ) return '#' . mb_strtoupper($actual) . '.' . $key;
		else $translation = nl2br($trans->value);

		// Inline (non break space)
		if ( $flag == 'nbsp' )
			return str_replace(' ', '&nbsp;', $translation);

		// Return
		return $translation;
	}


}

