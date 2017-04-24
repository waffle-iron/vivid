<?php

namespace Vivid\Database\Schema\Type
{
    use Vivid\Database\Schema\IType;

    class Datetime extends AbstractType
    {
        public function __toString() : string
        {
            return 'DATETIME';
        }

        public static function AsString() : string
        {
            return (string)new static(0);
        }
    }
}
