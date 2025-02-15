<?php

namespace EscaliersSolution\LaravelAdmin\Models;

use EscaliersSolution\LaravelAdmin\Models\BaseModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuItem extends BaseModel
{

    protected $appends = ['url', 'has_children', 'active'];

    public function permission()
    {
        return $this->hasOne(\App\Models\Permission::class, 'id', 'permission_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function getUrlAttribute($value)
    {
        return $this->path ? (filter_var($this->path, FILTER_VALIDATE_URL) ? $this->path : admin_url($this->path)) : '#';
    }

    public function getHasChildrenAttribute($value)
    {
        return count($this->children) > 0;
    }

    public function getActiveAttribute($value)
    {
        if($this->has_children) {
            $subMenuActive = false;
            foreach ($this->children as $item) {
                if($item->active) {
                    $subMenuActive = true;
                    break;
                }
            }
            return ($subMenuActive);
        }
        else {
            return ($this->path === str(\request()->path())->after(config('laravel-admin.admin_prefix'))->after('/')->before('/')->toString());
        }
        
    }

    public function scopeVisible($query)
    {
        $query->where('display', '1');
        return $query;
    }

    // The main filter query used to display menu
    public function scopeHierarchy($query)
    {
        // get all visible items
        $query->visible();

        // get in set order
        $query->orderBy('order');

        // attach children which are visible, permitted and in set order
        $query->with(['children' => function($q0) {
            $q0->orderBy('order');
            if (auth_user() && !auth_user()->role->unrestricted) $q0->whereIn('permission_id', auth_user()->role->permissions->pluck('id'))->orwhere('permission_id', null);
        }]);

        // get root level items without children which are permitted
        $query->where(function ($q1) {
            $q1->whereDoesntHave('parent');
            $q1->whereDoesntHave('children');
            if (auth_user() && !auth_user()->role->unrestricted) $q1->whereIn('permission_id', auth_user()->role->permissions->pluck('id'));
        });

        // or get root level items which has children which are permitted
        $query->orWhere(function ($q2) {
            $q2->whereDoesntHave('parent');
            $q2->whereHas('children', function ($q21) {
                if (auth_user() && !auth_user()->role->unrestricted) $q21->whereIn('permission_id', auth_user()->role->permissions->pluck('id'))->orwhere('permission_id', null);
            });
        });

        return $query;
    }

    public static function reorder($data)
    {
        foreach ($data as $item) {
            $menuItem = self::findOrFail($item['id']);
            $menuItem->parent_id = $item['parent_id'] ?? null;
            $menuItem->order = $item['order'];
            $item = $menuItem->save();
        }
    }

    public static function createNew($data)
    {
        $menuItem = new MenuItem();
        $menuItem->parent_id = null;
        $menuItem->text = $data['text'] ?? "Menu Item";
        $menuItem->path = $data['path'] ?? null;
        $menuItem->icon_class = $data['icon_class'] ?? 'fas fa-circle';
        $menuItem->target = $data['target'];
        $menuItem->permission_id = $data['permission_id'] ?? null;
        $menuItem->order = 0;
        $menuItem->display = $data['display'] ?? 1;
        $menuItem->save();
    }

    public static function updateAt($id, $data)
    {
        $menuItem = self::findOrFail($id);
        $menuItem->text = $data['text'] ?? "Menu Item #{$id}";
        $menuItem->path = $data['path'] ?? null;
        $menuItem->icon_class = $data['icon_class'] ?? 'fas fa-circle';
        $menuItem->target = $data['target'];
        $menuItem->permission_id = $data['permission_id'] ?? null;
        $menuItem->display = $data['display'] ?? 1;
        $menuItem->save();
    }

    public static function deleteAt($id)
    {
        $menuItem = self::findOrFail($id);
        self::unOrphan($id);
        $menuItem->delete();
    }

    private static function unOrphan($id)
    {
        self::where('parent_id', $id)->update(['parent_id' => null]);
    }
}
