<?php

if (! function_exists('imagePath')) {
    function imagePath($path) {

        return url('/').$path;
    }
}


if (! function_exists('langUrl')) {
    function langUrl($name) {
        return route($name, app()->getLocale());
    }
}


if (! function_exists('TwelveFormat')) {
    function TwelveFormat($value,$step) {

        $time = strtotime($value);
        $endTime = date("h:i A", strtotime("+$step minutes", $time));


        return  date('h:i A',strtotime($value)).' '.$endTime;
    }
}




/**
 * Shortcut for accessing the config theme.
 *
 * @param  string  $file
 * @return string
 */
function theme($file = null)
{


    // Laravel perfers dot notation for view file names, especially in test
    $dot_file = str_replace('/', '.', $file);
    $dot_file = preg_replace('/^\./', '', $dot_file);

    // To check the file's existance, we need '/' in files however
    $file_name = str_replace('.', '/', $file);
    $file_name = preg_replace('/^\//', '', $file_name);

    $theme_file = resource_path('views/' .
        "/{$file_name}.blade.php"
    );

    return file_exists($theme_file) ?"$dot_file":"$dot_file";
}



?>
