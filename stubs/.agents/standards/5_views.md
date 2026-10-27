# 5. Pohľady (Views)
- **Zmysluplné pomenovanie pohľadov**
  - Pohľady pomenovávajte podľa zdroja, ktorý zobrazujú.
  - Z dôvodu prehľadnosti a lepšej štruktúry organizujte pohľady do priečinkov podľa funkcií alebo modulov.
  - Príklady: `user\dashboard.blade.php`, `user\edit.blade.php` atď.

- **Class Mode Separácia (Livewire):**
  - Striktne oddeľujte logiku, ktorá patrí do PHP tried (`app/Livewire/*.php`) a vzhľad (`resources/views/livewire/*.blade.php`). Do Blade šablón nevkladajte PHP logiku, ako by to bolo možné pri SFC/Volt prístupe.

- **Udržujte pohľady jednoduché**
  - Vyhnite sa používaniu surovej (raw) PHP logiky v Blade súboroch. Namiesto toho pre zložitú logiku používajte helper funkcie alebo view composers.
  - Vyhnite sa vkladaniu zložitej logiky alebo databázových dopytov do Blade súborov. Na prípravu dát používajte controllery alebo Livewire komponenty.
  - Zlý príklad (Bad example):

```php
@php
	$users = DB::table('users')->where('active', true)->get();
@endphp
<ul>
	@foreach ($users as $user)
		<li>{{ $user->name }}</li>
	@endforeach
</ul>
```

  - Dobrý príklad (Good example):

```php
// In Controller
$users = User::where('active', true)->get();
return view('users.index', compact('users'));

// In Blade
<ul>
	@foreach ($users as $user)
		<li>{{ $user->name }}</li>
	@endforeach
</ul>
```

