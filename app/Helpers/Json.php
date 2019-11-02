<?php


namespace App\Helpers;


class Json
{
    // data -> json
    public function dump($data = null, $onlyInDebugMode = true)
    {
        $show = ($onlyInDebugMode === true && env('APP_DEBUG') === false) ? false : true;
        if (array_key_exists('json', app('request')->query()) && $show) {
            header('Content-Type: application/json');
            die(json_encode($data));
        }
    }
}
