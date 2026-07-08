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

- Pre polymorfné (morph) tabuľky používajte označenie `model` namiesto prefixu `abble_` (napr. `commentable_id`).

```php
	Schema::create('comments', function (Blueprint $table) {
		$table->id();
		$table->unsignedBigInteger('model_id'); // ID of the related model
		$table->string('model_type'); // Model class name (Post, Video, etc.)
		$table->text('comment');      // The actual comment
		$table->timestamps();
	});
```

  - A následne to takto používajte v modeloch a controlleroch:

```php
// Relationship -- Many to Many (Polymorphic)
public function comments(): MorphToMany {
    return $this->morphMany(comment::class, 'model');
}

// Add a comment to the Post
$post->comments()->create(['comment' => 'Great post!']);
```

- Pre lepšiu čitateľnosť a prenositeľnosť databázy používajte vstavané metódy Laravelu (`string`, `integer`, `foreignId` atď.) namiesto surových (raw) SQL dopytov.
- Snažte sa vyhnúť zbytočne zložitej štruktúre tabuliek a celkovo sa snažte minimalizovať veľkosť databázy.
- Vyhnite sa používaniu typu `bigInteger`, pokiaľ je zrejmé, že tabuľka (napríklad `categories` alebo `tags`) nikdy nebude obsahovať obrovské množstvo záznamov.
- Všade, kde je to možné, použite `unsignedTinyInteger` a `tinyText` namiesto klasického `integer` alebo `string`.
- Pri vývoji sa zamerajte na použitie MariaDB ako databázovej služby.
- Pokiaľ je to aplikovateľné, definujte pre stĺpce predvolené hodnoty (default values), aby ste predišli problémom s hodnotami `NULL`.

```php
$table->boolean('is_active')->default(true);
$table->string('status')->default('pending');
```

- Na vynútenie vzťahov medzi tabuľkami používajte cudzie kľúče (foreign key constraints). Na kaskádové správanie používajte `onDelete()` a `onUpdate()`.

```php
$table->foreignId('user_id')->constrained()->onDelete('cascade');
```

- Pre zvýšenie výkonu pridajte indexy na často dopytované stĺpce. Pre vynútenie jedinečnosti použite metódu `unique()`.

```php
$table->string('email')->unique();
$table->index(['first_name', 'last_name']);
```

- Pre polia, v ktorých môžu chýbať údaje, explicitne uveďte `nullable()`.

```php
$table->string('middle_name')->nullable();
```

- Pre dlhšie textové polia vždy používajte samostatný model `Text()` a definujte polymorfnú (morph) reláciu.

```php
Schema::create('texts', function (Blueprint $table) {
    $table->id(); // Auto-incrementing unsigned integer primary key
    $table->unsignedInteger('model_id');   // Equivalent to UNSIGNED INT(10)
    $table->tinyText('model_type');        // Equivalent to TINYTEXT
    $table->tinyText('flag')->nullable();  // TINYTEXT with NULL default
    $table->text('value')->nullable();     // TEXT with utf8mb4 collation
});

// In model:
// Relationship -- One to One (Polymorphic)
public function body(): MorphOne {
	return $this->morphOne(Text::class, 'model')->where('flag', 'body');
}
```

- Namiesto boolean hodnôt uprednostňujte dátumy (timestamps). Napríklad používajte `published_at` namiesto `is_published`.
- Pre dátumové polia vždy používajte zmysluplnú príponu `_at`.


---
### Čo ďalej:
5. Pozrite si štandardy pre [Pohľady (Views)](https://git.greksak.sk/Michal/coding-standard/src/branch/main/docs/5_views.md).
