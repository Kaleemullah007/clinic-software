<?php

use App\Models\Module;
use Illuminate\Support\Facades\Cache;

if (! function_exists('userModules')) {
    function userModules()
    {
        return Cache::remember('user'.auth()->id(),'100',function(){
            return Module::get();
        });
    }
}

if (! function_exists('refreshModule')) {
    function refreshModule()
    {
         Cache::forget('user'.auth()->id());
        return Cache::remember('user'.auth()->id(),'100',function(){
            return Module::get();
        });
    }
}

if (! function_exists('userRights')) {
    function userRights()
    {
         return Cache::get('ur'.auth()->id());

    }
}

if (! function_exists('appointmentStaus')) {
    function appointmentStaus($id)
    {
        $status[5]= 'Approved';
        $status[1]= 'Pending';
        $status[2]= 'Accepted';
        $status[3]= 'Rejected';
        $status[4]= 'Completed';
         return $status[$id]??'';

    }
}

?>
