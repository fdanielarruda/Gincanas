<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Services\UserService;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        protected UserService $service
    ) {}

    public function index()
    {
        $users = $this->service->getJudges();

        return Inertia::render('Users/Index', ['users' => $users]);
    }

    public function create()
    {
        return Inertia::render('Users/CreateUser');
    }

    public function store(StoreUserRequest $request)
    {
        $this->service->createUser($request->validated());

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(int $id)
    {
        $user = $this->service->find($id);

        return Inertia::render('Users/EditUser', [
            'user' => $user
        ]);
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $this->service->updateUser($id, $request->validated());

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return redirect()->route('users.index')
            ->with('success', 'Usuário deletado com sucesso!');
    }
}
