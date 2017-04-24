<?php

namespace Database\Query
{
    use Database\Connection\DB;
    use Utility\Regex;

    class Builder
    {
        const SELECT = 'SELECT';
        const UPDATE = 'UPDATE';
        const DELETE = 'DELETE';
        const INSERT = 'INSERT';

        private $table;
        private $wheres;
        private $columns;
        private $values;
        private $primaries;
        private $limit;

        private static $cache = [];

        public function __construct(string $table)
        {
            $this->table = Regex::Encapsulate($table, '`');
            $this->wheres = [];
            $this->columns = ['*'];
            $this->values = [];
            $this->primaries = [];
        }

        public function SetPrimaries(array $primaries)
        {
            $this->primaries = $primaries;
        }

        public function ToSql(string $method) : string
        {
            $buildHash = hash('md5', join('', [
                $method,
                $this->table,
                join('', $this->wheres),
                join('', $this->columns),
                join('', $this->values),
                $this->limit,
            ]));

            if(key_exists($buildHash, static::$cache))
            {
                return static::$cache[$buildHash];
            }

            switch($method) // TODO - extract the build functions to external classes
            {
                case Builder::SELECT:
                    $sql = $this->BuildSelect(...array_slice(func_get_args(), 1));
                    break;
                case Builder::UPDATE:
                    $sql = $this->BuildUpdate(...array_slice(func_get_args(), 1));
                    break;
                case Builder::DELETE:
                    $sql = $this->BuildDelete(...array_slice(func_get_args(), 1));
                    break;
                case Builder::INSERT:
                    $sql = $this->BuildInsert(...array_slice(func_get_args(), 1));
                    break;
                default:
                    throw new \Exception(
                        'invalid method provided'
                    );
            }

            static::$cache[$buildHash] = $sql;

            return $sql;
        }

        public function Select($columns = ['*']) : Builder
        {
            $this->columns = is_array($columns)
                ? $columns
                : func_get_args();

            return $this;
        }
        public function From(string $table) : Builder
        {
            $this->table = Regex::Encapsulate($table, '`');

            return $this;
        }
        public function Where(string $condition) : Builder
        {
            $this->wheres[] = $condition;

            return $this;
        }

        public function All() : array
        {
            return DB::Select($this);
        }
        public function First() : array
        {
            $this->limit = 1;

            return DB::Select($this)[0];
        }

        public function Update(array $values, array $selection, bool $mass = false) : array
        {
            $this->columns = array_keys($values);
            $this->values = array_values($values);

            return DB::Update($this, $selection);
        }
        public function Insert(array $values, bool $mass = false) : array
        {
            $this->columns = array_keys($values);
            $this->values = array_values($values);

            return DB::Insert($this);
        }

        private function BuildSelect() : string
        {
            $columns = implode(', ', array_map(function ($column){
                return preg_replace('/([^*]+)/', '`$1`', $column);
            }, $this->columns));

            $sql = 'SELECT ' . $columns . ' FROM ' . $this->table;
            $sql .= $this->CompileWheres();

            if($this->limit !== null)
            {
                $sql .= ' LIMIT ' . $this->limit;
            }

            return $sql;
        }
        private function BuildUpdate(array $columns) : string
        {
            $values = [];

            foreach($columns as $column)
            {
                $value = $this->values[array_search($column, $this->columns)];
                $values[] = Regex::Encapsulate($column, '`') . '=' . (is_string($value) ? '"' . $value . '"' : $value);
            }

            $wheres = [];
            foreach($this->primaries as $primary)
            {
                $value = $this->values[array_search($primary, $this->columns)];
                $wheres[] = Regex::Encapsulate($primary, '`') . '=' . (is_string($value) ? '"' . $value . '"' : $value);
            }

            return 'UPDATE ' . $this->table . 'SET ' . join(',', $values) . (count($wheres) > 0 ? (' WHERE ' . join(' AND ', $wheres)) : '') . ';';
        }
        private function BuildDelete() : string
        {
            return '';
        }
        private function BuildInsert() : string
        {
            return 'INSERT INTO ' . $this->table . '(`' . join('`, `', $this->columns) . '`) VALUES ("' . join('", "', $this->values) . '");';
        }

        private function CompileWheres() : string
        {
            $parsed = [];

            foreach($this->wheres as $where)
            {
                $where = Regex::Encapsulate($where, '`', '`"', 'A-Za-z_', 1);
                $where = preg_replace(
                    [
                        '/&&/',
                        '/\|\|/',
                    ],
                    [
                        'AND',
                        'OR',
                    ],
                    $where
                );

                $parsed[] = $where;
            }

            return count($parsed) > 0
                ? ' WHERE ' . implode(' AND ', $parsed)
                : '';
        }
    }
}
