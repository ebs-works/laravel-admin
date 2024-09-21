<?php

namespace EscaliersSolution\LaravelAdmin\Policies;

use EscaliersSolution\LaravelAdmin\Models\User;
use EscaliersSolution\LaravelAdmin\Models\MenuItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuItemPolicy extends CrudPolicy
{
    public function reOrder(User $user)
    {
        return is_policy_authorized($this, __FUNCTION__, $user->role_id);
    }
}