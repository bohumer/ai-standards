# Pravidlá projektu (Project Rules)

> **Globálne architektonické štandardy:** Pred akoukoľvek úpravou kódu povinne dodržiavaj hlavné architektonické štandardy definované v [`.agents/AGENTS.md`](./.agents/AGENTS.md), podrobné pravidlá v priečinku [`.agents/standards/`](./.agents/standards/) (`1_routing.md` až `8_localization.md`) a vzorové implementácie v [`.agents/example-project/`](./.agents/example-project/). Nižšie uvedené pravidlá platia špecificky pre tento projekt.

## Príprava Git Commitov (Commit Messages)

Vždy, keď ťa používateľ vyzve na roztriedenie a prípravu zmien do Git commitov, **musíš automaticky vytvoriť implementačný plán (`implementation_plan.md`)**, v ktorom:

1. Navrhneš logické rozdelenie upravených súborov do jednotlivých commitov.
2. Ku každému commitu navrhneš presný text správy (commit message) v angličtine.
3. **Štýl správy:** Commit messages musia vždy presne dodržiavať používateľov špecifický formát `PREFIX - popis`.
    - **PREFIX:** Musí byť vždy verzálkami (napr. `ADD`, `EDIT`, `UPDATE`, `FIX`, `DELETE`, `REFACTOR`). Prefix vyberaj podľa povahy zmien v commite.
    - **Oddelovač:** Pomlčka s medzerami z oboch strán (`-`).
    - **popis:** Stručný a výstižný popis toho, čoho sa commit týka a čo sa robilo. Zvyčajne malými písmenami (okrem prvých písmen názvov komponentov ak je to vhodné).
    - _Príklad správneho formátu:_ `ADD - implement settings module with drafts and translations` alebo `EDIT - layout localization and admin login redirection`. Nikdy nepíš zbytočne dlhé zoznamy do odrážok do textu commitu, ak to nie je vyslovene nutné. Vždy sa zameraj iba na jednoriadkový hlavný message podľa tohto vzoru.

## Správa prekladov (TranslationSeeder)

Keď pridáš nové textové kľúče `t()` do Blade šablón alebo Livewire tried, pridaj ich vždy do `database/seeders/TranslationSeeder.php`. Samostatne ich však zapíš aj do dočasného temp seeder súboru, ktorý spusti a ihneď po zapísaní do databázy vymaž – nepotrebujeme zakaždým spúšťať obrovský seeder kvôli pár novým kľúčom.

## Databázové migrácie (Database Migrations)

Pre každú zmenu databázovej schémy vždy vytvor novú aditívnu migráciu (na local, test aj production serveri). Je prísne zakázané spätne modifikovať už spustené migrácie alebo spúšťať `migrate:fresh`, nakoľko databáza obsahuje importované historické dáta.

## Architektúra Autentifikácie a Správy Sessions (Auth Architecture & Standards)

Aby sa predišlo opakovanému zlyhávaniu administrátorského prístupu a neúmyselnému presmerovaniu na prihlasovaciu stránku („login loop“), musia byť trvalo dodržiavané nasledujúce pravidlá:

1. **Jediný Guard (`web`):** V celom projekte sa používa výhradne guard `web` (žiadny oddelený `admin` guard).
2. **Konzistentný Middleware Stack pre Administráciu:** Všetky administrátorské routy a komponenty musia byť chránené jednotným middleware stackom `['auth', 'verified', 'admin']`.
3. **Model `User` a E-mail Verifikácia:**
   - Pole `'email_verified_at'` musí zostať trvalo súčasťou `User::$fillable`, aby pri seedovaní ani programovom vytváraní administrátorov/používateľov nedochádzalo k tichému zahodeniu timestampu verifikácie.
   - V middleware `EnsureEmailIsVerified` je implementované pravidlo, že používateľ s overenou rolou `admin` je považovaný za dôveryhodného systémového správcu a nesmie byť nikdy blokovaný výzvou `/verify-email`.
   - Pri bežnej registrácii (`register.blade.php`) aj zápisoch sa do session ukladá flag `first_login_bypass_verification`, umožňujúci okamžitý prístup do klientskej zóny pred potvrdením linku z e-mailu.
4. **Administrátorské Routy a URL Navigácia (Livewire 4 SPA):**
   - Pre skratky `/admin` a `/admin/login` existujú explicitné presmerovania (`admin` -> `admin.dashboard`, `admin/login` -> `/login`), aby nespadávali do catch-all fallback controllerov a nepresmerúvali administrátora na homepage.
   - Všetky interné administrátorské odkazy v navigácii (`<x-admin.sidebar-link>`, `<x-admin.breadcrumbs>`, `<x-admin.nav-link>`) povinne používajú `wire:navigate` pre okamžitú SPA navigáciu v Livewire 4.
   - `wire:navigate` sa nesmie používať výhradne na binárne sťahovania súborov (PDF/XLSX exporty), odhlasovanie (`logout`) a externé odkazy.
   - Je prísne zakázané odstraňovať `wire:navigate` zo sidebar liniek alebo nahrádzať štandardný routing provizórnymi frontendovými skriptami ako riešenie chýb autentifikácie.
5. **Session a Cookie Management:**
   - Prihlasovací Livewire Volt komponent vykonáva jediné bezpečné volanie `SpamGuard::attemptLogin()` s `Auth::attempt()`, ktoré korektne migruje session ID bez duplicitných race conditions.
   - Regresné integračné testy v `tests/Feature/Auth/AdminAuthSessionRegressionTest.php` sa musia spúšťať pri každej úprave autentifikácie a session logiky.
