<?php

namespace EscaliersSolution\LaravelAdmin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \EscaliersSolution\LaravelAdmin\Skeleton\SkeletonClass
 */
class LaravelAdmin extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-admin';
    }
}
