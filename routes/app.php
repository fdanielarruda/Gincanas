<?php

use App\Http\Controllers\GymkhanaController;
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

                Route::get('/{id}/equipes', 'teams')->name('teams');
                Route::get('/{id}/equipes/cadastrar', 'createTeam')->name('teams.create');
                Route::post('/{id}/equipes', 'storeTeam')->name('teams.store');
                Route::get('/{id}/equipes/{team_id}/editar', 'editTeam')->name('teams.edit');
                Route::put('/{id}/equipes/{team_id}', 'updateTeam')->name('teams.update');
                Route::delete('/{id}/equipes/{team_id}', 'destroyTeam')->name('teams.destroy');
            });
    });
