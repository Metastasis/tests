<!--Страница просмотра результата-->
<?
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<html  style='overflow:auto;'>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Панель администрирования</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>
<body  style='overflow-x:hidden;'>
<div id='pict_box'></div>
<div id='header_menu'>
	<a href='result_loader.php?close=1'>Главная</a> | 
	<a href="../admin.php">Панель администрирования</a> | 
	<a href="test_list.php">Список тестов</a> | 
	<a href="user_list.php">Список пользователей</a> | 
	<a href="result_list.php">Список результатов</a>
</div>
<?php
require_once '../connect.php';

require_once '../config.php';
require_once '../functions.php';
session_start();
if (($_SESSION['username']=='') or ($_SESSION['usergroup']<>$admin_index))
{
	header('Location: ../index.php');
}
if(isset($_GET['close']))
{
	session_destroy();
	header('Location: ../index.php');
}

?>
<?php
if (isset($_GET['resultid']))
{
	$load_list = mysqli_query($connect_srv, "SELECT * FROM RESULTS WHERE ID =".$_GET['resultid']);
	$log = mysqli_fetch_array($load_list, MYSQLI_ASSOC);
	
	$fail_answers = $log['RESALL'] - $log['RESRIGHT'];
	echo'
	<div id="header_content">
	<h3 align="center">Результат прохождения теста &quot;'.$log['TESTNAME'].'&quot; пользователем &quot;'.$log['USERNAME'].'&quot;</h3>
	</div>
	<div id="action_header" >
	<h4 align="left">Правильных ответов: '.$log['RESRIGHT'].', неправильных ответов: '.$fail_answers.', всего вопросов: '.$log['RESALL'].', оценка: '.$log['RESBALL'].'</h3>
	</div>
	';
	
	echo prepear_readed_answer($log['TESTLOG']);
}
?>

</body>
</html>