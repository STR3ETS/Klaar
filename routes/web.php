<?php

use App\Http\Controllers\EntryController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // Entries
    Route::get('entries', [EntryController::class, 'index'])->name('entries.index');
    Route::get('entries/create', [EntryController::class, 'create'])->name('entries.create');
    Route::get('entries/{entry}', [EntryController::class, 'show'])->name('entries.show');
    Route::patch('entries/{entry}/finalize', [EntryController::class, 'finalize'])->name('entries.finalize');
    Route::delete('entries/{entry}', [EntryController::class, 'destroy'])->name('entries.destroy');
});

require __DIR__.'/auth.php';
