<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('auth.login');
});

Route::get('/auth/login', function () {
    return view('auth.login');
})->name('auth.login');

Route::get('/me', function () {
    return view('auth.me');
})->name('auth.me');


Route::get('/profiles', fn () => view('profiles.index'))->name('profiles.index');
Route::get('/profiles/{id}', fn ($id) => view('profiles.show', compact('id')))->name('profiles.show');
Route::get('/profiles/{id}/edit', fn ($id) => view('profiles.edit', compact('id')))->name('profiles.edit');