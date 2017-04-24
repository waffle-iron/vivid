<?php

namespace Database\Schema
{
    use Utility\Regex;

    class Column implements \JsonSerializable
    {
        protected $name;
        protected $type;
        protected $nullable;

        public function __construct(string $name, IType $type, bool $nullable = false)
        {
            $this->name = $name;
            $this->type = $type;
            $this->nullable = $nullable;
        }
        public function __toString() : string
        {
            return join(' ', [
                Regex::Encapsulate($this->name, '`'),
                $this->type,
                ($this->nullable ? '' : 'NOT ') . 'NULL'
            ]);
        }

        public function GetName() : string
        {
            return $this->name;
        }

        function jsonSerialize() : array
        {
            return [
                $this->name,
                $this->type,
                $this->nullable,
            ];
        }
    }
}
