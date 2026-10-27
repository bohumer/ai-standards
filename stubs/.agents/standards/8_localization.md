# 8. Lokalizácia a viacjazyčné dáta (Translations & Localization)

Systém striktne rozlišuje iba **dva povolené mechanizmy prekladov**:
1. **Helper `t()`** (tabuľka `translations`) pre statické texty používateľského rozhrania.
2. **JSON stĺpce v databáze** (trait `HasJsonTranslations`) pre všetky dynamické viacjazyčné údaje entít a číselníkov.

> **Prísny zákaz jazykových prípon:** Nikdy nevytvárajte databázové stĺpce s príponou jazyka (napr. `name_sk`, `name_en`) ani verejné vlastnosti v Livewire komponentoch s príponou jazyka (napr. `public $name_sk`, `public $value_en`). Takýto prístup znemožňuje dynamické pridanie ďalšieho jazyka bez úpravy schémy a kódu.

---

## 1. Synchronizácia aktívneho jazyka (Locale)

- Aktuálny jazyk používateľa vracia helper `app_locale()` (`session('locale') ?? config('app.locale')`) a zoznam podporovaných jazykov vracia `app_langs()` (zo `Setting::getList('system', 'language')` alebo konfigurácie).
- Vo webovej skupine middlewarov musí byť zaregistrovaný `SetLocaleMiddleware`, ktorý na začiatku každej požiadavky zavolá `App::setLocale(app_locale())`. Tým sú interné mechanizmy Laravelu (validácie, paginácia, dátumy Carbon) vždy synchronizované s `app_locale()`.
- Pre systémové správy samotného frameworku (napr. Laravel Fortify, vstavané validačné pravidlá) udržiavajte štandardné jazykové súbory v adresári `lang/{locale}/`.

---

## 2. Statické texty UI – Helper `t()`

- Všetky statické texty v Blade šablónach, názvy stĺpcov, tlačidlá, notifikácie a vlastné validačné hlášky v Livewire triedach obaľujte **výlučne do funkcie `t()`**:
  ```php
  public function t(string $key, array $replace = []): string
  ```
- **Žiadne predvolené (fallback) texty v kóde:**
  - Do funkcie `t()` nikdy neposielajte ako druhý parameter náhradný text (napr. ~~`t('product.save', 'Uložiť')`~~ je zakázané).
  - Ak preklad pre aktuálny jazyk v tabuľke `translations` neexistuje, `t()` **musí vrátiť viditeľný identifikátor `#Trans.{$key}`**, aby chýbajúci kľúč okamžite udrel do očí pri vývoji a kontrole UI.
  - Rovnako pri chýbajúcom preklade v sekundárnom jazyku (napr. `en`) funkcia `t()` neprepadáva potichu do slovenčiny, ale vypíše `#Trans.{$key}`.
- **Dynamické premenné v prekladoch:**
  - Druhý parameter `$replace` slúži výhradne na nahradenie zástupných symbolov `:parameter` vo vete uloženej v databáze:
    ```php
    // Hodnota v DB: "Pole :name je povinné."
    t('validation.required_property', ['name' => $property->name]);
    ```
- **Pravidlá pre ukladanie a výpis `Translation`:**
  - Model `Translation` vracia presný text z databázy (z dvojúrovňovej cache: statická runtime premenná + `Cache::remember` s invalidáciou pri zmene záznamu).
  - V modeli `Translation` nikdy nedeformujte veľké a malé písmená cez `mb_strtolower()` a neaplikujte `nl2br()`. HTML escapovanie zabezpečuje výhradne Blade cez `{{ t('key') }}`.
  - Každý novopridaný kľúč `t()` bezodkladne zapíšte do `database/seeders/TranslationSeeder.php` (a do databázy pre všetky aktívne jazyky projektu).

---

## 3. Dynamické viacjazyčné dáta modelov – JSON atribúty (`HasJsonTranslations`)

Všetky databázové entity a číselníky, ktorých textové atribúty sa zobrazujú používateľom (napr. `Category`, `Product`, `Profile`, `Property`, `PropertyGroup`, `PropertyValue` aj možnosti v `Setting`), ukladajú prekladané polia do **stĺpcov typu `JSON`** priamo v tabuľke daného modelu:

```json
{
    "sk": "Drevený stôl",
    "en": "Wooden table"
}
```

### Pravidlá pre Model a Trait `HasJsonTranslations`
- V modeli použite trait `App\Traits\HasJsonTranslations` a vymenujte prekladané atribúty v poli `public array $translatable = ['name', 'slug', 'description'];`.
- **Fallback pre dynamické dáta:** Pri čítaní atribútu na frontende (`$model->name`) trait vráti hodnotu pre `app_locale()`. Ak je hodnota pre aktuálny jazyk `null` alebo prázdny reťazec `""` (kontrolované cez `filled()`), vráti hodnotu v primárnom jazyku (`config('app.fallback_locale', 'sk')`), prípadne prvú neprázdnu hodnotu.

### Pravidlá pre Migrácie a Unikátne JSON Indexy (MySQL 8.0+)
- Prekladané polia definujte ako `$table->json('name');`.
- Ak musí byť JSON atribút unikátny alebo rýchlo vyhľadávateľný podľa kľúča (typicky `slug`), v MySQL 8.0 vytvorte **generovaný virtuálny stĺpec s unikátnym indexom**:
  ```php
  Schema::create('categories', function (Blueprint $table) {
      $table->id();
      $table->json('name');
      $table->json('slug');
      $table->string('slug_sk')->virtualAs("json_unquote(json_extract(`slug`, '$.sk'))")->unique();
      $table->string('slug_en')->virtualAs("json_unquote(json_extract(`slug`, '$.en'))")->unique();
      $table->timestamps();
  });
  ```

### Pravidlá pre Livewire Formuláre (`Create` / `Edit`) a `HasDrafts`
- Vo formulárových Livewire komponentoch deklarujte prekladané polia ako **asociatívne pole**:
  ```php
  public array $name = [];
  public array $slug = [];
  ```
- V metóde `mount()` inicializujte kľúče dynamicky podľa podporovaných jazykov (`app_langs()`), pri `Edit` ich naplňte cez `$model->getTranslations('name')` a následne zavolajte `$this->loadDraft(...)`:
  ```php
  foreach (app_langs() as $lang) {
      $this->name[$lang] = $category?->getTranslations('name')[$lang] ?? '';
  }
  ```
- V Blade šablóne generujte inputy alebo prepínač jazykových kariet (tabov) v cykle `@foreach(app_langs() as $lang)` s prepojením `wire:model.live.blur="name.{{ $lang }}"`. Tým sa pole `name` uloží do `drafts.payload` ako celok a pridanie nového jazyka do systému si nevyžiada žiadnu úpravu komponentu.
- Validáciu dynamických jazykových polí definujte v metóde `protected function rules(): array` (napr. `'name.sk' => ['required', 'string', 'max:255']`, `'name.*' => ['nullable', 'string', 'max:255']` a pre unikátny JSON slug `Rule::unique('categories', 'slug->sk')->ignore($this->categoryId)`).

### Kedy použiť samostatnú tabuľku `*_translations`
Samostatnú relačnú tabuľku (napr. `article_translations`) použite iba vo výnimočnom prípade, ak entita presahuje rádovo státisíce záznamov a vyžaduje natívny MySQL `FULLTEXT` index nad dlhými textami vo viacerých jazykoch alebo samostatný stav publikovania pre každý jazyk osobitne.
