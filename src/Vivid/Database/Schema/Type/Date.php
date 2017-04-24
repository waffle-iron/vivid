<?php

namespace Vivid\Database\Schema\Type
{
    class Date extends AbstractType
    {
        public function __toString() : string
        {
            return 'DATE';
        }

        public static function AsString() : string
        {
            return (string)new static(0);
        }
    }
}
