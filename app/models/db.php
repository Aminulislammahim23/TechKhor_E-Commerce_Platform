<?php
    $host = '127.0.0.1';
    $dbname   = 'techkhorDB';
    $dbuser = 'root';
    $dbpass = '';

    function getConnection() {
       global $dbname, $dbuser, $dbpass;
       $con = mysqli_connect($GLOBALS['host'], $dbuser, $dbpass, $dbname);
       return $con; 
    }
?>