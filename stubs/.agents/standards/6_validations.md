# 6. Validácia
- **Validácia priamo v Livewire komponente (Odporúčané pre interaktívne UI):**
  - Pri Livewire komponentoch nepoužívajte triedy Form Request. Uprednostnite zabudovanú validáciu pomocou PHP 8 atribútov (`#[Validate]`) priamo na verejných vlastnostiach (public properties) komponentu.
  - S PHP 8.3 naplno využívajte striktné typovanie dát.
  - Pre chybové hlášky využite metódu `messages()` priamo v komponente, kde uplatníte vlastný prekladový helper `t()`.
  - Vyhnete sa tým zložitému prepojeniu Form Requestov so životným cyklom Livewire komponentu.

```php
namespace App\Livewire;

use Livewire\Attributes\Validate;
use Livewire\Component;

class UserProfile extends Component
{
    #[Validate('required|min:3')]
    public string $name = ''; 

    #[Validate('required|email')]
    public string $email = '';

    // Custom validation error messages using t() helper
    public function messages(): array {
        return [
            'name.required' => t('rule_name_required'),
        ];
    }

    public function save(): void {
        $this->validate();
        // Save logic...
    }
}
```

- **Validácia cez Form Request (pre štandardné API alebo non-Livewire POST požiadavky):**
  - Na validáciu vstupov namiesto písania logiky priamo v controlleri používajte triedy Form Request.
  - Tento prístup udržuje controllery čisté a zachováva oddelenie zodpovedností presunutím validačnej logiky do samostatnej triedy.
  - Vstupné dáta validujte pomocou vlastných request tried, ktoré sú odvodené od triedy `FormRequest`.

```php
namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
	public function rules(): array {
		return [
			'title' => [
				'required',
				'string',
				'max:255',
			],
		];
	}

	public function messages(): array {
		return [
			'title.required' => t('rule_title_required'),
		];
	}
}

// In controller:
public function store(ActivityRequest $request) {
	Activity::create($request->validated());
}
```

- **Používajte zabudované validačné pravidlá (Built-in Validation Rules)**
  - Kedykoľvek je to možné, vždy uprednostnite zabudované validačné pravidlá Laravelu.
  - Sú detailne otestované a pokrývajú väčšinu bežných situácií, ako napríklad `required`, `email`, `unique`, `exists` atď.

- **V prípade potreby vytvorte vlastné pravidlá (Custom Validation Rules)**
  - Pre jednorazovú špecifickú logiku vo vnútri konkrétneho FormRequestu použite metódu `withValidator`.
  - Pre globálne vlastné pravidlá použite `Validator::extend`.

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class CustomFormRequest extends FormRequest
{
	...
	public function withValidator(Validator $validator) {
		$validator->after(function ($validator) {
			if ($this->hasInvalidCustomLogic()) {
				$validator->errors()->add('custom_field', t('rule_custom_field_message'));
			}
		});
	}

	private function hasInvalidCustomLogic() {
		return $this->input('custom_field') === 'invalid';
	}
}
```

- Pre globálne pravidlá použite metódu `Validator::extend()`.
  - Fasádu Validator rozšírte v súbore `App\Providers\AppServiceProvider:`


```php
use Illuminate\Support\Facades\Validator;

public function boot()
{
	Validator::extend('custom_rule', function ($attribute, $value, $parameters, $validator) {
		return $value !== 'invalid';
	});
}
```

- **Vyhnite sa inline validácii v controlleroch.** Validačná logika by nemala zbytočne zahltiť metódy vášho controllera (v prostredí Livewire to čistým spôsobom riešia `#[Validate]` atribúty).
- Vždy sa uistite, že vlastná (custom) validačná logika je dostatočne otestovaná a dobre zdokumentovaná.
- **Používajte podmienečné pravidlá (Conditional Validation Rules).** Pre podmienenú validáciu používajte kľúčové slová ako `sometimes`, `required_if`, `required_unless` atď.
- **Validujte polia a vnorené údaje (Arrays and Nested Fields).** Na validáciu hodnôt v poli použite syntax `.*`.

```php
	'items.*.name' => 'required|string',
	'items.*.price' => 'required|numeric|min:0',
```

- **Validácia relačných dát.**
  - Na validáciu údajov voči existujúcim databázovým záznamom používajte pravidlá `exists` a `unique`.

```php
	'category_id' => 'required|exists:categories,id',
	'email' => 'required|email|unique:users,email',
```

- **Ochrana pred Mass Assignment zraniteľnosťami**
  - Na vytvorenie alebo úpravu modelov používajte výlučne zvalidované dáta.

```php
// Bad example:
User::create($request->all());

// Good example:
User::create($request->validated());
```


---
### Čo ďalej:
7. Pozrite si štandardy pre [Testovanie](https://git.greksak.sk/Michal/coding-standard/src/branch/main/docs/7_testing.md).
