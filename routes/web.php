<?php

use App\Http\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;

Route::get('/',[GalleryController::class,'index'])->name('index');
Route::post('/upload',[GalleryController::class,'upload'])->name('upload');
Route::get('delete/{id}',[GalleryController::class,'delete'])->name('delete');