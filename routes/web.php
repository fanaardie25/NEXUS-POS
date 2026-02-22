<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::livewire('/cashier', 'pages::cashier.index')->middleware('auth');
