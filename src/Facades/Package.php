<?php

declare(strict_types=1);

namespace XBot\Package\Facades;

use Illuminate\Support\Facades\Facade;
use XBot\Package\Package as PackageService;

class Package extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PackageService::class;
    }
}
