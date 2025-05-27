<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MyCar;
use Illuminate\Auth\Access\HandlesAuthorization;

class MyCarPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_my::car');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MyCar $myCar): bool
    {
        return $user->can('view_my::car');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_my::car');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MyCar $myCar): bool
    {
        return $user->can('update_my::car');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MyCar $myCar): bool
    {
        return $user->can('delete_my::car');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_my::car');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, MyCar $myCar): bool
    {
        return $user->can('force_delete_my::car');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_my::car');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, MyCar $myCar): bool
    {
        return $user->can('restore_my::car');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_my::car');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, MyCar $myCar): bool
    {
        return $user->can('replicate_my::car');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_my::car');
    }
}
