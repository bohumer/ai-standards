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
  - **Livewire Komponenty:** Pre pripájanie Livewire komponentov vždy používajte modernú HTML-like syntax: `<livewire:user-profile :user="$user" />` namiesto zastaralého `@livewire('user-profile')`.

```html
<form id="company_form" class="entity-company-form sm:max-w-2xl mx-auto mt-5" method="POST" action="{{ route('company.update', $model) }}">
	@method( $model->name ? 'PATCH' : 'PUT' )
	@csrf
	<x-form.text name="name" :label="t('Company_name')" :value="old('name', $model->name ?? '')" :required="true" />
```

- **Livewire 4 a UI/UX Vzory:**
  - **Auto-save / Real-time formuláre (Draft Table Pattern):** Pre dosiahnutie vynikajúceho UX ukladajte formuláre na pozadí, akonáhle používateľ vyplní pole (on blur). 
    - Nevytvárajte zbytočné `nullable` polia v hlavnej tabuľke.
    - Použite dedikovanú tabuľku `drafts (id, user_id, model_type, model_id, payload)`, kde do `payload` stĺpca (JSON/array cast) ukladáte celý stav `$this->except('productId')` pri každom zavolaní `updated()` hooku.
    - Odošlite celú požiadavku do DB len raz prostredníctvom `$this->fill($draft->payload)` v metode `mount()`.
  - **Optimalizácia:** Atribúty `wire:model` sú predvolene deferované (odložené). Pre okamžitú odozvu pri písaní využite explicitne `wire:model.live.debounce.300ms="query"`. Pre bežné polia, ktoré sa majú zvalidovať a auto-uložiť po opustení inputu, použite `wire:model.live.blur`.
  - **Odosielanie formulárov:** Pri klasických formulároch vždy používajte `wire:submit="save"`, čo automaticky zabráni obnove stránky a vykoná akciu.
  - **UX Loading indikátory:** Vždy používajte indikátory načítavania `wire:loading` pre asynchrónne akcie (napr. `<span wire:loading>Ukladám...</span>`), aby mal používateľ okamžitú spätnú väzbu.

- **Využívajte rozloženia (Layouts) a sekcie (Sections)**
  - Používajte `@extends()` a `@section()`, aby ste sa vyhli duplicite a vytvorili konzistentnú štruktúru naprieč stránkami.
  - Na znovupoužiteľné časti (napr. hlavičky, pätičky a čiastočné šablóny - partials) používajte `@include()`.
  - Súbor layoutu: layouts/app.blade.php
```html
<html>
<head>
	<title>@yield('title')</title>
</head>
<body>
	<x-svg.logo_main' size="medium" />
	@include('layout.header')
	<div class="content">
		@yield('content')
	</div>
```

  - Pohľad potomka (Child view): 

```php
@extends('layouts.app')
@section('title', t('title_home_page'))
@section('content')
	<h1>Welcome to the Home Page</h1>
@endsection
```

- **Pre väčšiu flexibilitu používajte `@yield()` a `@stack()`**
  - `@yield()` používajte pre zástupné miesta, ktoré sa v rámci stránky použijú iba raz.
  - `@stack()` používajte pre sekcie, ktoré môžu obsahovať viacero položiek (napríklad skripty).
  - V Blade layoute:

```php
@yield('content')
@stack('scripts')
```
  - V pohľade potomka: 

```php
@push('scripts')
	<script src="/js/specific.js"></script>
@endpush
```

- **Sanitizácia výstupu**
  - Na komentáre používajte syntax blade `{{-- --}}` namiesto klasického html `<!-- -->`. Blade syntax zabezpečí, že sa komentár neobjaví vo finálnom HTML výstupe.
  - Na escapovanie obsahu a ochranu pred XSS útokmi používajte syntax `{{ }}`.
  - Syntax `{!! !!}` používajte výlučne pre dôveryhodný obsah. Nikdy ju nepoužívajte pre dáta, ktoré zadáva používateľ.

```html
{{-- User section --}}
<p>{{ $user->name }}</p>  <!-- Escaped -->
<p>{!! $user->bio !!}</p> <!-- Trusted -->
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
  - Na preklad textov nepoužívajte funkcie `@lang` ani `trans()`. Interné preklady Laravelu sa nachádzajú v `resources/lang/en/\*\*\*.php`.
  - Namiesto toho používajte náš vlastný prekladový helper `t()`. Používame totiž preklady upraviteľné v databáze namiesto tých natvrdo zakódovaných v Laraveli.

- **Ladenie chýb (Debugging) cez `@dd()` alebo `@dump()`**
  - Na ladenie a výpis premenných priamo v Blade šablónach používajte `@dd` alebo `@dump`.

```php
@dd($users)
```


---
### Čo ďalej:
6. Pozrite si štandardy pre [Validácie](https://git.greksak.sk/Michal/coding-standard/src/branch/main/docs/6_validations.md).
