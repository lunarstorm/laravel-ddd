<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

it('can initialize composer.json', function () {
    $data = json_decode(file_get_contents(base_path('composer.json')), true);
    $before = data_get($data, ['autoload', 'psr-4', 'Domains\\']);
    expect($before)->toBeNull();

    Artisan::call("ddd:install");

    $data = json_decode(file_get_contents(base_path('composer.json')), true);
    $after = data_get($data, ['autoload', 'psr-4', 'Domains\\']);
    expect($after)->toEqual(config('ddd.paths.domains'));
});
