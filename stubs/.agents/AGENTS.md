# Pravidlá pre prácu s modelmi a Livewire (Draft Table Pattern)

Tento súbor obsahuje špecifické pravidlá (Draft Pattern, Settings, preklady), ktoré musíš bezvýhradne dodržiavať pri vytváraní a úprave akéhokoľvek modelu v tomto systéme.

> **DÔLEŽITÉ:** Okrem tohto súboru **musíš zohľadňovať aj všetky postupy a štandardy zadefinované v súboroch `1_routing.md` až `8_localization.md` v priečinku `standards/`** (najmä prísny zákaz priraďovania celých Eloquent modelov do `public` properties, povinné uzamykanie ID cez `#[Locked]` podľa `3_models.md` a jednotný systém prekladov podľa `8_localization.md`). Pravidlá z `standards/` tvoria základ a tento súbor (`AGENTS.md`) ich dopĺňa o špecifické funkcie pre administráciu.
> **Špecifické pravidlá projektu:** Vždy pred začatím práce s kódom skontroluj prítomnosť súboru `AGENTS.md` v koreňovom adresári projektu. Ak existuje, obsahuje dodatočné pravidlá a pokyny platné výhradne pre daný projekt. Súbor `.agents/AGENTS.md` slúži výlučne pre zdieľané globálne štandardy.

## 1. Architektúra modelu a Views

**Základná trieda (BaseLivewireComponent):**
Všetky celostránkové a administrátorské Livewire komponenty (Index, Show, Create, Edit) **musia** dediť od `App\Support\BaseLivewireComponent` (nikdy nie priamo od `Livewire\Component`). Tento base komponent poskytuje dôležité funkcie pre audit log, navigáciu a notifikácie, ktoré musíš povinne využívať:

1. **Titulok stránky:** V metóde `mount()` vždy nastav dynamický titulok cez `$this->setTitle(t('nazov_stranky') . ' ' . $dalsia_premmenna)`. Nevpisuj titulok natvrdo do HTML; hlavný layout ho musí vypísať do `<title>`.
2. **Flash a Audit Log:** Zakaždým, keď používateľ niečo vytvorí (`save` v Create), upraví (`save` v Edit) alebo vymaže (`delete`), musíš zavolať `$this->flashSuccess(t('vhodny_kluc'))`. Táto metóda ukáže používateľovi správu (cez session alebo JS event `flash-message`) a automaticky zapíše udalosť do tabuľky `logs`. Ak dôjde k chybe, použi `$this->flashError()`. Výnimkou je priebežné ukladanie draftov (konceptov), ktoré logovať **nesmieš**.
3. **Návrat späť:** Pri tlačidlách "Zrušiť" alebo pri úspešnom uložení formulára používaj na presmerovanie späť na zoznam metódu `$this->redirectBackWithParams('admin.model.index')`.
4. **Bezpečnosť stavu a autorizácia (KRITICKÉ):**
   - Nikdy neukladaj Eloquent model do `public` premennej. Ukladaj iba skalárne ID povinne označené atribútom **`#[Locked]`** (`#[Locked] public ?int $modelId = null;`) a model čítaj cez `#[Computed]`.
   - Atribútom `#[Locked]` povinne označ aj všetky systémové verejné premenné komponentu (napr. `$tableModelClass`, príznaky režimu/routy).
   - Oprávnenia overuj cez Policy (`$this->authorize(...)`) v `mount()` **aj v každej mutačnej metóde** (`save()`, `delete()`, hromadné akcie).

Pri každom spravovateľnom modeli vytvor nasledujúce 4 Livewire komponenty a im prislúchajúce Blade šablóny (`Index`, `Show`, `Create`, `Edit`) – okrem čisto auditných alebo systémových read-only modelov (napr. `Log`), kde postačuje `Index` a `Show`.

### Index (Zoznam záznamov)

- Kde budú vypísané všetky záznamy z modelu.
- Každý riadok musí byť klikateľný ako odkaz na `model.show`.
- **Povinné funkcionality:** Vyhľadávanie, sortovanie podľa stĺpcov, filtrovanie podľa polí, paginácia.
- Na implementáciu tabuľkových operácií **vždy použi trait `App\Traits\WithUniversalTable`** (z `app/Traits/WithUniversalTable.php`) a inšpiruj sa štruktúrou ukážkovej triedy `app/Livewire/Admin/Product/Index.php`.

### Show (Detail modelu)

- Zobraz všetky polia modelu (načítaného cez `#[Computed]`).
- Tlačidlo pre návrat späť na `Index`.
- Tlačidlá na `Edit` a `Delete` (viditeľnosť musí byť chránená cez `@can` podľa Policy).
- Zobraz sekcie s odkazmi na related (príbuzné) modely.

