<?php 

namespace bezhanSalleh\LaravelCalendar\Facades;

use Illuminate\Support\Facades\Facade;

class Calendar extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'laravel-calendar';
    }
}