- Dodržujte princíp DRY (Don't Repeat Yourself). Opakovaniu kódu predchádzajte používaním komponentov alebo includov.
- **Používajte Blade Komponenty**
  - Pre znovupoužiteľné časti šablón (napr. tlačidlá, modály, formuláre) používajte Blade komponenty.
  - **Livewire Komponenty:** Pre pripájanie Livewire komponentov vždy používajte modernú HTML-like tagovú syntax: `<livewire:user-profile :user-id="$user->id" />` namiesto zastaraného `@livewire('user-profile')`. Do vnoreného komponentu odovzdávajte skalárne ID, nie celý Eloquent model.

```html
<form id="company_form" class="entity-company-form sm:max-w-2xl mx-auto mt-5" wire:submit="save">
	<x-form.text name="name" :label="t('company.name')" wire:model.live.blur="name.sk" :required="true" />
```

- **Livewire 4 a UI/UX Vzory:**
  - **Auto-save / Real-time formuláre (Draft Table Pattern):** Pre dosiahnutie vynikajúceho UX ukladajte formuláre na pozadí, akonáhle používateľ vyplní pole (on blur).
    - Nevytvárajte zbytočné `nullable` polia ani neukladajte falošné záznamy (napr. `'Nový produkt'`) priamo do hlavnej tabuľky modelu.
    - Použite dedikovanú tabuľku `drafts (id, user_id, model_type, model_id, payload)` cez trait `HasDrafts`, kde do `payload` stĺpca (JSON/array cast) ukladáte iba vyplnené formulárové polia pri zavolaní `updated()` hooku. Pre vnorené formuláre s viacerými rodičmi využite voliteľný kontextový kľúč.
    - Ak formulár pri vytváraní (`Create`) vyžaduje okamžitý upload obrázkov alebo médií (napr. galéria, PageBuilder), nahrávané súbory viažte polymorfne na záznam `Draft` a pri ostrom uložení v `save()` ich prelinkujte na novovytvorený model.
  - **Optimalizácia:** Atribúty `wire:model` sú predvolene deferované (odložené). Pre okamžitú odozvu pri vyhľadávaní využite explicitne `wire:model.live.debounce.300ms="search"`. Pre formulárové polia, ktoré sa majú zvalidovať a auto-uložiť po opustení inputu, použite `wire:model.live.blur`.
  - **Odosielanie formulárov:** Pri formulároch v Livewire vždy používajte `wire:submit="save"`.
  - **UX Loading indikátory:** Vždy používajte indikátory načítavania `wire:loading` a `wire:target="save"` pre asynchrónne akcie, aby mal používateľ okamžitú spätnú väzbu a neodoslal formulár dvakrát.

- **Blade Komponentové Layouty v Livewire 4:**
  - Pre layouty používajte moderné **Blade komponenty** v `resources/views/components/layouts/` (napr. `app.blade.php`, `admin.blade.php`) s výpisom `{{ $slot }}` a v Livewire triede ich priraďujte atribútom `#[Layout('components.layouts.admin')]`. Zastarané dedenie `@extends()` / `@section()` v nových Livewire komponentoch nepoužívajte.
  - Každý layout **musí**:
    1. Vykresľovať dynamický titulok z `BaseLivewireComponent::setTitle()` cez `<title>{{ isset($page_title) && $page_title ? $page_title . ' | ' . config('app.name') : config('app.name') }}</title>`.
    2. Obsahovať globálny UI listener na browser event `flash-message` (`@flash-message.window` v AlpineJS) aj session flash správy, aby fungovali notifikácie z `$this->flashSuccess()` s presmerovaním aj bez presmerovania.
    3. Obsahovať `@stack('scripts')` pre prípadné špecifické skripty podstránok.

```html
{{-- resources/views/components/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app_locale() }}">
<head>
	<title>{{ !empty($page_title) ? $page_title . ' | ' . config('app.name') : config('app.name') }}</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
	<main>{{ $slot }}</main>
	@stack('scripts')
</body>
</html>
```

- **Sanitizácia výstupu a ochrana pred Stored XSS:**
  - Na komentáre používajte syntax Blade `{{-- --}}` namiesto klasického HTML `<!-- -->`.
  - Na escapovanie obsahu vždy štandardne používajte `{{ }}`.
  - **Prísne pravidlo pre `{!! !!}`:** Nikdy nevypisujte obsah zadávaný používateľom alebo predajcom (napr. WYSIWYG editor, PageBuilder bloky, HTML popis produktu/profilu) cez `{!! $content !!}` bez predchádzajúcej **serverovej HTML sanitizácie** (whitelist bezpečných HTML tagov a atribútov, odstránenie `<script>`, `<iframe>`, `on*` atribútov a `javascript:` odkazov).

```html
{{-- Escaped plain text --}}
<p>{{ $product->name }}</p>

{{-- Rich HTML sanitized on server before rendering --}}
<div class="prose">{!! clean_html($product->content) !!}</div>
```

- **Optimalizácia cyklov (Loops)**
  - Vyhnite sa náročnému spracovaniu dát vo vnútri Blade cyklov; dáta si vždy vopred pripravte v controlleri.
  - Zlý príklad (Bad example):

```php
@foreach ($users as $user)
    <p>{{ $user->posts()->count() }}</p>
@endforeach
```

  - Dobrý príklad (Good example):

```php
// In Controller
$users = User::withCount('posts')->get();

// In view
@foreach ($users as $user)
	<p>{{ $user->posts_count }}</p>
@endforeach
```

- **Podmienečné triedy (Conditional Classes)**
  - Na dynamické štýlovanie používajte Blade syntax pre podmienečné triedy.
  - Taktiež je dobré mať v každom `<body>` tagu základné informácie o type stránky (napr. 'page', 'user', 'uid-864' a podobne).
  - Príklad:

```html
<body class="@yield('body_class') bg-white text-gray-800 dark:bg-gray-800 dark:text-gray-200">
	...
	<li class="{{ $user->isActive() ? 'active' : '' }}">
		{{ $user->name }}
	</li>
```

- **Používajte `route()` helper v Blade:**
  - Namiesto natvrdo napísaných (hardcoded) adries vždy používajte pomenované routy alebo helpery.
  - Vyhnite sa statickým URL adresám, s výnimkou adries smerujúcich mimo aplikáciu.
  - Pre adresy smerujúce mimo projektu používajte atribút `target="_blank"`.
  - Zlý príklad (Bad example):

```html
<a href="/login">Login</a>
```

  - Dobrý príklad (Good example):

```html
<a href="http://example.com" target="_blank">Outgoing</a>
<a href="{{ route('login') }}">Login</a>
```

- Používajte minimalizované inline SVG ikony a iné vektorové grafiky.

```html
	<x-svg.close class="h-8" alt="Close button" />
```

```xml
{{-- Close button --}}
<svg viewBox="0 0 14 14" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" {{
	 $attributes->merge(['class' => "
		inline
		transition
		duration-75
		flex-shrink-0
		rtl:rotate-180
	"]) }}>
	<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 0.999939L13 13" />
	<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 13L13 0.99996" />
</svg>
```


- **Lokalizácia textov**
  - Na preklad statických textov UI v Blade šablónach nepoužívajte `@lang` ani `__()`, ale výhradne náš prekladový helper `t('key')` (prípadne s poľom premenných `t('key', ['name' => $val])`).
  - Nikdy neposielajte do `t()` predvolený fallback text ako druhý parameter – chýbajúci kľúč sa musí zobraziť ako `#Trans.key`. Kompletné pravidlá pre statické a dynamické JSON preklady nájdete v [8_localization.md](./8_localization.md).

- **Ladenie chýb (Debugging) cez `@dd()` alebo `@dump()`**
  - Na ladenie a výpis premenných priamo v Blade šablónach počas lokálneho vývoja používajte `@dd` alebo `@dump` (pred odovzdaním kódu ich vždy odstráňte).

```php
@dd($users)
```


---
### Čo ďalej:
6. Pozrite si štandardy pre [Validácie](./6_validations.md).
