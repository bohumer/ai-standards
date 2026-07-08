# 1. Smerovanie (Routing)
- **Definície ciest (Routes)**:
  - Cesty definujte v `routes/web.php` pre web a v `routes/api.php` pre API.
  - Pre interaktívne UI nepoužívajte bežné controllery, namiesto toho smerujte priamo na Livewire komponenty:
```php
// Good example: Full-page Livewire component
Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
```
  - **Autentifikácia (Fortify):** Nedefinujte vlastné routy ani controllery pre prihlasovanie a registráciu. Nechajte to na Laravel Fortify a zamerajte sa iba na vykreslenie Livewire komponentov v rámci `FortifyServiceProvider` (napr. `Fortify::loginView(...)`).
  - Cesty vždy pomenovávajte pomocou metódy `name()`.
  - Používajte zmysluplné názvy ciest (napr. `user.profile` namiesto `profile`).

```php
// Good example: Defining a single route
Route::get('/author/show', \App\Livewire\Author\Show::class)->name('author.show');

// Bad example
Route::get('/author/profile', [UserController::class, 'show'])->name('author-profile');
```
- Používajte **RESTful Resource** Controllery pre CRUD operácie (ak nevyužívate Livewire komponenty):

```php
// Good example: Resource routes for CRUD
Route::resource('user', UserController::class)->except(['show']);
```

  Toto automaticky vytvorí cesty ako:

	GET /user → index
	POST /user/ → store
	GET /user/{slug} → edit
	POST /user/{slug} → update
	A ďalšie...

  - Ak potrebujete vylúčiť niektoré metódy:

```php
Route::resource('post', PostController::class)->except(['create', 'edit']);
```

- **Zoskupovanie súvisiacich ciest (Group Related Routes)**
  - Používajte skupiny ciest (`route groups`) na logické usporiadanie a zoskupenie súvisiacich ciest.
  - Skupiny využívajte pre middlewary, prefixy alebo menné priestory (namespaces).

```php
// Good example: Grouped routes
Route::group(['prefix'=>'admin', 'as'=>'admin.'], function() {
	Route::controller(AdminController::class)->group(function() {
		Route::get(...)
	});
});
```

  - Vyhnite sa opakovaniu podobnej logiky vo viacerých cestách. Namiesto toho použite skupiny ciest alebo znovupoužiteľné metódy.

```php
// Bad example:
Route::get('/admin/users', [AdminController::class, 'users'])->middleware('admin');
Route::get('/admin/posts', [AdminController::class, 'posts'])->middleware('admin');

// Good example:
Route::prefix('admin')->middleware('admin')->group(function () {
	Route::get('/users', [AdminController::class, 'users']);
	Route::get('/posts', [AdminController::class, 'posts']);
});
```

- **Efektívne využívanie parametrov ciest (Route Parameters)**
  - Parametre definujte s jasnými názvami a obmedzeniami (constraints).
  - Používajte `{}` na definovanie povinných parametrov a pridajte obmedzenia pomocou metódy `where`.

```php
Route::get('/comment/{id}', [CommentController::class, 'show'])->where('id', '[0-9]+')->name('comment.show');
```

- Ak je to potrebné, používajte voliteľné parametre:

```php
Route::get('/profile/{user?}', [ProfileController::class, 'show'])->name('profile.show');
```


---
### Čo ďalej:
2. Pozrite si štandardy pre [Controllery](https://git.greksak.sk/Michal/coding-standard/src/branch/main/docs/2_controllers.md).
