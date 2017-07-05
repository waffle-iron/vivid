<?php

namespace Vivid\Database\Schema\Type
{
    class Timestamp extends AbstractType
    {
        public function __toString() : string
        {
            return 'TIMESTAMP';
        }

        public static function AsString() : string
        {
            return (string)new static();
        }
    }
}
