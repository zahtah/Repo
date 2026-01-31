<?php

namespace App\Policies;

use App\Models\Allocation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AllocationPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view allocation');
    }

    public function create(User $user)
    {
        return $user->can('create allocation');
    }

    public function update(User $user, Allocation $allocation)
    {
        return $user->can('edit allocation');
    }

    public function delete(User $user, Allocation $allocation)
    {
        return $user->can('delete allocation');
    }

    public function approve(User $user, Allocation $allocation)
    {
        return $user->can('approve allocation')
            && $allocation->status !== 'approved';
    }
}

