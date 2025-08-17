<?php

use App\Http\Controllers\GymkhanaController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->group(function () {
        Route::prefix('gincanas')
            ->name('gymkhana.')
            ->controller(GymkhanaController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}/editar', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

        Route::prefix('gincanas/{gymkhana_id}/equipes')
            ->name('gymkhana.teams.')
            ->controller(TeamController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{team_id}/editar', 'edit')->name('edit');
                Route::put('/{team_id}', 'update')->name('update');
                Route::delete('/{team_id}', 'destroy')->name('destroy');
            });

        Route::prefix('gincanas/{step_id}/fases')
            ->name('gymkhana.phases.')
            ->controller(PhaseController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{team_id}/editar', 'edit')->name('edit');
                Route::put('/{team_id}', 'update')->name('update');
                Route::delete('/{team_id}', 'destroy')->name('destroy');
            });
    });
