<?php
//Файл подключения к базе данных

$server = 'localhost';
$username = 'root';
$password = '';
$db_name = 'test';
error_reporting(0);
//Попытка подключения к серверу
$connect_srv = mysql_connect ($server,$username,$password);
$use_table = mysql_select_db ($db_name); //Попытка выбора используемой базы данных

mysql_query("SET NAMES utf8"); //Использование имен только в кодировке UTF8

?>