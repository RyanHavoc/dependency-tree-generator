<?php

namespace App\Narum\Facades;

use Illuminate\Support\Facades\Facade;

class Narum extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'narum';
    }
}
