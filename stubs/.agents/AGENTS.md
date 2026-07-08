# Pravidlá pre prácu s modelmi a Livewire (Draft Table Pattern)

Tento súbor obsahuje špecifické pravidlá (Draft Pattern, Settings, preklady), ktoré musíš bezvýhradne dodržiavať pri vytváraní a úprave akéhokoľvek modelu v tomto systéme.

> **DÔLEŽITÉ:** Okrem tohto súboru **musíš zohľadňovať aj všetky postupy a štandardy zadefinované v súboroch v priečinku `standards/`** (napríklad prísny zákaz priraďovania celých Eloquent modelov do public properties v Livewire podľa `3_models.md`). Pravidlá z `standards/` tvoria základ a tento súbor (AGENTS.md) ich dopĺňa o špecifické funkcie pre administráciu.

## 1. Architektúra modelu a Views
Pri každom modeli musíš vytvoriť nasledujúce 4 Livewire komponenty a im prislúchajúce Blade šablóny (`Index`, `Show`, `Create`, `Edit`).

### Index (Zoznam záznamov)
- Kde budú vypísané všetky záznamy z modelu.
- Každý riadok musí byť klikateľný ako odkaz na `model.show`.
- **Povinné funkcionality:** Vyhľadávanie, sortovanie podľa stĺpcov, filtrovanie podľa polí, paginácia.
- Na implementáciu tabuľkových operácií **vždy použi trait `WithUniversalTable`** z priečinka `app/Livewire/Traits` a inšpiruj sa štruktúrou ukážkovej triedy `app/Livewire/Admin/Product/Index.php`.

### Show (Detail modelu)
- Zobraz všetky polia modelu.
- Tlačidlo pre návrat späť na `Index`.
- Tlačidlá na `Edit` a `Delete` (viditeľnosť musí byť chránená podľa policy).
- Zobraz sekcie s odkazmi na related (príbuzné) modely.

### Create a Edit (Formuláre)
- Všetky polia musia byť zobrazené vo formulári.
- Všetky polia musia byť riadne validované v Livewire triede.
- Priprav preklady (pomocou `t()`) aj na chybové/validácie hlášky, pokiaľ ich definuješ v triede.

## 2. Priebežné ukladanie konceptov (Draft Table Pattern)
Aby používateľ neprišiel o údaje pri úprave/vytváraní v prípade výpadku, systém používa tabuľku `drafts`.
- V každom `Create` a `Edit` komponente **musíš použiť trait `HasDrafts`** (z `app/Livewire/Traits/HasDrafts.php`).
- Pri načítaní komponentu (`mount()`) zavolaj `$this->loadDraft(Model::class, $modelId)`.
- V triede zadefinuj hook `updated()`, v ktorom zavoláš `$this->saveDraft(Model::class, $modelId, $this->only([...všetkyFormovéPolia...]))`. V Blade šablónach k inputom vždy pridaj `wire:model.live.blur="fieldname"`, aby sa hook `updated()` spúšťal po strate zamerania z poľa.
- Po ostrom uložení záznamu v metóde `save()` vždy zmaž draft zavolaním `$this->clearDraft(Model::class, $modelId)`.

## 3. Preklady (Translations)
- Všetky texty vo frontend aj backend kóde musia byť vkladané **výlučne v `t('key')`** funkcií (alebo `App\Models\Translation::translate('key')`).
- Anglické a slovenské preklady do aktuálneho jazyka musia byť **bezodkladne pridávané do `database/seeders/TranslationSeeder.php`** (alebo ich do databázy priamo zapisuj ak ide o jednorázový fix).

## 4. Možnosti v poliach (Settings - Selects)
- Ak v modeli existuje pole, ktoré vyberá z viacerých možností (napr. category, typ atď.), **tieto možnosti sa nesmú "nadrátať" napevno v kóde**.
- Možnosti sa ukladajú v databázovej tabuľke `settings`.
- **Integrácia s prekladmi (Translations):** 
  - V tabuľke `settings` slúžia stĺpce `flag`, `key` a `value` iba na definíciu vnútorného kľúča (napr. flag = `product`, key = `category`, value = `electronics`).
  - Formuláre pre ukladanie Settings (Create/Edit) musia priamo obsahovať textové polia pre preklady (napr. `value_sk`, `value_en`), ale tieto texty sa nesmú ukladať do modelu Setting.
  - Všetky preklady možností sa ukladajú výhradne do modelu `Translation`, kde prekladovým kľúčom je poskladaný reťazec `$flag.$key.$value` (napr. `product.category.electronics`).
- Hodnoty do selectboxu musíš ťahať z databázy cez `Setting::getList('product', 'category')`. Tieto vytiahnuté možnosti vo frontende povinne prekladaj cez funkciu `t()`.

## 5. HTML, CSS a Design
- Pre šablóny **vždy použi Tailwind CSS**.
- Design, štýly a použité komponenty však **musia byť vždy zosúladené s aktuálnym dizajnom** stránky každého konkrétneho projektu, v ktorom operuješ. Nenavrhuj univerzálny vzhľad, ale adaptuj kód tak, aby vizuálne splýval s hotovými zobrazeniami klienta (použi rovnaké triedy, wrapper element a paddingy ako nájdeš inde).

## 6. Pravidlá pri zmene štruktúry modelu (Pridanie/Odobratie poľa)
Vždy, keď dostaneš požiadavku pridať nové pole alebo odobrať existujúce pole z modelu, **musíš povinne vykonať krížovú kontrolu** so všetkými našimi štandardmi a zabezpečiť aktualizáciu na všetkých relevantných miestach:
1. **Databáza:** Aktualizovať príslušnú migráciu alebo vytvoriť novú.
2. **Model:** Pridať/odobrať pole z vlastnosti `$fillable` a v prípade potreby (napr. boolean, date) upraviť `$casts`.
3. **Livewire Classy (Create & Edit):** 
   - Pridať/odobrať verejnú premennú (`public $pole;`).
   - Upraviť validácie vo vlastnosti `$rules`.
   - **Drafts:** Pridať/odobrať pole zo zoznamu premenných v metóde `$this->saveDraft(...)`, ktorá sa volá v hooku `updated()`.
4. **Views (Livewire Blade):** Pridať/odobrať príslušný HTML prvok vo formulároch (vrátane prepojenia `wire:model.live.blur`). Taktiež overiť, či ho netreba zobraziť/odstrániť aj v prehľade (Index) a v detaile (Show).
5. **Preklady:** Ak nové pole vyžaduje vlastný text (napr. label formulára, názov stĺpca), musíš ho zaobaliť do `t()` funkcie a doplniť do seederov.
