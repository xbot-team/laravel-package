<?php

arch('src classes')
    ->expect('XBot\Package')
    ->toBeClasses()
    ->toOnlyBeUsedIn('XBot\Package')
    ->ignoring('XBot\Package\PackageServiceProvider')
    ->ignoring('XBot\Package\Console\Commands\PackageCommand');

arch('facades')
    ->expect('XBot\Package\Facades')
    ->toBeClasses()
    ->toExtend('Illuminate\Support\Facades\Facade')
    ->toOnlyBeUsedIn('XBot\Package');

arch('commands')
    ->expect('XBot\Package\Console\Commands')
    ->toBeClasses()
    ->toExtend('Illuminate\Console\Command');

arch('strict types')
    ->expect('XBot\Package')
    ->toUseStrictTypes();

arch('no forbidden words')
    ->expect('XBot\Package')
    ->not->toUse(['die', 'var_dump', 'exit', 'dd', 'eval']);
