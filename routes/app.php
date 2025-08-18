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

        Route::prefix('gincanas/{gymkhana_id}/fases')
            ->name('gymkhana.phases.')
            ->controller(PhaseController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{phase_id}/editar', 'edit')->name('edit');
                Route::put('/{phase_id}', 'update')->name('update');
                Route::delete('/{phase_id}', 'destroy')->name('destroy');
            });

        Route::prefix('gincanas/{gymkhana_id}/juizes')
            ->name('gymkhana.judges.')
            ->controller(JudgeController::class)
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
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}/gerenciar', 'manager')->name('manager');
                Route::put('/{id}', 'update')->name('update');
                Route::get('/{id}/ranking', 'ranking')->name('ranking');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });
    });
