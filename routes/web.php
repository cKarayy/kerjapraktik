<?php

use Illuminate\Support\Facades\Route;

Route::get('/admin/welcome', function () {
    return view('admin.welcome');
});


// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');