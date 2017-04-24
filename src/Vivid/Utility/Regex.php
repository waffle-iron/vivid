<?php

namespace Vivid\Utility
{
    class Regex
    {
        public static function Encapsulate(string $input, string $char, string $filter = null, string $lookFor = 'A-Za-z0-9_', int $limit = -1) : string
        {
            $filter = $filter ?? $char;

            return preg_replace(
                '/(?![^' . $filter . ']*[' . $filter . '](?:[^' . $filter . ']*[' . $filter . '][^' . $filter . ']*[' . $filter . '])*[^' . $filter . ']*$)([' . $lookFor . ']+)/',
                $char . '$1' . $char,
                $input,
                $limit
            );
        }
    }
}
