<?php

use App\Livewire\ConferenceSignUpPage;
use Illuminate\Support\Facades\Route;

Route::get('/conference-sign-up', ConferenceSignUpPage::class);
Route::get('/', function () {
    return view('welcome');
});
