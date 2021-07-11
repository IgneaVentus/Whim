<?php
    namespace Whim\Controllers;
    require "../vendor/autoload.php"; 
    use Whim\Controllers\DBManipulator;

    if(isset($_GET["data"])) {
        $dbm = new DBManipulator($_GET["data"]);
    }
    else die ("No data");

    switch(array_shift($dbm->data)) {
        case "fetchall":
            switch(array_shift($dbm->data)) {
                case "article":
                    $art = new Whim\Models\Article();
                    echo(json_encode($art->fetchAll($dbm->conn)));
                    break;
            }
    }
?>
