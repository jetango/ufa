<?php

if (!function_exists('ufa')) {

    /**
     * Get ufa Instance
     * @param null $interface
     * @return \Angejia\Ufa\Ufa
     */
    function ufa($interface = null)
    {
        if (isset($interface)) {
            return app('UfaService')->get($interface);
        }

        return app('UfaService');
    }
}
