<?php

namespace Vivid\Database\Schema\Type
{
    class Enum extends AbstractType
    {
        public function __construct($values)
        {
            $this->values = is_string($values)
                ? explode(',', $values)
                : $values;
        }

        public function __toString() : string
        {
            return 'ENUM("' . implode('", "', $this->values) . '")';
        }

        public static function AsString() : string
        {
            return (string)new static('');
        }
    }
}
