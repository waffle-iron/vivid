<?php

namespace Database\Vivid
{
    use Database\Connection\DB;
    use Utility\Regex;

    abstract class Model implements \IteratorAggregate, \ArrayAccess, \Countable, \Serializable
    {
        protected $builder;
        protected $schema;
        protected $table;
        protected $fields;
        protected $original;

        protected static $macroMethods = [
            'Where',
            'All',
            'First',
        ];

        public function __construct()
        {
            $this->table = Regex::Encapsulate($this->table, '`');
            $this->builder = DB::Table($this->table);

            if(DB::IsCached($this->table))
            {
                $this->schema = DB::GetCached($this->table);

                foreach($this->schema as $field)
                {
                    $this->fields[$field] = null;
                    $this->original[$field] = null;
                }

                $this->builder->SetPrimaries($this->schema->GetIndex('PRIMARY'));
            }
        }
        public function __call($name, $arguments)
        {
            if (!method_exists($this, $name) || !in_array($name, static::$macroMethods))
            {
                throw new \Exception(
                    'Method ' . $name . ' does not exist'
                );
            }

            return call_user_func_array([$this, $name], $arguments);
        }
        public static function __callStatic($name, $arguments)
        {
            return (new static())->__call($name, $arguments);
        }
        public function __set($name, $value)
        {
            $this->fields[$name] = $value;
        }
        public function __get($name)
        {
            return $this->fields[$name];
        }
        public function __toString() : string
        {
            return json_encode($this->ToArray());
        }

        public function ToArray() : array
        {
            return $this->fields;
        }

        public function Fill(array $row)
        {
            $this->fields = $row;
            $this->original = $row;
        }
        public function Save()
        {
            $fields = join('', $this->fields);
            $original = join('', $this->original);

            if($fields == $original)
            {
                return false;
            }

            if($original === '' && $fields !== '')
            {
                $result = $this->builder->Insert($this->fields);

                if($this->schema !== null)
                {
                    var_dump($result);
                    var_dump($this->schema->GetIndex('PRIMARY'));
                    die;
                }

                return $result['rowCount'] > 0;
            }

            $values = [];
            foreach($this->fields as $column => $field)
            {
                if($this->original[$column] != $field)
                {
                    $values[] = $column;
                }
            }

            return $this->builder->Update($this->fields, $values)['rowCount'] > 0;
        }

        protected function All($columns = ['*']) : Collection
        {
            $data = $this->builder->Select(is_array($columns)
                ? $columns
                : func_get_args()
            )->All();

            return new Collection(
                static::class,
                $data
            );
        }
        protected function First($columns = ['*']) : Model
        {
            $this->Fill(
                $this->builder->Select(is_array($columns)
                    ? $columns
                    : func_get_args()
                )->First()
            );

            return $this;
        }
        protected function Where(string $condition) : Model
        {
            if(strlen($condition) == 0)
            {
                throw new \Exception(
                    'No condition given'
                );
            }

            $this->builder->Where($condition);

            return $this;
        }

        public function getIterator()
        {
            foreach($this->fields as $column => $field)
            {
                yield $column => $field;
            }
        }

        public function offsetExists($offset)
        {
            return key_exists($offset, $this->fields);
        }
        public function offsetGet($offset)
        {
            return $this->fields[$offset];
        }
        public function offsetSet($offset, $value)
        {
            $this->fields[$offset] = $value;
        }
        public function offsetUnset($offset)
        {
            unset($this->fields[$offset]);
        }

        public function Count()
        {
            return count($this->fields);
        }

        public function serialize()
        {
            return $this->ToArray();
        }
        public function unserialize($serialized)
        {
            $this->__construct();
            $this->Fill($serialized);
        }
    }
}
