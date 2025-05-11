<?php

namespace App\Policies;

use App\Models\DeliveryOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeliveryOrderPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, DeliveryOrder $deliveryOrder)
    {
        // ให้ผู้ใช้เข้าถึงเฉพาะใบส่งสินค้าของบริษัทของตัวเอง
        return $user->currentCompany()->id === $deliveryOrder->company_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, DeliveryOrder $deliveryOrder)
    {
        return $user->currentCompany()->id === $deliveryOrder->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, DeliveryOrder $deliveryOrder)
    {
        return $user->currentCompany()->id === $deliveryOrder->company_id && 
               in_array($deliveryOrder->delivery_status, ['pending', 'cancelled']);
    }
}
