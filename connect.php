<?php
//Файл подключения к базе данных

$server = 'localhost';
$username = 'root';
$password = 'ithcnmyfyjce';
$db_name = 'web_test';
error_reporting(0);
//Попытка подключения к серверу
$connect_srv = mysql_connect ($server,$username,$password);
$use_table = mysql_select_db ($db_name); //Попытка выбора используемой базы данных

mysql_query("SET NAMES utf8"); //Использование имен только в кодировке UTF8

?>