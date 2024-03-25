<?php

namespace App\Traits;

trait Uuid
{
    public static function getByUUID($uuid)
    {   
        if ($uuid == '')
            return null;

        return self::where('uuid', $uuid)->first();
    }
    
    public static function getIdByUUID($uuid)
    {
        $result = self::where('uuid', $uuid)->first();
        $id = null;
        if ($result) 
            $id = $result->id;

        return $id;
    }

}