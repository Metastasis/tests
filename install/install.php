<html>
<?php
// header('Content-Type: text/html; charset=utf-8');
?>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Установка базы данных</title>
	<link href="../style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id='pict_box'></div>
<?php
require_once '../config.php';
$server = 'localhost';
$username = 'root';
$password = '';

if (!isset($_POST['do_install']))
{
	echo "  <form name='form1' method='post'>
		<div id='header_menu'>Web-приложение для организации тестирования</div>
		<div id='content' align=center>
		<center>
			<h3>Программа установки базы данных</h3>
			Введите имя базы данных:
			<input type='text' name='db_name'><br><br>
			<input type='submit' name='do_install' value='Создать БД' >
			<p>Рекомендуется удалить файл с web-сервера после окончания установки.</p>
		</center>
		</div>
		</form>";

	
}	
else
{
	
	$connect_srv = mysql_connect ($server,$username,$password) or die (">Ошибка подключения к серверу.<br>");
	echo ">Подключение к серверу произведено успешно.<br>";

	// $create_db = mysql_query ("CREATE DATABASE `".$_POST['db_name']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;")
	// or die (">Ошибка создания новой базы данных.<br>");
	// echo '>База данных создана.<br>';
	
	$choose_new_db = mysql_select_db ($_POST['db_name']) or die (">Ошибка выбора базы данных");
	echo ">База данных выбрана.<br>";
	
	mysql_query("SET NAMES utf8");
	
	$create_users = mysql_query ("
		CREATE TABLE `".$_POST['db_name']."`.`USERS` (
		`ID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`USERNAME` VARCHAR( 255 ) NOT NULL ,
		`USERPASS` VARCHAR( 255 ) NOT NULL ,
		`USERGROUP` VARCHAR( 255 ) NOT NULL ,
		`USERVARIANT` INT( 1 ) NOT NULL 
		) ENGINE = MYISAM") or die (">Ошибка создания таблицы USERS.<br>");
	echo ">Таблица USERS создана.<br>";
	
	$add_admin = mysql_query ("
		INSERT INTO `".$_POST['db_name']."`.`users` (
		`ID` ,
		`USERNAME` ,
		`USERPASS` ,
		`USERGROUP` ,
		`USERVARIANT` 
		)
		VALUES (NULL , 'admin', 'admin', 'admin', '1');") or die (">Ошибка добавления записи администратора.");
	echo ">Добавлен профиль admin с паролем admin.<br>";

	$create_test_list = mysql_query("
		CREATE TABLE `".$_POST['db_name']."`.`TEST_LIST` (
		`ID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`TESTNAME` VARCHAR( 255 ) NOT NULL ,
		`TESTTABLE` VARCHAR( 255 ) NOT NULL ,
		`TESTPASS` VARCHAR( 255 ) NOT NULL ,
		`BALL5` INT(3) NOT NULL ,
		`BALL4` INT(2) NOT NULL ,
		`BALL3` INT(2) NOT NULL ,
		`TESTGROUPS` VARCHAR( 255 ) NOT NULL,
		`TESTMODE` VARCHAR( 255 ) NOT NULL,
		`TESTDATE` DATE NOT NULL,
		`TESTVARIANT` INT( 5 ) NOT NULL,
		`TESTCOUNT` INT( 5 ) NOT NULL
		
		) ENGINE = MYISAM") or die (">Ошибка создания таблицы TEST_LIST.<br>");
	echo ">Таблица TEST_LIST создана.<br>";

	$create_results = mysql_query ("
		CREATE TABLE `".$_POST['db_name']."`.`RESULTS` (
		`ID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`USERNAME` VARCHAR( 255 ) NOT NULL ,
		`TESTNAME` VARCHAR( 255 ) NOT NULL ,
		`TESTDATE` DATE NOT NULL ,
		`RESRIGHT` INT( 10 ) NOT NULL ,
		`RESALL` INT( 10 ) NOT NULL ,
		`RESBALL` INT( 10 ) NOT NULL ,
		`TESTLOG` TEXT NOT NULL 
		) ENGINE = MYISAM") or die (">Ошибка создания таблицы RESULTS.<br>");
	echo ">Таблица RESULTS создана.<br>";
	
	$create_speciality = mysql_query ("
		CREATE TABLE `".$_POST['db_name']."`.`speciality` (
		`ID` INT NOT NULL ,
		`NAME` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		`SHORT` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		PRIMARY KEY ( `ID` ) 
		) ENGINE = MYISAM") or die (">Ошибка создания таблицы SPECIALITY.<br>");
	echo ">Таблица SPECIALITY создана.<br>";
		
	$create_discipline = mysql_query("
		CREATE TABLE `".$_POST['db_name']."`.`discipline` (
		`ID` INT NOT NULL AUTO_INCREMENT ,
		`NAME` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		`SHORT` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		PRIMARY KEY ( `ID` ) 
		) ENGINE = MYISAM") or die (">Ошибка создания таблицы DISCIPLINE.<br>");
	echo ">Таблица DISCIPLINE создана.<br>";
	
	echo '<META HTTP-EQUIV=Refresh CONTENT="5; index.php">';
	}
?>
</body>
</html>