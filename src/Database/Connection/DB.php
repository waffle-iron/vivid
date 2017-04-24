<?php

namespace Database\Connection
{
    use Database\Query\Builder;
    use Database\Schema\Column;
    use Database\Schema\Table;
    use Database\Schema\Type\AbstractType;
    use Utility\Regex;

    class DB
    {
        private static $connection;
        private static $tables;

        private static function GetConnection() : \PDO
        {
            if(static::$connection === null)
            {
                static::$connection = new \PDO('mysql:host=127.0.0.1;charset=utf8', 'root', 'Chris@cpb-1995!');
            }

            return static::$connection;
        }

        public static function Query(string $query) : array
        {
            $connection = static::GetConnection();
            $connection->beginTransaction();

            try
            {
                var_dump($query);

                $stm = $connection->prepare(trim($query));
                $stm->execute();

                $results = $stm->fetchAll(\PDO::FETCH_ASSOC);
                $lastID = $connection->lastInsertId() ?? -1;
                $count = $stm->rowCount();

                $connection->commit();
            }
            catch(\Exception $e)
            {
                $connection->rollBack();
            }

            return [
                "lastInsertId" => $lastID,
                "rowCount" => $count,
                "results" => $results,
            ];
        }

        public static function Table(string $table) : Builder
        {
            return new Builder($table);
        }
        public static function Create(Table $table) : Table
        {
            return static::Query($table);
        }

        public static function Select(Builder $builder) : array
        {
            return static::Query($builder->ToSql($builder::SELECT))["results"];
        }
        public static function Update(Builder $builder) : array
        {
            return static::Query($builder->ToSql($builder::UPDATE, ...array_slice(func_get_args(), 1)));
        }
        public static function Delete(Builder $builder) : array
        {
            return static::Query($builder->ToSql($builder::DELETE));
        }
        public static function Insert(Builder $builder) : array
        {
            return static::Query($builder->ToSql($builder::INSERT));
        }

        protected static function Tables(string $prefix = '')
        {
            return static::Table('information_schema.tables')->Select([
                'TABLE_SCHEMA',
                'TABLE_NAME',
                'TABLE_ROWS',
                'CREATE_TIME',
                'UPDATE_TIME',
            ])->Where('TABLE_SCHEMA LIKE "' . $prefix . '%"')->All();
        }
        protected static function Schema(string $database, string $table)
        {
            return static::Table('information_schema.columns')->Select([
                'COLUMN_NAME',
                'IS_NULLABLE',
                'DATA_TYPE',
                'CHARACTER_MAXIMUM_LENGTH',
                'COLUMN_TYPE',
                'COLUMN_KEY',
                'EXTRA',
            ])->Where('TABLE_SCHEMA = "' . $database . '" && TABLE_NAME = "' . $table . '"')->All();
        }
        protected static function Indexes(string $table)
        {
            return static::Query('SHOW INDEX FROM ' . Regex::Encapsulate($table, '`') . ';');
        }

        public static function Cache(string $path)
        {
            $path = getcwd() . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR;
            $file = $path . 'SchemaCache.json';

            $tables = [];

            if(!file_exists($file))
            {
                foreach(static::Tables('C_') as $table)
                {
                    extract($table);
                    $table = new Table($TABLE_SCHEMA . '.' . $TABLE_NAME);

                    foreach(static::Schema($TABLE_SCHEMA, $TABLE_NAME) as $column)
                    {
                        extract($column);

                        $table->$COLUMN_NAME = AbstractType::FromString($COLUMN_TYPE);
                    }

                    $indexes = [];
                    foreach(static::Indexes($table->GetName()) as $index)
                    {
                        extract($index);

                        if(!key_exists($Key_name, $indexes))
                        {
                            $indexes[$Key_name] = [];
                        }

                        if(!in_array($Column_name, $indexes[$Key_name]))
                        {
                            $indexes[$Key_name][] = $Column_name;
                        }
                    }

                    foreach($indexes as $index => $columns)
                    {
                        $table->SetIndex($index, $columns);
                    }

                    $tables[] = $table;
                }

                file_put_contents($file, json_encode($tables));
            }
            else
            {
                $tables = Table::FromJson(file_get_contents($file));
            }

            static::$tables = $tables;
        }
        public static function IsCached(string $name) : bool
        {
            foreach(static::$tables as $table)
            {
                if($table->GetName() === $name)
                {
                    return true;
                }
            }

            return false;
        }
        public static function GetCached(string $name) : Table
        {
            foreach(static::$tables as $table)
            {
                if($table->GetName() === $name)
                {
                    return $table;
                }
            }

            return null;
        }
    }
}
