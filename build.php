#!/usr/bin/env php
<?php

function ask( string $question, string $default = '' ): string
{
    $answer = readline($question . ( $default ? " ({$default})" : null ) . ': ');
    
    if (! $answer) {
        return $default;
    }
    
    return $answer;
}

function confirm( string $question, bool $default = false ): bool
{
    $answer = ask($question . ' (' . ( $default ? 'Y/n' : 'y/N' ) . ')');
    
    if (! $answer) {
        return $default;
    }
    
    return strtolower($answer) === 'y';
}

function writeln( string $line ): void
{
    echo $line . PHP_EOL;
}

/*
 |--------------------------------------------------------------------------
 | Initialize Project
 |--------------------------------------------------------------------------
 | - This script will perform the following steps:
 |
 | @ Collect Input.
 |
 | * User provide the value with interactive prompt.
 | * Perform replace all placeholder with input value.
 | * Create the necessary files and directories.
 | * Set the correct permission for the files and directories.
 |
 | @ Cleaning Up.
 | * Confirmation prompt to user whether to self-destruct this file.
 | * If user confirm, self-destruct this file.
 |
 | @ Ended.
 */

writeln('The project has been initialized.');
confirm('Do you want this file to self-destruct?', true) && unlink(__FILE__);
