<?php

namespace Vivid\Database\Schema\Type
{
    class Char extends AbstractType
    {
        public function __toString() : string
        {
            return 'CHAR';
        }

        public static function AsString() : string
        {
            return (string)new static();
        }
    }
}
