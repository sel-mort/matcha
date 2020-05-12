<?php
    require_once "database.php";
    $DB_DSN = 'mysql:host=localhost';
    $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    );
    try
    {
        $conn = new PDO($DB_DSN,$DB_USER,$DB_PASSWORD,$options);
        $q = file_get_contents ("database.sql");

        $conn->exec($q);

        echo "Database created !";
    }
    catch(PDOException $e)
    {
        echo $e->getMessage();
    }
?>%