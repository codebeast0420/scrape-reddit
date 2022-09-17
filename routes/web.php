<?php

use App\Http\Livewire\Products;
use App\Http\Livewire\ScraperForm;
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

Route::get('/', Products::class);
Route::get('/scraper', ScraperForm::class);