### Create a Edit (Formuláre)

- Všetky polia musia byť zobrazené vo formulári. Pre viacjazyčné atribúty nikdy nevytváraj premenné typu `$name_sk`, `$name_en`, ale použi dynamické asociatívne pole `public array $name = []` vykreslené v cykle podľa aktívnych jazykov (`app_langs()`).
- Všetky polia musia byť riadne validované pomocou atribútu `#[Validate]` (pre jednoduché statické pravidlá) alebo metódy `protected function rules(): array` (pre dynamické pravidlá, `Rule::unique` a viacjazyčné polia). Zastaranú vlastnosť `$rules` nepoužívaj.
- Vlastné chybové a validačné hlášky definuj v metóde `messages(): array` pomocou `t()`.

## 2. Priebežné ukladanie konceptov (Draft Table Pattern)

Aby používateľ neprišiel o údaje pri úprave/vytváraní v prípade výpadku, systém používa tabuľku `drafts`.

- V každom `Create` a `Edit` komponente **musíš použiť trait `HasDrafts`** (z `app/Livewire/Traits/HasDrafts.php`).
- Pri načítaní komponentu (`mount()`) zavolaj `$this->loadDraft(Model::class, $modelId)`.
- V triede zadefinuj hook `updated()`, v ktorom zavoláš `$this->saveDraft(Model::class, $modelId, $this->only([...všetkyFormovéPolia...]))`. V Blade šablónach k inputom vždy pridaj `wire:model.live.blur="fieldname"`, aby sa hook `updated()` spúšťal po strate zamerania z poľa.
- Ak formulár pri vytváraní (`Create`) vyžaduje okamžitý upload súborov/obrázkov, nevytváraj falošné záznamy v hlavnej tabuľke modelu – viaž nahrávané súbory na záznam `Draft` a pri `save()` ich prelinkuj na novovytvorený model.
- Po ostrom uložení záznamu v metóde `save()` vždy zmaž draft zavolaním `$this->clearDraft(Model::class, $modelId)`.

## 3. Preklady a viacjazyčnosť (Translations & JSON)

Systém používa výlučne **dva spôsoby prekladov** (podrobne v `standards/8_localization.md`):

1. **Statické texty UI (`t()`):**
   - Všetky statické texty vo frontend aj backend kóde vkladaj **výlučne cez `t('key')`** (prípadne s premennými `t('key', ['name' => $val])`).
   - **Nikdy neposielaj do `t()` predvolený fallback text ako druhý parameter** – chýbajúci preklad musí svietiť ako `#Trans.key`, aby okamžite upozornil na chýbajúci záznam.
   - Preklady pre všetky aktívne jazyky bezodkladne pridávaj do `database/seeders/TranslationSeeder.php`.
2. **Dynamické viacjazyčné dáta modelov (`JSON` stĺpce):**
   - Všetky verejne zobrazované textové atribúty modelov a číselníkov ukladaj do `JSON` stĺpcov (`{"sk": "...", "en": "..."}`) s traitom `App\Traits\HasJsonTranslations`. Nikdy nevytváraj stĺpce typu `name_sk`, `name_en`.

## 4. Možnosti v poliach (Settings - Selects)

- Ak v modeli existuje pole, ktoré vyberá z viacerých možností (napr. category, typ atď.), **tieto možnosti sa nesmú "nadrátať" napevno v kóde**.
- Možnosti sa ukladajú v databázovej tabuľke `settings`.
- **Admin správa:** Pre model `Setting` musí vždy existovať plnohodnotné administrátorské CRUD rozhranie (Livewire komponenty a routy pre Index, Show, Create, Edit), presne rovnako, ako je to definované v bode 1.
- **VAROVANIE PRE AGENTA (Settings Structure):** V `example-project` obsahuje komponent `Setting\Index` pole `$settingsStructure` so vzorovými dátami. **TOTO SÚ IBA PRÍKLADY.** Pri implementácii Settings do nového projektu **musíš analyzovať skutočné modely cieľového projektu** a naplniť `$settingsStructure` iba takými kategóriami a modelmi, ktoré naozaj v danom projekte figurujú.
- **Viacjazyčné názvy možností (JSON stĺpec `label`):**
    - V tabuľke `settings` slúžia stĺpce `flag`, `key` a `value` na definíciu vnútorného kľúča (napr. `flag = 'product'`, `key = 'category'`, `value = 'electronics'`), kým zobrazovaný viacjazyčný názov sa ukladá priamo do **JSON stĺpca `label`** s traitom `HasJsonTranslations`.
    - Formuláre pre ukladanie Settings (`Create`/`Edit`) nesmú používať pevné premenné `value_sk`, `value_en`, ale dynamické pole `public array $label = []` generované podľa aktívnych jazykov (`app_langs()`).
