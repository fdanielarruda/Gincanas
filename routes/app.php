<?php

use App\Http\Controllers\GymkhanaController;
use App\Http\Controllers\GymkhanaResultController;
use App\Http\Controllers\JudgeController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->group(function () {
        Route::prefix('juizes')
            ->name('users.')
            ->controller(UserController::class)
            ->middleware(['admin'])
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}/editar', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

        Route::prefix('gincanas')
            ->name('gymkhana.')
            ->controller(GymkhanaController::class)
            ->middleware(['admin'])
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
            ->middleware(['admin'])
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{team_id}/editar', 'edit')->name('edit');
                Route::put('/{team_id}', 'update')->name('update');
                Route::delete('/{team_id}', 'destroy')->name('destroy');
            });

        Route::prefix('gincanas/{gymkhana_id}/provas')
            ->name('gymkhana.phases.')
            ->controller(PhaseController::class)
            ->middleware(['admin'])
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{phase_id}/editar', 'edit')->name('edit');
                Route::put('/{phase_id}', 'update')->name('update');
                Route::put('/{phase_id}/reorder', 'reorder')->name('reorder');
                Route::delete('/{phase_id}', 'destroy')->name('destroy');
            });

        Route::prefix('gincanas/{gymkhana_id}/juizes')
            ->name('gymkhana.judges.')
            ->controller(JudgeController::class)
            ->middleware(['admin'])
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::delete('/{judge_id}', 'destroy')->name('destroy');
            });

        Route::prefix('resultados')
            ->name('results.')
            ->controller(GymkhanaResultController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create')->middleware(['admin']);
                Route::post('/', 'store')->name('store')->middleware(['admin']);
                Route::get('/{id}/gerenciar', 'manager')->name('manager');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy')->middleware(['admin']);
            });
    });
