# 4. Migrácie (Migrations)
- Pre názvy tabuliek a stĺpcov používajte `snake_case`.
- V názvoch súborov **ponechajte predvolené formáty Laravelu** s dátumovou časovou pečiatkou (napr. `2024_11_08_100000_`), aby bolo možné ľahko dohľadať, kedy bola migrácia vytvorená.
- Používajte popisné a zmysluplné názvy súborov, ako napríklad `create_users_table`.
- Vyhnite sa implementovaniu biznis logiky priamo v migráciách. Migrácie slúžia výlučne na správu databázovej schémy, nie na manipuláciu s dátami.
- Vyhnite sa kombinovaniu viacerých nesúvisiacich zmien do jedného migračného súboru.
- Viacero **súvisiacich** tabuliek však môžete spojiť do jednej migrácie.

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

	// Run the migrations.
	public function up(): void
	{
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('email')->unique();
			$table->timestamp('email_verified_at')->nullable();
			$table->string('password');
			$table->timestamps();
		});

		Schema::create('user_tokens', function (Blueprint $table) {
			$table->string('email')->primary();
			$table->string('type')->default('password_reset');
			$table->string('token');
			$table->timestamp('created_at')->nullable();
		});
	}

	// Reverse the migrations.
	public function down(): void
	{
		Schema::dropIfExists('users');
		Schema::dropIfExists('user_tokens');
	}
};
```

- Pre polymorfné (morph) tabuľky používajte buď štandardnú Laravel konvenciu `$table->morphs('imageable')` (`*able`), alebo internú konvenciu `$table->morphs('model')` (`model_type`, `model_id`). V `AppServiceProvider::boot()` vždy zaregistrujte `Relation::enforceMorphMap([...])`, aby sa v databáze neukladali plné PHP menné priestory tried.

```php
	Schema::create('images', function (Blueprint $table) {
		$table->id();
		$table->morphs('imageable'); // Creates imageable_type, imageable_id + composite index
		$table->string('path');
		$table->unsignedSmallInteger('sort_order')->default(0);
		$table->timestamps();
	});
```

  - A následne to takto používajte v modeloch:

```php
// Relationship -- One to Many (Polymorphic)
public function images(): MorphMany
{
    return $this->morphMany(Image::class, 'imageable');
}
```

- Pre lepšiu čitateľnosť a prenositeľnosť databázy používajte vstavané metódy Laravelu (`string`, `integer`, `foreignId`, `json` atď.) a Eloquent metódy namiesto surových (raw) SQL operátorov viazaných na jediný driver (napr. nepoužívajte MySQL `RLIKE`, pokiaľ existuje štandardná alternatíva kompatibilná aj s testovacím prostredím).
- Primárnou cieľovou databázou je **MySQL 8.0+** (InnoDB, `utf8mb4`).
- Pre všetky primárne a cudzie kľúče **vždy používajte `$table->id()` a `$table->foreignId('..._id')->constrained()`** (`BIGINT UNSIGNED`), aby bola zaručená plná kompatibilita InnoDB cudzích kľúčov. Menšie celočíselné typy (`unsignedTinyInteger`, `unsignedSmallInteger`) používajte iba pre malé nečíselníkové atribúty bez relácií (napr. `role`, `sort_order`).
- **Nepoužívajte `tinyText`:** v znakovej sade `utf8mb4` má limit 255 bajtov (nie znakov) a nepodporuje štandardné B-Tree indexy bez dĺžky prefixu. Pre krátke reťazce vždy používajte `$table->string()`.
- Pokiaľ je to aplikovateľné, definujte pre stĺpce predvolené hodnoty (default values), aby ste predišli problémom s hodnotami `NULL`.

```php
$table->boolean('is_active')->default(true);
$table->string('status')->default('pending');
```

- Na vynútenie vzťahov medzi tabuľkami používajte cudzie kľúče (foreign key constraints). Na kaskádové správanie používajte `cascadeOnDelete()` / `nullOnDelete()`.

```php
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
```

- Pre zvýšenie výkonu pridajte indexy na často dopytované a filtrované stĺpce (vrátane oboch smerov v pivot tabuľkách). Pre vynútenie jedinečnosti použite metódu `unique()`.
- Pre unikátne alebo zoraditeľné hodnoty uložené vo viacjazyčných `JSON` stĺpcoch (napr. `slug`) definujte v MySQL 8.0 generované virtuálne stĺpce s indexom:

```php
$table->json('slug');
$table->string('slug_sk')->virtualAs("json_unquote(json_extract(`slug`, '$.sk'))")->unique();
$table->string('slug_en')->virtualAs("json_unquote(json_extract(`slug`, '$.en'))")->unique();
```

- Pre polia, v ktorých môžu chýbať údaje, explicitne uveďte `nullable()`.

```php
$table->string('middle_name')->nullable();
```

- **Dlhé textové a štruktúrované polia ukladajte priamo v tabuľke modelu:**
  - Stĺpce typu `text`, `longText` a `json` definujte priamo v tabuľke príslušnej entity (MySQL 8 InnoDB `DYNAMIC` formát ukladá dlhé hodnoty off-page mimo hlavného B-Tree indexu).
  - **Nevytvárajte** samostatnú polymorfnú tabuľku `texts` ani model `Text` – zbytočne to rozbíja dátovú integritu a spôsobuje N+1 dotazy.

- Pre časové okamihy udalostí uprednostňujte dátumové polia s príponou `_at` (napr. `published_at`, `email_verified_at`). Pre prepínače viditeľnosti alebo schválenia sú povolené aj explicitné `boolean` stĺpce (`is_approved`).


---
### Čo ďalej:
5. Pozrite si štandardy pre [Pohľady (Views)](./5_views.md).
