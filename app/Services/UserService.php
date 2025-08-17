<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getJudges()
    {
        return User::where('type', User::TYPE_JUDGE)
            ->get();
    }

    public function find(int $id): User
    {
        return User::findOrFail($id);
    }

    public function createUser(array $data): User
    {
        $data['type'] = User::TYPE_JUDGE;

        return User::create($data);
    }

    public function updateUser(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        if (!isset($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return $user;
    }

    public function delete(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
