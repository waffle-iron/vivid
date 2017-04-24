<?php

namespace Database\Schema\Type
{
    class Integer extends AbstractType
    {
        public function __construct(int $length)
        {
            $this->length = $length;
        }

        public function __toString() : string
        {
            return 'INT(' . $this->length . ')';
        }

        public static function AsString() : string
        {
            return (string)new static(0);
        }
    }
}
