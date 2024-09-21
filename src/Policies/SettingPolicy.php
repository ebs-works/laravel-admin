<?php

namespace EscaliersSolution\LaravelAdmin\Policies;

use EscaliersSolution\LaravelAdmin\Models\User;
use EscaliersSolution\LaravelAdmin\Models\Setting;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy extends CrudPolicy
{
    public function setValue(User $user)
    {
        return is_policy_authorized($this, __FUNCTION__, $user->role_id);
    }
}