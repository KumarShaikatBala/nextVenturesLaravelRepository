<?php

use Illuminate\Support\Facades\Route;
use Dedoc\Scramble\Scramble;
Route::get('/', function () {
    return view('welcome');
});


Scramble::registerUiRoute(path: 'docs/api')->name('scramble.docs.ui');

Scramble::registerJsonSpecificationRoute(path: 'docs/api.json')->name('scramble.docs.document');
