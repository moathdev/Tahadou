<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ParticipantController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Language Switcher
|--------------------------------------------------------------------------
*/
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'en'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Landing Page — Create Group
|--------------------------------------------------------------------------
*/
Route::get('/', [GroupController::class, 'index'])->name('home');
Route::post('/groups', [GroupController::class, 'create'])->name('group.create');

/*
|--------------------------------------------------------------------------
| Admin Entry — find group by UUID
|--------------------------------------------------------------------------
*/
Route::get('/admin',  [AdminController::class, 'findForm'])->name('admin.find');
Route::post('/admin', [AdminController::class, 'findAndLogin'])->name('admin.find.submit');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin/{uuid}')->name('admin.')->group(function () {
    Route::get('/login',     [AdminController::class, 'loginForm'])->name('login');
    Route::post('/login',    [AdminController::class, 'login'])->name('login.submit');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::delete('/participants/{participant}', [AdminController::class, 'removeParticipant'])->name('participants.remove');
    Route::post('/lock',          [AdminController::class, 'toggleLock'])->name('lock');
    Route::post('/draw',          [AdminController::class, 'executeDraw'])->name('draw');
    Route::get('/download-excel', [AdminController::class, 'downloadExcel'])->name('download.excel');
});

/*
|--------------------------------------------------------------------------
| Participant Registration Routes
|--------------------------------------------------------------------------
*/
Route::prefix('join/{uuid}')->name('participant.')->group(function () {
    Route::get('/',       [ParticipantController::class, 'show'])->name('register');
    Route::post('/',      [ParticipantController::class, 'register'])->name('register.submit');
    Route::get('/thanks', [ParticipantController::class, 'success'])->name('success');
});
