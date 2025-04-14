<?php

namespace App\Policies;

use App\Models\User;
use App\Domain\Organization\Models\Position;
use Illuminate\Auth\Access\HandlesAuthorization;

class PositionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        // ผู้ใช้ทุกคนที่ล็อกอินสามารถดูรายการตำแหน่งได้
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Domain\Organization\Models\Position  $position
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Position $position)
    {
        // ผู้ใช้ทุกคนที่ล็อกอินสามารถดูรายละเอียดตำแหน่งได้
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // ผู้ใช้ที่มีสิทธิ์ 'manage-positions' หรือ 'manage-all-positions' เท่านั้นที่สามารถสร้างตำแหน่งใหม่
        return $user->can('manage-positions') || $user->can('manage-all-positions');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Domain\Organization\Models\Position  $position
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Position $position)
    {
        // ผู้ใช้ที่มีสิทธิ์ 'manage-positions' หรือ 'manage-all-positions' เท่านั้นที่สามารถแก้ไขตำแหน่ง
        return $user->can('manage-positions') || $user->can('manage-all-positions');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Domain\Organization\Models\Position  $position
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Position $position)
    {
        // ผู้ใช้ที่มีสิทธิ์ 'manage-positions' หรือ 'manage-all-positions' เท่านั้นที่สามารถลบตำแหน่ง
        // ตำแหน่งที่มีพนักงานอยู่จะไม่สามารถลบได้
        if ($position->employees()->count() > 0) {
            return false;
        }

        return $user->can('manage-positions') || $user->can('manage-all-positions');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Position $position): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Position $position): bool
    {
        return false;
    }
}
