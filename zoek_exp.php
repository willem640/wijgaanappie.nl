<?php

session_start();
require_once 'setup.php';
$query=DB::query('SELECT * FROM products LIMIT 100');
var_dump($query);
?>