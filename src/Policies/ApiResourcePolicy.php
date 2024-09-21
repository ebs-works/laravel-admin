<?php

namespace EscaliersSolution\LaravelAdmin\Policies;

use EscaliersSolution\LaravelAdmin\Models\User;
use EscaliersSolution\LaravelAdmin\Models\ApiResource;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApiResourcePolicy extends CrudPolicy
{
    public function test(User $user)
    {
        return is_policy_authorized($this, __FUNCTION__, $user->role_id);
    }
}
