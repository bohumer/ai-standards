# 3. Modely
- Používajte **jednotné číslo** a popisné názvy pre modely.
- Na transformáciu dát v rámci modelu používajte v Laraveli 13 modernú triedu `Illuminate\Database\Eloquent\Casts\Attribute` (`Attribute::make(get: ..., set: ...)`). Zastarané metódy `getFooAttribute()` a `setFooAttribute()` nepoužívajte.
- Relácie definujte **explicitne** s návratovými typmi (napr. `HasOne`, `BelongsTo`, `HasMany`, `MorphMany`).
- Všetky relácie popíšte do jednoriadkového komentára nad metódou.

```php
	// Relationship -- One to Many (Polymorphic)
	public function images(): MorphMany {
		return $this->morphMany(Image::class, 'imageable');
	}
```

- V modeloch dodržujte toto poradie prvkov:
  1. `protected $table` - túto vlastnosť definujte explicitne
  2. `protected $fillable` (a prípadne `public array $translatable`) - používajte bezpečnejšie `$fillable` namiesto `$guarded`
  3. `protected function casts(): array` - v Laraveli 13 uprednostnite metódu `casts()` pred vlastnosťou `$casts`
  4. `protected static function booted(): void` - používajte len pre lokálne modelové udalosti (napr. vymazanie cache). Ochranu proti N+1 a neexistujúcim atribútom zapínajte globálne v `AppServiceProvider::boot()` cez `Model::shouldBeStrict(! $this->app->isProduction());`.
- Dodržujte toto poradie metód v modeloch:
  1. Relácie (Relationships)
  2. Accessors / Mutators (`Attribute`)
  3. Query Scopes (`scopeSearch`, `scopeActive` a pod.)
  4. Vlastné doménové a pomocné metódy
- Vo vlastnosti `$fillable` a v migráciách používajte toto logické poradie:
  1. `id` (v migrácii)
  2. cudzie kľúče (`*_id`)
  3. hlavné doménové polia (`name`, `slug`, `description`, `content`, číselné hodnoty)
  4. stavové príznaky a dátumové polia (`status`, `published_at`)
  5. Laravel `timestamps()` / `softDeletes()`
- Pomocou metódy `casts(): array` transformujte Eloquent atribúty pre dátumové polia na objekt Carbon (`'datetime'`, `'date'`), JSON štruktúry na `'array'` a logické príznaky na `'boolean'`.

- **Bezpečnosť modelov a stavu v Livewire (KRITICKÉ):**
  - Všetky verejné premenné (`public properties`) v Livewire komponente sú serializované do HTML (`wire:snapshot`) a pokiaľ nie sú uzamknuté, klient ich môže v požiadavke `livewire/update` ľubovoľne prepísať.
  - **Prísny zákaz `public Model`:** Do verejných premenných komponentu nikdy nepriradzujte celé Eloquent modely ani Eloquent kolekcie. Hrozí únik citlivých atribútov (napr. e-mailov, interných príznakov) a rozpad eager-loaded relácií.
  - **Povinný atribút `#[Locked]`:** Do stavu komponentu ukladajte iba skalárne ID modelu povinne označené atribútom **`#[Locked]`** (`#[Locked] public ?int $modelId = null;`). Rovnako povinne označte atribútom `#[Locked]` všetky systémové a konfiguračné verejné vlastnosti komponentu (napr. `$tableModelClass`, `$isAdminRoute`, názov cieľového modelu pri uploaderi), ktoré používateľ nesmie meniť z prehliadača. Bez `#[Locked]` vzniká kritická zraniteľnosť **IDOR / Privilege Escalation**!
  - **Načítanie cez `#[Computed]`:** Samotný model načítavajte výlučne cez Computed Properties (`#[Computed]`), ktoré sa na frontend neserializujú.
  - **Povinná autorizácia v mutáciách:** Kontrolu oprávnení cez `$this->authorize(...)` (Policy) vykonávajte nielen v `mount()`, ale **povinne v každej mutačnej metóde** (`save()`, `delete()`, mazanie obrázkov, hromadné akcie v tabuľke).

```php
// Good example: Laravel 13 Eloquent model with casts(), relationships and Attribute
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Company extends Model
{
	// Base table name
	protected $table = 'companies';

	// Mass assignment
	protected $fillable = ['category_id', 'owner_id', 'name', 'slug', 'published_at'];

	// Attribute casting
	protected function casts(): array
	{
		return [
			'published_at' => 'datetime',
		];
	}

	// Relationship -- One to Many
	public function employees(): HasMany
	{
		return $this->hasMany(Employee::class);
	}

	// Relationship -- One to Many (Polymorphic)
	public function images(): MorphMany
	{
		return $this->morphMany(Image::class, 'imageable');
	}

	// Accessor -- formatted display title
	protected function displayTitle(): Attribute
	{
		return Attribute::make(
			get: fn () => "{$this->name} ({$this->published_at?->format('Y')})",
		);
	}
}
```

- Vyhnite sa používaniu `$with` pre relácie, ktoré nie sú povinné alebo nutne potrebné.
  Môže to viesť k zvýšenej spotrebe pamäte a k spomaleniu výkonu databázových dopytov.

```php
// Not ideal: Will always load 'posts' relationship, even when not needed.
protected $with = ['posts'];

// Better: Use explicit eager loading when needed.
$users = User::with('posts')->get();
```

- Vlastnosť `$with` používajte striedmo, a to len pre relácie, ktoré sú:
  - Nevyhnutné vo väčšine prípadov (napríklad Používateľ a Profil).
  - Dátovo nenáročné (napr. malé relácie, ktoré nenačítavajú obrovské sady dát).
  - Zriedka podliehajúce zmenám (napr. relácie, ktoré neskôr nespôsobia načítanie ďalších vnorených relácií).

- Používajte **SoftDeletes** uvážlivo, nie plošne na každom modeli:
  - `SoftDeletes` povoľte pre obchodné a katalógové entity (napr. produkty, kategórie, objednávky), kde je potrebná história alebo možnosť obnovenia záznamu.
  - Nepoužívajte `SoftDeletes` na pomocných, väzobných, draftových a číselníkových tabuľkách.
  - Ak model používa `SoftDeletes`, pamätajte na to, že databázový `unique()` index zahŕňa aj soft-zmazané riadky a `DB::table(...)` dopyty neaplikujú filter `WHERE deleted_at IS NULL` automaticky.

- Na začatie dopytovania (querying) Eloquent modelu používajte statickú metódu `query()`.
- Ako interný primárny kľúč používajte automaticky inkrementované ID (`$table->id()`).


---
### Čo ďalej:
4. Pozrite si štandardy pre [Migrácie](./4_migrations.md).
