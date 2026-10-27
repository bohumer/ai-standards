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
