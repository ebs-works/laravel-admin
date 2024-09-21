<?php

namespace EscaliersSolution\LaravelAdmin\Policies;

use EscaliersSolution\LaravelAdmin\Models\User;
use EscaliersSolution\LaravelAdmin\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy extends CrudPolicy
{
    public function setPermissions(User $user)
    {
        return is_policy_authorized($this, __FUNCTION__, $user->role_id);
    }
}
