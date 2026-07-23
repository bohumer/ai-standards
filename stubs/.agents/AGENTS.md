# Pravidlá pre prácu s modelmi a Livewire (Draft Table Pattern)

Tento súbor obsahuje špecifické pravidlá (Draft Pattern, Settings, preklady), ktoré musíš bezvýhradne dodržiavať pri vytváraní a úprave akéhokoľvek modelu v tomto systéme.

> **DÔLEŽITÉ:** Okrem tohto súboru **musíš zohľadňovať aj všetky postupy a štandardy zadefinované v súboroch v priečinku `standards/`** (napríklad prísny zákaz priraďovania celých Eloquent modelov do public properties v Livewire podľa `3_models.md`). Pravidlá z `standards/` tvoria základ a tento súbor (AGENTS.md) ich dopĺňa o špecifické funkcie pre administráciu.
> **Špecifické pravidlá projektu:** Vždy pred začatím práce s kódom skontroluj prítomnosť súboru `project_rules.md` v adresári `.agents/`. Ak existuje, tento súbor obsahuje dodatočné špecifické pravidlá a pokyny platné výhradne pre daný projekt, ktoré musíš dodržiavať nad rámec týchto globálnych štandardov. `AGENTS.md` nikdy neupravuj s projektovo-špecifickými zmenami.

## 1. Architektúra modelu a Views
**Základná trieda (BaseLivewireComponent):**
Všetky tvoje Livewire komponenty (Index, Show, Create, Edit) **musia** dediť od `App\Support\BaseLivewireComponent` (nikdy nie priamo od `Livewire\Component`). Tento base komponent ti poskytuje dôležité funkcie pre audit log, navigáciu a notifikácie, ktoré musíš logicky a povinne využívať:
1. **Titulok stránky:** V metóde `mount()` vždy nastav dynamický titulok cez `$this->setTitle(t('nazov_stranky') . ' ' . $dalsia_premmenna)`. Nevpisuj titulok natvrdo do HTML.
2. **Flash a Audit Log:** Zakaždým, keď používateľ niečo vytvorí (`save` v Create), upraví (`save` v Edit) alebo vymaže (`delete`), musíš zavolať `$this->flashSuccess(t('vhodny_kluc'))`. Táto metóda nielen ukáže používateľovi správu, ale automaticky zapíše túto udalosť do databázy do audit logu. Ak dôjde k chybe, použi `$this->flashError()`. Výnimkou je priebežné ukladanie draftov (konceptov), ktoré logovať **nesmieš**.
3. **Návrat späť:** Pri tlačidlách "Zrušiť" alebo pri úspešnom uložení formulára používaj na presmerovanie späť na zoznam metódu `$this->redirectBackWithParams('admin.model.index')`.

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
- **Admin správa:** Pre model `Setting` musí vždy existovať plnohodnotné administrátorské CRUD rozhranie (Livewire komponenty a routy pre Index, Show, Create, Edit), presne rovnako, ako je to definované v bode 1.
- **VAROVANIE PRE AGENTA (Settings Structure):** V `example-project` obsahuje komponent `Setting\Index` pole `$settingsStructure` so vzorovými dátami (napr. `Article`, `Course`, `StudyGroup`). **TOTO SÚ IBA PRÍKLADY.** Tieto modely v iných projektoch s najväčšou pravdepodobnosťou neexistujú. Pri implementácii Settings do nového projektu **musíš analyzovať skutočné modely cieľového projektu** a naplniť premennú `$settingsStructure` (alebo vymyslieť iný dynamický spôsob) iba takými kategóriami a modelmi, ktoré naozaj v danom projekte figurujú. Nikdy nekopíruj tieto vzorové kategórie do iných projektov.
- **Integrácia s prekladmi (Translations):** 
  - V tabuľke `settings` slúžia stĺpce `flag`, `key` a `value` iba na definíciu vnútorného kľúča (napr. flag = `product`, key = `category`, value = `electronics`).
  - Formuláre pre ukladanie Settings (Create/Edit) musia priamo obsahovať textové polia pre preklady (napr. `value_sk`, `value_en`), ale tieto texty sa nesmú ukladať do modelu Setting.
  - Všetky preklady možností sa ukladajú výhradne do modelu `Translation`, kde prekladovým kľúčom je poskladaný reťazec `$flag.$key.$value` (napr. `product.category.electronics`).
