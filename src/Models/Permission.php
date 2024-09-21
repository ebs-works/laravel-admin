<?php

namespace EscaliersSolution\LaravelAdmin\Models;

use EscaliersSolution\LaravelAdmin\Models\BaseModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends BaseModel
{
    use HasFactory;

    protected $labelColumn = 'name';

    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class);
    }
}
