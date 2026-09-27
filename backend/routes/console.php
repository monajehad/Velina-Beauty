<?php

use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Closure based console commands
| for your application. You can also register commands defined in the
| app/Console/Commands directory.
|
*/

Artisan::command('inspire', function () {
    $this->comment('The only way to do great work is to love what you do.');
})->purpose('Display an inspiring quote');