- Hodnoty do selectboxu musíš ťahať z databázy cez `Setting::getList('product', 'category')`. Tieto vytiahnuté možnosti vo frontende povinne prekladaj cez funkciu `t()`.

## 5. HTML, CSS a Design
- Pre šablóny **vždy použi Tailwind CSS**.
- Design, štýly a použité komponenty však **musia byť vždy zosúladené s aktuálnym dizajnom** stránky každého konkrétneho projektu, v ktorom operuješ. Nenavrhuj univerzálny vzhľad, ale adaptuj kód tak, aby vizuálne splýval s hotovými zobrazeniami klienta.
- **Pozor na ukážkové Blade komponenty a Layouty:** Šablóny v `example-project` môžu obsahovať špecifické Blade komponenty (napr. `<x-form.button>`, `<x-admin.breadcrumbs>`) alebo konkrétne layouty (napríklad `#[Layout('layouts.admin')]`). **Tieto komponenty a layouty sú len zástupné (PLACEHOLDERS).** Nikdy nevytváraj ani nekopíruj tieto UI prvky ani názvy layoutov do cieľového projektu, ak tam už neexistujú. 
  - Ak cieľový projekt používa iný layout (napr. `layouts.app` namiesto `layouts.admin`), musíš použiť ten existujúci. 
  - Ak cieľový projekt nemá komponent `<x-form.button>`, použi štandardný HTML `<button>` s adekvátnymi Tailwind triedami podľa existujúceho vizuálu. 
  - Účelom ukážkových šablón je ukázať **logiku dát a prepojení** (ako fungujú parametre a volania), absolútne nie ich presný vizuál ani HTML štruktúru. Vždy prispôsob svoj kód existujúcemu frontendu!
- **Zákaz natívnych upozornení (wire:confirm):** Nikdy nepoužívaj natívne upozornenia prehliadača ako `wire:confirm="Naozaj zmazať?"`, `alert()` alebo `confirm()`. Tieto prvky kazia profesionálny vzhľad. Na potvrdzovanie akcií (napr. zmazanie) **vždy použi alebo vytvor dizajnové modálne okno** (napr. cez AlpineJS alebo samostatný Livewire komponent ako `@livewire('admin.translation.delete')`), ktoré je vizuálne zosúladené s projektom.

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

## 7. Model Policies a Oprávnenia (Autorizácia)
Pri vytváraní alebo úprave akéhokoľvek modelu musíš vždy skontrolovať, či pre neho existuje príslušná Policy trieda (napr. `ArticlePolicy`). Ak neexistuje, **musíš ju vytvoriť**.
- **Zákaz externých balíčkov:** Na správu oprávnení v tomto projekte **nepoužívaj** externé balíčky typu `spatie/laravel-permission` ani nevytváraj zložité dynamické matice oprávnení, pokiaľ si to používateľ vyslovene nevyžiada.
- **Hierarchické Roly:** Tento systém využíva jednoduchý, hierarchický prístup k rolám, ktoré sú definované ako číselné hodnoty (napr. 0 = System, 1 = Admin, 2 = Editor, 3 = Seller, 9 = User).
- **Používanie konštánt:** V `{Model}Policy` (alebo kdekoľvek inde v kóde) **nikdy nepoužívaj "magické čísla"**. Vždy používaj preddefinované konštanty z modelu `User` (napr. `User::ROLE_ADMIN`, `User::ROLE_EDITOR`).
- **Implementácia v Policy:** Každú novovytvorenú Policy triedu **musíš predvyplniť ukážkovým kódom pre základné akcie (view, create, update, delete)**, ktorý demonštruje kontrolu rolí a vlastníctva (napríklad `create` môže len admin; `update` a `delete` môže admin alebo vlastník záznamu - `user_id === $user->id`). **Tento ukážkový kód však vygeneruj zakomentovaný**. Vďaka tomu nebudú akcie predvolene blokované, no v prípade potreby si ich môžem jednoducho odkomentovať a upraviť. Zabezpečíš tak, že v Policy bude hneď pripravená štruktúra: `// return $user->role_id === User::ROLE_ADMIN;` a podobne.

