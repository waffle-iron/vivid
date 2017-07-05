<?php

namespace Vivid\Database\Schema\Type
{
    use Vivid\Database\Schema\IType;

    abstract class AbstractType implements IType, \JsonSerializable
    {
        protected $variables = [];

        private static function UglyDynamicBinding() : array
        {
            $bindings = [];

            foreach(get_declared_classes() as $class)
            {
                if(is_subclass_of($class, __CLASS__))
                {
                    $bindings[preg_replace('/([A-Za-z]+)(.*)/', '$1', $class::AsString())] = $class;
                }
            }

            return $bindings;
        }

        public static function FromString(string $data)
        {
            $type = strtoupper(preg_replace('/([A-Za-z]+)(.*)/', '$1', $data));
            $bindings = static::UglyDynamicBinding();

            preg_match('/\((.*)\)/', $data, $match);

            return new $bindings[$type]($match[1]);
        }

        public function __get($name)
        {
            return $this->variables[$name];
        }
        public function __set($name, $value)
        {
            $this->variables[$name] = $value;
        }

        function jsonSerialize()
        {
            return [
                static::class,
                array_values($this->variables),
            ];
        }
    }
}
