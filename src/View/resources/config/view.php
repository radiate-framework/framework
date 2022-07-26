<?php

use Radiate\Support\Facades\App;

return [
    /*
    |--------------------------------------------------------------------------
    | View Storage Path
    |--------------------------------------------------------------------------
    |
    | Here you may specify a path that should be checked for your views.
    |
    */

    'path' => App::basePath('resources/views'),

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the wp-content
    | directory. However, as usual, you are free to change this value.
    |
    */
    'compiled' => WP_CONTENT_DIR . '/framework/views',
];
