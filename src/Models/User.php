<?php

namespace EscaliersSolution\LaravelAdmin\Models;

use EscaliersSolution\LaravelAdmin\Models\BaseModel;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

use EscaliersSolution\LaravelAdmin\Models\Role;

class User extends BaseModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, JWTSubject
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $labelColumn = 'username';
    protected $appends = ['active_status', 'locale', 'timezone', 'date_format', 'datetime_format'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $model->locale = setting('app.locale');
            $model->timezone = setting('app.timezone');
            $model->date_format = setting('app.dateformat');
            $model->datetime_format = setting('app.datetimeformat');

        });
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function setPasswordAttribute($value)
    {
        if($value) $this->attributes['password'] = bcrypt($value);
    }

    public function getActiveStatusAttribute($value)
    {
        return $this->active > 0 ? 'Active' : 'Inactive';
    }

    public function setPreferencesAttribute($value)
    {
        if(!(\is_string($value) && \is_json($value))) {
            if(\is_array($value)) $value = \json_encode($value);
            elseif(\is_object($value) && $value instanceof \Illuminate\Support\Collection) $value = $value->toJson();
            else $value = \json_encode([]);
        }
        $this->attributes['preferences'] = $value;
    }

    public function getPreferencesAttribute($value)
    {
        return \is_json($value) ? collect(json_decode($value)) : collect([]);
    }

    public function setLocaleAttribute($value)
    {
        $this->preferences = $this->preferences->merge(['locale' => $value]);
    }

    public function getLocaleAttribute()
    {
        return $this->preferences->get('locale');
    }

    public function setTimezoneAttribute($value)
    {
        $this->preferences = $this->preferences->merge(['timezone' => $value]);
    }

    public function getTimezoneAttribute()
    {
        return $this->preferences->get('timezone');
    }

    public function setDateFormatAttribute($value)
    {
        $this->preferences = $this->preferences->merge(['dateformat' => $value]);
    }

    public function getDateFormatAttribute()
    {
        return $this->preferences->get('dateformat');
    }

    public function setDatetimeFormatAttribute($value)
    {
        $this->preferences = $this->preferences->merge(['datetime_format' => $value]);
    }

    public function getDatetimeFormatAttribute()
    {
        return $this->preferences->get('datetime_format');
    }

    public function scopeVisible($query)
    {
        return $query->whereHas('role', function($q){
            $q->visible();
        });
    }

    public static function elements()
    {
        return [
            'id' => [],
            'username' => [
                'required' => true,
                'unique' => true,
            ],
            'password' => [
                'type' => 'password',
                'required' => true,
            ],
            'name' => [
                'required' => true,
                'unique' => true,
            ],
            'email' => [
                'type' => 'email',
                'unique' => true,
            ],
            'phone' => [],
            'role_id' => [
                'label' => 'Role',
                'type' => 'select',
                'options' => Role::visible()->pluck('name', 'id'),
                'required' => true,
                'attr' => ['v-model' => 'selectedRoleId', 'data-url' => api_admin_url('role')]
            ],
            'active' => [
                'type' => 'radio',
                'options' => ['Inactive', 'Active'],
                'required' => true,
            ],
            'locale' => [
                'required' => true,
                'type' => 'select2',
                'value' => 'en',
                'options' => array_to_options(['en'], false)
            ],
            'timezone' => [
                'required' => true,
                'type' => 'select2',
                'value' => 'UTC',
                'options' => array_to_options(timezone_identifiers_list(), false)
            ],
            'date_format' => [
                'required' => true,
                'type' => 'select2',
                'value' => 'd/m/Y',
                'options' => array_to_options(['d/m/Y', 'Y-m-d'], false)
            ],
            'datetime_format' => [
                'required' => true,
                'type' => 'select2',
                'value' => 'd/m/Y g:i A',
                'options' => array_to_options(['d/m/Y g:i A', 'Y-m-d H:i:s'], false)
            ],
            'active_status' => [
                'searchable' => false,
                'sortable' => false
            ],
            'role' => [
                'relation' => 'role.name'
            ],
        ];
    }

    public static function listable()
    {
        return ['id', 'name', 'username', 'email', 'phone', 'active_status', 'role'];
    }

    public static function editable()
    {
        return ['name', 'username', 'password', 'email', 'phone', 'active', 'role_id'];
    }

    public static function editableProfile()
    {
        return ['name', 'email', 'phone', 'password', 'locale', 'timezone', 'date_format', 'datetime_format'];
    }

    public static function getQuery()
    {
        $query = parent::getQuery();
        $query->with('role')->select('users.*')->whereHas('role', function (\Illuminate\Database\Eloquent\Builder $query) {
            $query->visible();
        });
        return $query;
    }
}
