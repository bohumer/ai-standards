# 7. Testovanie
- **Píšte Unit aj Feature testy**
  - **Unit (jednotkové) testy** využívajte najmä na detailné otestovanie každého jedného poľa formulára – či do neho povoľujeme vkladať správne formáty a či aplikácia vráti správnu validačnú chybu pri zadaní neplatných údajov.
  - **Feature testy** používajte na testovanie ucelených procesov (flows) zahŕňajúcich cesty, controllery, pohľady a middlewary, čím sa zabezpečí správne fungovanie aplikácie ako celku.

- **Testovanie interaktívnych Livewire komponentov:** 
  - Na testovanie Livewire komponentov používajte špecializovaný helper `Livewire::test()` namiesto klasických HTTP požiadaviek. 
  - Tento nástroj vám umožňuje plnohodnotne otestovať stav komponentu, jeho validáciu aj konkrétne akcie, a to bez nutnosti vykonávať klasické HTTP volania. Sústreďte sa na testovanie každej verejnej (public) metódy komponentu.

```php
// Testing Livewire form validation
Livewire::test(ContactForm::class)
    ->set('email', '')
    ->call('submit')
    ->assertHasErrors(['email' => 'required']);

// Actions and state changes
Livewire::test(Counter::class)
    ->call('increment')
    ->assertSet('count', 1);
```

- **Používajte popisné názvy testovacích metód**
  - Metódy pomenúvajte jasne a popisne, aby z nich bol hneď zrejmý účel samotného testu.

```php
public function test_user_can_login_with_valid_credentials() {
    // Test logic
}
```

- **Testy organizujte do logických adresárov**
  - Priečinky s testami organizujte tak, aby zrkadlili štruktúru vašej aplikácie.
  - Zamerajte sa hlavne na štruktúrovanie podľa modelov.
    Napríklad: `tests/Feature/UserTest.php`, `tests/Feature/PostTest.php` atď.

- **Využívajte zabudované testovacie funkcie Laravelu**
  - Pre redukciu opakujúceho sa kódu (boilerplate) používajte pri štandardných (non-Livewire) požiadavkách zabudované helpery Laravelu.

```php
$this->post('/login', [
	'email' => 'test@example.com',
	'password' => 'password',
])->assertStatus(200);
```

- **Vyhnite sa natvrdo napísaným dátam (Hardcoding)**
  - Na rýchle a efektívne generovanie testovacích údajov používajte model factories.
  - Namiesto vpisovania presných hodnôt používajte factories, seedery alebo generovanie dynamických dát.

```php
$user = User::factory()->create();
$this->actingAs($user)->get('/dashboard')->assertStatus(200);

// Bad example:
$this->post('/login', [
    'email' => 'fixed@example.com',
    'password' => 'password',
])->assertStatus(200);

// Good example:
$user = User::factory()->create();
$this->post('/login', [
    'email' => $user->email,
    'password' => 'password',
])->assertStatus(200);
```

- **Mockovanie externých závislostí**
  - V testoch nikdy nereálne netestujte API tretích strán alebo externé služby. Namiesto toho používajte "mocky" na ich simuláciu.
  - Uistite sa, že všetky požiadavky využívajúce HTTP Clienta [boli nahradené (faked)](https://laravel.com/docs/11.x/http-client#preventing-stray-requests).
  - Takisto nahradzujte úlohy v radoch (queued processes), aby sa len overilo ich správne odoslanie, ale nevykonali sa reálne.

```php
Http::fake([
	'api.example.com/*' => Http::response(['data' => 'mocked'], 200),
]);
$response = Http::get('https://api.example.com/data');
$response->assertOk();
```

- **Píšte testy pre každú metódu controllera.**
  - **Testovanie validačnej logiky.** Overte, či aplikácia správne zachytáva a zvláda validačné chyby.
  - **Testovanie pozitívnych aj negatívnych scenárov.** Napíšte testy pre úspešné (validné) aj chybné (nevalidné) situácie.

```php
// Positive tests:
$this->post('/register', [
	'email' => '',
])->assertSessionHasErrors('email');

$this->post('/login', [
    'email' => 'test@example.com',
    'password' => 'correct-password',
])->assertRedirect('/dashboard');

// Negative test:
$this->post('/login', [
    'email' => 'test@example.com',
    'password' => 'wrong-password',
])->assertSessionHasErrors('email');
```

- **Premazanie databázy medzi testami**
  - Pre zabezpečenie toho, že databáza zostane pred každým testom čistá, vždy využívajte vlastnosť (trait) `RefreshDatabase`.

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
	use RefreshDatabase;

	public function test_user_creation() {
		$user = User::factory()->create();
		$this->assertDatabaseHas('users', ['email' => $user->email]);
	}
}
```

- Po významných zmenách v kóde vždy spustite testy, aby ste sa uistili, že aplikácia zostáva stabilná.
- Ešte pred samotnou opravou nahlásenej chyby (bugu) vždy napíšte test, ktorý tento problém replikuje. Tak sa vyhnete tomu, aby sa rovnaký problém v budúcnosti objavil znova.