## 8. Tvorba API Endpointov a API Resources
- **Nie plošne:** API endpointy sa **nevytvárajú** automaticky pre každý model. API navrhuj a implementuj len pre tie modely a akcie, ktoré budú reálne konzumované treťou stranou (napr. mobilná/VueJS aplikácia, externé integračné API, platobné brány). Pre internú administráciu spravovanú cez Livewire API nevytváraj.
- **API Resources:** Nikdy nevracaj surové Eloquent modely priamo z API controllerov. Vždy vytvor príslušný API Resource (napr. `ProductResource`), v ktorom presne zadefinuješ zoznam vracaných polí. Týmto predídeš nechcenému úniku citlivých interných dát (napr. hashované heslo, interné príznaky, logy).
- **Verziovanie:** Všetky API routy musia byť organizované a verziované (napr. v `routes/api/v1.php`), čo zabezpečí spätnú kompatibilitu pri budúcich aktualizáciách klientskej aplikácie.
- **Autentifikácia:** Pre SPA alebo natívnu aplikáciu (napr. VueJS) používaj prepojenie pomocou Laravel Sanctum (token-based pre natívne/mobilné appky, cookie-based pre SPA na rovnakej doméne).

## 9. Univerzálne Tabuľky a Výkon (Performance)
Pri správe a výpise dát v administrácii je absolútnou prioritou **rýchlosť a optimalizácia dopytov**, ako aj zachovanie univerzálneho UI:
- **Základ (WithUniversalTable):** Na implementáciu zoznamov vždy používaj trait `WithUniversalTable` a existujúce znovupoužiteľné Blade komponenty (ako `<x-admin.table.th>`, `<x-admin.table-filter>`, `<x-admin.table.bulk-actions>`). Nikdy neprogramuj tabuľkovú logiku pre každý model od nuly.
- **Výkon a Caching (Prevencia N+1):** Pri práci s tabuľkami sa prísne vyhýbaj vykonávaniu databázových dopytov vo vnútri cyklov (napr. preklady alebo ťahanie selectbox hodnôt). Na dynamické konfiguračné dáta a preklady (ako napr. metóda `Setting::getList()`) **vždy používaj perzistentnú cache cez `Cache::remember()`**. Predídeme tým situácii, kedy jeden Livewire request odpáli stovky redundantných dopytov a spomalí aplikáciu (N+1 query problem).
- **Hromadné Akcie a Modaly:** Logiku hromadných akcií (bulk actions) nenechávaj plne na frontendový JavaScript. Stav otvorenia okna a vybrané riadky riaď priamo cez Livewire (`$bulkActionModalShow`, `$selectedRows`). Pri modáloch sa vyhýbaj kolíziám s `click.outside` eventmi a natívnym JavaScript alertom.
- **Filtrovanie:** Dbaj na Livewire striktné typovanie (napríklad integer vs. string v zaškrtávacích políčkach). Aktívne filtre (vyhľadávanie a aplikované checkboxy) sa musia vždy graficky zobraziť aj nad tabuľkou vo forme "odznakov" (badges) s možnosťou vymazania konkrétneho filtra alebo zrušenia všetkých naraz cez funkciu `resetAllFilters()`. Ak nie sú aplikované žiadne filtre (napr. všetky checkboxy odznačené), výsledok databázového dopytu musí korektne vrátiť 0 riadkov.
