<?php

require_once 'src/Database/Schema/IType.php';
require_once 'src/Utility/Regex.php';
require_once 'src/Database/Connection/DB.php';
require_once 'src/Database/Schema/Type/AbstractType.php';
require_once 'src/Database/Schema/Type/Date.php';
require_once 'src/Database/Schema/Type/Datetime.php';
require_once 'src/Database/Schema/Type/Integer.php';
require_once 'src/Database/Schema/Type/TinyInteger.php';
require_once 'src/Database/Schema/Type/Text.php';
require_once 'src/Database/Schema/Type/Varchar.php';
require_once 'src/Database/Schema/Type/Char.php';
require_once 'src/Database/Schema/Type/Timestamp.php';
require_once 'src/Database/Schema/Type/Enum.php';
require_once 'src/Database/Schema/Column.php';
require_once 'src/Database/Schema/Table.php';
require_once 'src/Database/Query/Builder.php';
require_once 'src/Database/Vivid/Collection.php';
require_once 'src/Database/Vivid/Model.php';
require_once 'Models/Route.php';
require_once 'Models/File.php';

use Database\Connection\DB;
use Database\Schema\Table;
use Database\Schema\Type\Integer;
use Database\Schema\Type\Varchar;

error_reporting(E_ALL);

set_error_handler(function($errno, $errstr, $errfile, $errline){
    var_dump([
        'error' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ]);
});

chdir(realpath(dirname(__FILE__)));

DB::Cache('Cache');

$file = File::Get(53);

if($file->Delete())
{
    var_dump($file->name);
}

function BenchMark(callable $callback) : string
{
    $start = microtime(true);

    for($i = 0; $i < 10000; $i++)
    {
        $callback();
    }

    return 'the benchmart took ' . ((microtime(true) - $start) * 1000) . ' milliseconds';
}
function PrintTable(array $rows)
{
    if(count($rows) == 0)
    {
        return;
    }

    echo '<table>';
    echo '<tr>';

    foreach(array_keys($rows[0]) as $key)
    {
        echo '<th>' . $key . '</th>';
    }

    echo '</tr>';

    foreach($rows as $row)
    {
        echo '<tr>';

        foreach($row as $cell)
        {
            echo '<td>' . $cell . '</td>';
        }

        echo '</tr>';
    }

    echo '</table>';
}

?>

<style>
    article {
        background: #fff;
        border: 1px solid #ccc;
        box-shadow: 0 0 15px #ccc;
        margin: 10px;
        padding: 10px;
    }

    h1, h2
    {
        margin: 10px 20px;
    }

    table
    {
        margin: 10px;
    }

    td, th
    {
        text-align: left;
        padding: 5px 10px;
    }
</style>
