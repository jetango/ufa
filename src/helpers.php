<?php

if (!function_exists('ufa')) {
    /**
     * Get the implementation of a repository or just the repository container.
     *
     * @param string|null $interface Repository interface name.
     * @return \Angejia\Foundation\Domain\Interfaces\Repository|\Angejia\Foundation\Domain\RepositoryContainer
     */
    function ufa($interface = null)
    {
        if (isset($interface)) {
            return app('UfaService')->get($interface);
        }

        return app('UfaService');
    }
}
