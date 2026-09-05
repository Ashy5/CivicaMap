<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/mapa', 'mapa')->name('mapa');
Route::view('/denuncias', 'denuncias')->name('denuncias');
Route::view('/servicios', 'servicios')->name('servicios');
Route::view('/login', 'auth.login')->name('login');
Route::view('/registro', 'auth.registro')->name('registro');
