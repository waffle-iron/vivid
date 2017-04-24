<?php

namespace Vivid\Database\Schema\Type
{
    class TinyInteger extends AbstractType
    {
        public function __construct(int $length)
        {
            $this->length = $length;
        }

        public function __toString() : string
        {
            return 'TINYINT(' . $this->length . ')';
        }

        public static function AsString() : string
        {
            return (string)new static(0);
        }
    }
}
