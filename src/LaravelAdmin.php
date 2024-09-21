<?php

namespace EscaliersSolution\LaravelAdmin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;

use EscaliersSolution\LaravelAdmin\Models\ApiResource;
use EscaliersSolution\LaravelAdmin\Models\MenuItem;
use EscaliersSolution\LaravelAdmin\Models\Permission;
use EscaliersSolution\LaravelAdmin\Models\Role;
use EscaliersSolution\LaravelAdmin\Models\Setting;
use EscaliersSolution\LaravelAdmin\Models\User;

class LaravelAdmin
{
    protected $version;
    protected $filesystem;

    protected $models = [
        'ApiResource'   => ApiResource::class,
        'MenuItem'      => MenuItem::class,
        'Permission'    => Permission::class,
        'Role'          => Role::class,
        'Setting'       => Setting::class,
        'User'          => User::class,
    ];

    public function __construct()
    {
        $this->filesystem = app(Filesystem::class);

        $this->findVersion();
    }

    public function model($name)
    {
        return app($this->models[str($name)->studly()->toString()]);
    }

    public function modelClass($name)
    {
        return $this->models[$name];
    }

    public function webRoutes()
    {
        require __DIR__.'/routes/web.php';
    }

    public function apiRoutes()
    {
        require __DIR__.'/routes/api.php';
    }

    public function getVersion()
    {
        return $this->version;
    }

    protected function findVersion()
    {
        if (!is_null($this->version)) {
            return;
        }

        if ($this->filesystem->exists(base_path('composer.lock'))) {
            // Get the composer.lock file
            $file = json_decode(
                $this->filesystem->get(base_path('composer.lock'))
            );

            // Loop through all the packages and get the version of voyager
            foreach ($file->packages as $package) {
                if ($package->name == 'ebs-works/laravel-admin') {
                    $this->version = $package->version;
                    break;
                }
            }
        }
    }
}
