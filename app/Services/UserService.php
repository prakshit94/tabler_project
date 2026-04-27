<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Get paginated users.
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()->with(['roles', 'permissions']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('mobile', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new user.
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        if (!empty($data['roles'])) {
            $user->assignRole($data['roles']);
        }

        return $user;
    }

    /**
     * Update an existing user.
     */
    public function updateUser(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user;
    }

    /**
     * Delete user (soft delete).
     */
    public function deleteUser(User $user): void
    {
        $user->delete();
    }

    /**
     * Change user status.
     */
    public function changeStatus(User $user, string $status): void
    {
        $user->update(['status' => $status]);
    }

    /**
     * Assign role to user.
     */
    public function assignRole(User $user, string $role): void
    {
        $user->assignRole($role);
    }

    /**
     * Remove role from user.
     */
    public function removeRole(User $user, string $role): void
    {
        $user->removeRole($role);
    }

    /**
     * Assign direct permission to user.
     */
    public function assignPermission(User $user, string $permission): void
    {
        $user->givePermissionTo($permission);
    }

    /**
     * Remove direct permission from user.
     */
    public function removePermission(User $user, string $permission): void
    {
        $user->revokePermissionTo($permission);
    }
}