- Hodnoty do selectboxu ťahaj z databázy cez kešované `Setting::getList('product', 'category')`, ktoré vráti asociatívne pole `['electronics' => 'Elektronika']` preložené do aktuálneho jazyka.

## 5. HTML, CSS a Design

- Pre šablóny **vždy použi Tailwind CSS**.
- Design, štýly a použité komponenty však **musia byť vždy zosúladené s aktuálnym dizajnom** stránky každého konkrétneho projektu, v ktorom operuješ. Nenavrhuj univerzálny vzhľad, ale adaptuj kód tak, aby vizuálne splýval s hotovými zobrazeniami klienta.
- **Pozor na ukážkové Blade komponenty a Layouty:** Šablóny v `example-project` môžu obsahovať špecifické Blade komponenty (napr. `<x-form.button>`, `<x-admin.breadcrumbs>`) alebo konkrétne layouty (napríklad `#[Layout('components.layouts.admin')]`). **Tieto komponenty a layouty sú len zástupné (PLACEHOLDERS).** Nikdy nevytváraj ani nekopíruj tieto UI prvky ani názvy layoutov do cieľového projektu, ak tam už neexistujú.
    - Ak cieľový projekt používa iný layout, musíš použiť ten existujúci (a overiť, že vykresľuje `$page_title` a spracúva event `flash-message`).
    - Ak cieľový projekt nemá komponent `<x-form.button>`, použi štandardný HTML `<button>` s adekvátnymi Tailwind triedami podľa existujúceho vizuálu.
    - Účelom ukážkových šablón je ukázať **logiku dát a prepojení** (ako fungujú parametre a volania), absolútne nie ich presný vizuál ani HTML štruktúru. Vždy prispôsob svoj kód existujúcemu frontendu!
- **Zákaz nechráneného `{!! !!}` (XSS ochrana):** Používateľský HTML obsah (WYSIWYG, PageBuilder) nikdy nevypisuj cez `{!! $content !!}` bez serverovej HTML sanitizácie.
- **Zákaz natívnych upozornení (`wire:confirm`):** Nikdy nepoužívaj natívne upozornenia prehliadača ako `wire:confirm="Naozaj zmazať?"`, `alert()` alebo `confirm()`. Na potvrdzovanie akcií (napr. zmazanie) **vždy použi alebo vytvor dizajnové modálne okno** (napr. cez AlpineJS alebo samostatný Livewire komponent ako `<livewire:admin.translation.delete />`), ktoré je vizuálne zosúladené s projektom.
- **SPA Navigácia (`wire:navigate` v Livewire 4):** Interné odkazy v administrácii a klientskej zóne (sidebar, breadcrumbs, nav-linky) musia povinne využívať `wire:navigate` pre okamžitú SPA odozvu bez preblikávania stránky. Výnimkou sú výhradne binárne sťahovania súborov (PDF, XLSX), odhlasovanie (`logout`) a externé URL. Je prísne zakázané odstraňovať `wire:navigate` ako pokus o opravu problémov s prihlasovaním alebo session.

## 6. Pravidlá pri zmene štruktúry modelu (Pridanie/Odobratie poľa)

Vždy, keď dostaneš požiadavku pridať nové pole alebo odobrať existujúce pole z modelu, **musíš povinne vykonať krížovú kontrolu** so všetkými našimi štandardmi a zabezpečiť aktualizáciu na všetkých relevantných miestach:

1. **Databáza:** Aktualizovať príslušnú migráciu alebo vytvoriť novú (pre viacjazyčné pole použiť `$table->json(...)`).
2. **Model:** Pridať/odobrať pole z vlastnosti `$fillable` (prípadne `$translatable`) a v prípade potreby upraviť metódu `casts(): array`.
3. **Livewire Classy (Create & Edit):**
    - Pridať/odobrať verejnú premennú (`public string $pole = '';` alebo `public array $pole = [];` pre JSON preklad).
    - Upraviť validácie pomocou atribútu `#[Validate]` alebo v metóde `protected function rules(): array`.
    - **Drafts:** Pridať/odobrať pole zo zoznamu premenných v metóde `$this->saveDraft(...)`, ktorá sa volá v hooku `updated()`.
4. **Views (Livewire Blade):** Pridať/odobrať príslušný HTML prvok vo formulároch (vrátane prepojenia `wire:model.live.blur`). Taktiež overiť, či ho netreba zobraziť/odstrániť aj v prehľade (Index) a v detaile (Show).
5. **Preklady:** Ak nové pole vyžaduje vlastný statický text (napr. label formulára, názov stĺpca), musíš ho zaobaliť do `t()` funkcie a doplniť do seederov.
