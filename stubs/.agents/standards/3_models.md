# 3. Modely
- Používajte **jednotné číslo** a popisné názvy pre modely.
- Na transformáciu dát v rámci modelu používajte **accessors**.
- V modeloch nepoužívajte mutátory (mutators) - pre junior programátorov môžu byť ťažšie na pochopenie.
- Relácie definujte **explicitne**, napríklad `hasOne`, `morphToMany` alebo `belongsTo`.
- Všetky relácie popíšte do jednoriadkového komentára nad metódou.

```php
	// Relationship -- One to One (Polymorphic)
	public function citation(): MorphOne {
		return $this->morphOne(Text::class, 'model')->where('flag', 'citation');
	}
```

- V modeloch dodržujte toto poradie premenných:
  1. `protected $table` - túto vlastnosť prosím definujte vždy
  2. `protected $fillable` - používajte bezpečnejšie `$fillable` namiesto `$guarded`
  3. `protected $casts`
  4. `protected static function boot()`
- Dodržujte toto poradie metód v modeloch:
  1. Relácie (Relationships)
  2. Accessors
  3. Vlastné metódy (Custom methods)
  4. Helpery (Helpers)
- Vo vlastnosti `$fillable` a v migráciách používajte toto poradie:
  1. id
  2. ostatné id polia
  3. polia s kratšími názvami
  4. polia s dlhšími názvami
  5. dátumové polia
  6. laravel timestamps
- Pomocou castingu transformujte Eloquent atribúty pre dátumové polia na objekt Carbon.

- **Bezpečnosť modelov v Livewire:**
  - Všetky verejné premenné (`public properties`) v Livewire komponente sú viditeľné na frontende v HTML (v atribúte `wire:snapshot`).
  - **Prísny zákaz:** Do verejných premenných komponentu nikdy nepriradzujte celé Eloquent modely. Hrozí tým únik citlivých dát a vážna zraniteľnosť typu **Mass Assignment** (kde útočník môže zmanipulovať JSON odosielaný späť na server).
  - Namiesto toho uložte do stavu komponentu iba ID modelu (`public int $modelId;`) a samotný model načítavajte výlučne cez Computed Properties (`#[Computed]`), ktoré sa na frontend neserializujú.

```php
// Good example: Eloquent model with relationships and accessors
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Company extends Model
{

	// Base table name
	protected $table = 'companies';

	// Mass assignment
	protected $fillable = [ 'type_id', 'category_id', 'owner_id', 'name', 'slug', 'public_at' ];

	// Return Carbon object
	protected $casts = [ 'public_at' => 'date' ];

	// Bootstrap any application services.
	protected static function boot(): void {
		Model::preventLazyLoading();
		parent::boot();
		static::saving(function ($model) {
			$model->slug = SlugService::createSlug($model);
		});
	}

	// Relationship -- One to Many
	public function employees(): HasMany {
		return $this->hasMany(Employ::class);
	}

	// Relationship -- Many to Many (Polymorphic)
	public function tags(): MorphToMany {
		return $this->morphToMany(Tag::class, 'model');
	}

	// Accessor -- fullName
	public function getFullNameAttribute() {
		return "{$this->name} ({$this->public_at->format('Y')})";
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

- Vyhnite sa používaniu **soft delete** na modeloch
  - Pri každom dopyte (hooku) to aplikuje SQL podmienku `WHERE deleted_at IS NULL`. Pri veľkých projektoch to môže spôsobiť problémy s výkonom.
  - Tieto podmienky nie sú automaticky povolené v Query Builderi (napr. pri použití `DB::table('tags')->get();`).
  - Soft delete nevyužíva kaskádové operácie na úrovni databázy.
  - Môže to spôsobiť problémy s unikátnymi obmedzeniami (unique constraints). Napríklad, ak vytvoríte používateľa s e-mailom, ktorý patrí soft-zmazanému používateľovi, databáza vráti chybu `Duplicate entry error`.

- Na začatie dopytovania (querying) Eloquent modelu používajte statickú metódu `query()`.
- Ako interný primárny kľúč používajte automaticky inkrementované ID (Incrementing IDs).


---
### Čo ďalej:
4. Pozrite si štandardy pre [Migrácie](https://git.greksak.sk/Michal/coding-standard/src/branch/main/docs/4_migrations.md).
