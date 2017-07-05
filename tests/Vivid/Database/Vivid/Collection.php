<?php

namespace Vivid\Database\Vivid
{
    class Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        protected $models = [];

        public function __construct(string $class, array $data = [])
        {
            foreach($data as $row)
            {
                $model = new $class();
                $model->Fill($row);

                $this->models[] = $model;
            }
        }

        public function getIterator()
        {
            foreach ($this->models as $model)
            {
                yield $model;
            }
        }

        public function ToArray() : array
        {
            $models = [];

            foreach($this->models as $model)
            {
                $models[] = $model->ToArray();
            }

            return $models;
        }

        public function offsetExists($offset)
        {
            return key_exists($offset, $this->models);
        }
        public function offsetGet($offset)
        {
            return $this->models[$offset];
        }
        public function offsetSet($offset, $value)
        {
            $this->models[$offset] = $value;
        }
        public function offsetUnset($offset)
        {
            unset($this->models[$offset]);
        }

        public function Count()
        {
            return count($this->fields);
        }
    }
}
