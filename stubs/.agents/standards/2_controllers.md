# 2. Controllery
- **Názvy controllerov v jednotnom čísle**
  - Controllery pomenovávajte podľa zdroja, ktorý spravujú, v jednotnom čísle.
  - Príklady: `UserController()`, `PostController()`

- **Oddelenie API a Web controllerov**
  - Ak má aplikácia API, používajte oddelené controllery pre API a webové cesty, aby ste zachovali jasné oddelenie zodpovedností (Separation of Concerns).
  - Príklad:
  ```php namespace App\Http\Controllers\Web;``` - UserController (pre web)
  ```php namespace App\Http\Controllers\Api;``` - UserController (pre API)

- **Dodržiavajte princíp jednej zodpovednosti (Single Responsibility Principle - SRP)**
  - Controllery by mali spracovávať iba HTTP požiadavky (requests) a odpovede (responses).
  - Vyhnite sa vkladaniu biznis logiky, databázových dopytov alebo iných zodpovedností priamo do controllera. Tieto úlohy delegujte na služby (services) alebo metódy modelu.

- **Livewire Class Komponenty vs. Controllery:**
  - Livewire komponenty (v adresári `app/Livewire/`) de facto nahrádzajú tradičné webové controllery pre interaktívne podstránky.
  - Klasické controllery si ponechajte primárne pre API endpointy, exporty a sťahovanie súborov, príjem webhooks a čisto statické stránky.
  - Rovnako ako controllery, ani Livewire komponenty by nemali obsahovať zložitú biznis logiku. Presúvajte ju do *Service* tried.
  - **PHP 8.3 Využitie:** Pre konštanty v controlleroch a komponentoch využívajte typované konštanty tried (napr. `public const int PAGINATION_LIMIT = 15;`).

```php
// Bad example (Controller/Livewire):
public function index() {
	$users = DB::table('users')->where('active', true)->get();
	return view('users.index', compact('users'));
}

// Good example:
public function index(UserService $userService): View|Response {
	$users = $userService->getActiveUsers();
	return view('users.index', compact('users'));
}
```

- Ťažkú biznis logiku delegujte na **services** a recyklujte kód zo service tried.
- **Nepoužívajte** repozitáre (repository), akcie (action) ani observer triedy - pre junior programátorov to môže byť ťažké na pochopenie.
- Deklarujte **návratové typy (Return classes)** metódy pomocou dvojbodky po deklarácii metódy.
- Uistite sa, že webové controllery vracajú objekty typu view, response alebo redirect.
- API controllery musia vracať výlučne JSON odpovede (`response()->json()`).

```php
// Simple controller
class SimpleController extends Controller
{
	use Illuminate\Http\RedirectResponse;
	use Illuminate\Http\Response;
	use Illuminate\View\View;

	public function functionName(): View|Response|RedirectResponse {
		...

		// Good example of return view
		return view('users.index', compact('users'));

		// Good example of return response
		return response()
			->view('article.show', ['model'=>$article])
			->header('last-modified', Carbon::now()->format('r'));

		// Good example of return redirect
		return redirect()->route('activity.show', $activity->slug);
	}
}
```

- Ak potrebujete zobraziť zoznam databázových záznamov, vždy používajte [stránkovanie (pagination)](https://laravel.com/docs/11.x/pagination).

- **Používajte Resource Controllery**
  - Pre štandardné CRUD operácie používajte resource controllery, aby vaše cesty a metódy zostali konzistentné a stručné.

```php
Route::resource('posts', PostController::class);
```

  - Metódy resource controllera:
    `index()`: Zobrazenie zoznamu zdrojov
    `show()`: Zobrazenie jedného zdroja
    `store()`: Uloženie nového zdroja
    `update()`: Úprava existujúceho zdroja
    `destroy()`: Vymazanie zdroja

- **Metódy udržujte úzko zamerané a krátke**
  - Každá metóda controllera by mala riešiť jedinú zodpovednosť alebo akciu.

- **Dodržiavajte RESTful princípy**
  - Navrhujte controllery tak, aby dodržiavali princípy REST:
  - Správne používajte HTTP slovesá (GET, POST, PUT, DELETE).
  - Používajte zmysluplné URI adresy, napríklad `/users` alebo `/user/{slug}`.

- **Používajte Dependency Injection (Vkladanie závislostí)**
  - Namiesto manuálneho vytvárania inštancií injektujte služby alebo helpery priamo do metód alebo konštruktorov controllera.

```php
// Bad example:
public function __construct() {
	$this->userService = new UserService();
}

// Good example:
public function __construct(UserService $userService) {
	$this->userService = $userService;
}
```

- **Využívajte Middleware**
  - Na úlohy ako autentifikácia, autorizácia alebo logovanie používajte middleware, namiesto toho, aby ste túto logiku duplikovali naprieč metódami controllera.

```php
public function __construct() {
	$this->middleware('auth');
	$this->middleware('can:update,post')->only(['edit', 'update']);
}
```

- **Vracajte JSON pre API**
  - Pre API controllery vracajte JSON odpovede so správnymi stavovými kódmi.
```php
public function store(PostRequest $request) {
	return response()->json(['data' => $data], 201); // 201 - Created
}
```


---
### Čo ďalej:
3. Pozrite si štandardy pre [Modely](https://git.greksak.sk/Michal/coding-standard/src/branch/main/docs/3_models.md).
