<?php

use Illuminate\Support\Facades\Route;

Route::post('/upload', 'Halimtuhu\ArrayFiles\FieldController@upload');
Route::delete('/delete', 'Halimtuhu\ArrayFiles\FieldController@delete');
