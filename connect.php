<?php
//Файл подключения к базе данных

$server = 'localhost';
$username = 'Temp';
$password = 'Temp_12345';
$db_name = 'web_test';
error_reporting(0);
//Попытка подключения к серверу
$connect_srv = mysqli_connect($server,$username,$password,$db_name);
mysqli_query($connect_srv, "SET NAMES utf8"); //Использование имен только в кодировке UTF8
//$use_table = mysql_select_db ($db_name); //Попытка выбора используемой базы данных
if(!$connect_srv)
   {
      echo "Ошибка подключения (" . mysqli_connect_errno() . ") " . mysqli_connect_error() . ")";
   }

?>