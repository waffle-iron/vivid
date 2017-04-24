<?php

namespace Vivid\Database\Schema
{
    use Vivid\Database\Connection\DB;
    use Vivid\Utility\Regex;

    class Table implements \JsonSerializable, \IteratorAggregate
    {
        private $table;
        private $columns;
        private $indexes;

        public function __construct(string $table)
        {
            $this->table = Regex::Encapsulate($table, '`');
            $this->indexes = [];
        }
        public function __toString() : string
        {
            return 'CREATE TABLE ' . $this->table . '(' . join(', ', $this->columns) . ') ENGINE = InnoDB;';
        }
        public function __get(string $name) : Column
        {
            return $this->columns[$name];
        }
        public function __set($name, IType $value)
        {
            $this->columns[$name] = new Column($name, $value);
        }

        public function GetName() : string
        {
            return $this->table;
        }

        public function GetIndex(string $index) : array
        {
            return key_exists($index, $this->indexes)
                ? $this->indexes[$index]
                : [];
        }
        public function SetIndex(string $name, array $columns)
        {
            $this->indexes[$name] = $columns;
        }

        public function Columns(array $columns) : Table
        {
            $this->columns = array_merge($this->columns, $columns);

            return $this;
        }
        public function Create()
        {
            DB::Query($this);
        }

        public function ToJson() : string
        {
            return json_encode($this);
        }
        public static function FromJson(string $json)
        {
            $data = json_decode($json, true);

            if(is_numeric(array_keys($data)[0]))
            {
                $tables = [];

                foreach($data as $table)
                {
                    $tables[] = Table::FromJson(json_encode($table));
                }

                return $tables;
            }

            $inst = new static(array_keys($data)[0]);

            foreach($data[array_keys($data)[0]][0] as list($name, $type, $nullable))
            {
                $inst->$name = new $type[0](...$type[1]);
            }

            foreach($data[array_keys($data)[0]][1] as $index => $columns)
            {
                $inst->SetIndex($index, $columns);
            }

            return $inst;
        }

        function jsonSerialize() : array
        {
            return [
                $this->table => [
                    array_values($this->columns),
                    $this->indexes,
                ],
            ];
        }

        public function getIterator()
        {
            foreach($this->columns as $column)
            {
                yield $column->GetName();
            }
        }
    }
}
