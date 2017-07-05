<?php

namespace Vivid\Database\Schema
{
    interface IType
    {
        public function __toString() : string;

        public static function AsString() : string;
    }
}
