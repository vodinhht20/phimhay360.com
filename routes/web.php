<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// admin/plugin/ophim-crawler/crawl

Route::get('aaa', function() {
    $a = Http::get('https://ophim1.com/phim/love-syndrome-iii');
dd($a->json());
});