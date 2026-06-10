<?php

declare(strict_types=1);

namespace XBot\Package;

class Package
{
    /**
     * The package configuration.
     */
    protected array $config;

    /**
     * Create a new Package instance.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get the package configuration.
     */
    public function config(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        return data_get($this->config, $key, $default);
    }

    /**
     * Return a greeting message.
     */
    public function hello(): string
    {
        $name = $this->config('name', 'World');

        return "Hello, {$name}!";
    }
}
