<?php

namespace Database\Schema\Type
{
    class Text extends AbstractType
    {
        public function __toString() : string
        {
            return 'TEXT';
        }

        public static function AsString() : string
        {
            return (string)new static(0);
        }
    }
}
