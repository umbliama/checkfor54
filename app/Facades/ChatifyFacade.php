<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ChatifyFacade extends Facade 
{
    protected static function getFacadeAccessor() 
    { 
        return 'CustomChatifyMessenger'; 
    }
}
