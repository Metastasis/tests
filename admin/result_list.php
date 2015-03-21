<!--Страница работы со списком результатов-->
<?
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Панель администрирования</title>
	<link href="../style.css" rel="stylesheet" type="text/css" />
	<script src="../calendar.js"></script>
</head>
<body>
<div id='pict_box'></div>
<div id='header_menu'>
	<a href='result_list.php?close=1'>Главная</a> | 
	<a href="../admin.php">Панель администрирования</a> |
	<a href="test_list.php">Список тестов</a> | 
	<a href="user_list.php">Список пользователей</a> | 
<?php
if (isset($_GET['filter']) || isset($_GET['formid']))
{
	echo "<a href='result_list.php'>Список результатов</a>";
}
else
{
	echo "Список результатов";
}
?>
</div>
<form name='form1' method='POST'>
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

//Вывод списка результатов на экран
if (!isset($_GET['filter']) && !isset($_GET['formid']))
{
	//Если сортировка активна, выводит кнопку, позволяющую ее отключить
	//Если сортировка неактивна, выводит кнопку, позволяющую изменить сортировку первого поля
	if (isset($_GET['order']) || isset($_GET['desc']))
	{
		$stop_btn = "<th><a href='result_list.php'><img title='Отменить сортировку' src='../images/buttons/delete_btn.png'  alt='Сортировка'></a></th>";
	}
	else
	{
		$stop_btn = "<th><a href='result_list.php?desc=true'><img title='Сортировать в обратную сторону' src='../images/buttons/updown_btn.png'  alt='Сортировка'></a></th>"; 
	}
	//Позволяет менять поле, по которому происходит сортировка
	switch ($_GET['order'])
	{
		case 'username'; 
			$order = 'USERNAME'; 
			
			if (!isset($_GET['desc']))
			{
				$link_name= '?order=username&desc=true';  $link_group = '?order=testname'; $link_date = '?order=date'; 
			}
			else
			{
				$link_name = '?order=username'; $link_group = '?order=testname'; $link_date = '?order=date'; 
			}
			break;
			
		case 'testname'; 
			$order = 'TESTNAME';
			
			if (!isset($_GET['desc']))
			{
				$link_name= '?order=username'; $link_group = '?order=testname&desc=true';  $link_date = '?order=date'; 
			}
			else
			{
				$link_name = '?order=username'; $link_group = '?order=testname'; $link_date = '?order=date'; 
			}
			break;
		
		case 'date'; 
			$order = 'TESTDATE';
			
			if (!isset($_GET['desc']))
			{ 
				$link_name= '?order=username'; $link_group = '?order=testname'; $link_date = '?order=date&desc=true'; 
			}
			else
			{
				$link_name = '?order=username'; $link_group = '?order=testname'; $link_date = '?order=date';
			}
			break;
			
		default: $order = "ID"; $link_name = '?order=username'; $link_group = '?order=testname'; $link_date = '?order=date';  break;
	}
	//Показывает направление сортировки
	if (isset($_GET['desc']))
	{
		$desc = 'desc';
		$sort_pict ='up_btn.png';
	}
	else
	{
		$desc = '';
		$sort_pict ='down_btn.png';
	}
	
	echo "
	<div id='content'>
	<h3>Список пройденных тестов</h3>


	<center>

	<div id='res_center_big_block' style='
			height:580px;
			clear:both;
			overflow:auto;'>";

	echo '
	<table  cellpadding="5" cellspacing="4" bordercolor="#ff0000" > 

	<tr bgcolor=#DBE7FF>
		<th><a href="result_list.php'.$link_name.'">Имя пользователя</a></th>
		<th><a href="result_list.php'.$link_group.'">Имя теста</a></th>
		<th><a href="result_list.php'.$link_date.'">Дата</a></th>
		<th>Балл</th>
		<th colspan=3>Действия</th>
		'.$stop_btn.'
	</tr>


	<div id="action_header">
		<a href="result_list.php?filter=1">Фильтрация</a>
	</div>';
	
	
	//Применение фильтра, если он активен
	if (isset($_SESSION['rl_filter_active']))
	{
		$filter = "WHERE `USERNAME` LIKE '%".$_SESSION['rl_filter_by_username']."%' AND
						 `TESTNAME` LIKE '%".$_SESSION['rl_filter_by_testname']."%' AND 
						 `TESTDATE` LIKE '%".$_SESSION['rl_filter_by_date']."%'";
	}
	else
	{
		$filter = '';
	}
	
	//Вывод записей на экран
	$load_list = mysqli_query($connect_srv, "SELECT * FROM RESULTS ".$filter." ORDER BY ".$order." ".$desc);
	if (mysql_num_rows($load_list)>0) 
	{
		$j=0;
		while ($result_list = mysqli_fetch_array($load_list, MYSQLI_ASSOC))
		{		
			if ( ($j % 2) == 0)
			{
				$tr_color = "bgcolor='#F5F5FF'";
			}
			else
			{
				$tr_color = "bgcolor='#E1E1EB'";
			}
			echo "
				<tr ".$tr_color."  align=center>
					<td>".$result_list['USERNAME']."</td>
					<td>".$result_list['TESTNAME']."</td>
					<td>".convert_data_for_user($result_list['TESTDATE'])."</td>
					<td>".$result_list['RESBALL']."</td>
					<td><a title='Показать результат' href='result_loader.php?resultid=".$result_list['ID']."'><img src='../images/buttons/show_btn.png' alt='Показать результат'></a></td>
					<td><a title='Печатная форма' href='result_list.php?formid=".$result_list['ID']."'><img src='../images/buttons/form_btn.png' alt='Печатная форма'></a></td>
					<td><a title='Удалить результат' href='result_list.php?deleteid=".$result_list['ID']."'><img src='../images/buttons/delete_btn.png' alt='Удалить результат'></a></td>
				";		
			if ($j == 0)
			{
				echo "<td><img title='Направление сортировки' src='../images/buttons/".$sort_pict."'  alt='Сортировка'></td>";
			}
			echo "</tr>";
			$j++;
		}
	}
	echo "</table>
	</center>
	</div></div>";
}

//Выводит сообщение, нужно ли удалять данный результат
if (isset($_GET['deleteid']))
{
	echo  '	
	<SCRIPT LANGUAGE="javascript">
	if (confirm("Удалить результат прохождения теста?")) {
		parent.location="result_list.php?deleteidaccepted='.$_GET['deleteid'].'";
		}
	</SCRIPT>';
}
//Удаляет результат, если это подвтерждено пользователем
if (isset($_GET['deleteidaccepted']))
{
	$delete_test = mysqli_query($connect_srv, "
	DELETE FROM `results` WHERE `results`.`ID` = ".$_GET['deleteidaccepted']." LIMIT 1 ");
	header ('Location: result_list.php');
}

//Организация фильтрации
//Использует переменные в глобальном массиве $_SESSION для указания того, активна ли фильтрация, и передачи к списку введенных фрагментов для фильтрации
if (isset($_GET['filter']))
{
	//Проверка правильности ввода значений, если были выбраны указатели активности фильтра
	if (isset($_POST['use_filter']))
	{
		$validate_filter = true;
		if ($_POST['use_filter_by_username'] == 'ON'  && $_POST['filter_by_username'] == '' )
		{
			$validate_filter = false;
			$report = 'rl_usernamefilter_error';
		}
		elseif  ($_POST['use_filter_by_testname'] == 'ON'  && $_POST['filter_by_testname'] == '')
		{
			$validate_filter = false;
			$report = 'rl_testnamefilter_error';
		}
		elseif  ($_POST['use_filter_by_date'] == 'ON'  && $_POST['filter_by_date'] == '')
		{
			$validate_filter = false;
			$report = 'rl_datefilter_error';
		}
		//Вывод неправильно введенных полей на экран
		if ($validate_filter == false)
		{
			if ($_POST['use_filter_by_username'] == 'ON')
			{
				$value_filter_username = 'value="'.$_POST['filter_by_username'].'"';
				$check_filter_username = 'checked=true';
			}
			else
			{
				$value_filter_username = '';
				$check_filter_username = '';
			}
			if ($_POST['use_filter_by_testname']=='ON')
			{
			
				$value_filter_testname = 'value="'.$_POST['filter_by_testname'].'"';
				$check_filter_testname = 'checked=true';
				
			}
			else
			{
				$value_filter_testname = '';
				$check_filter_testname = '';
			}
			if ($_POST['use_filter_by_date']=='ON')
			{
			
				$value_filter_date = 'value="'.$_POST['filter_by_date'].'"';
				$check_filter_date = 'checked=true';
				
			}
			else
			{
				$value_filter_date = '';
				$check_filter_date = '';
			}
		}
	}
	else
	//Вывод на экран значений, хранимых в глобальном массиве $_SESSIONS
	{
		if (isset($_SESSION['rl_filter_by_username']))
		{
			$value_filter_username = 'value="'.$_SESSION['rl_filter_by_username'].'"';
			$check_filter_username = 'checked=true';
		}
		else
		{
			$value_filter_username = '';
			$check_filter_username = '';
		}
		if (isset($_SESSION['rl_filter_by_testname']))
		{
			$value_filter_testname = 'value="'.$_SESSION['rl_filter_by_testname'].'"';
			$check_filter_testname = 'checked=true';
		}
		else
		{
			$value_filter_testname = '';
			$check_filter_testname = '';
		}
		if (isset($_SESSION['rl_filter_by_date']))
		{
			$value_filter_date = 'value="'.convert_data_for_user($_SESSION['rl_filter_by_date']).'"';
			$check_filter_date = 'checked=true';
		}
		else
		{
			$value_filter_date = '';
			$check_filter_date = '';
		}
	}
	echo "
	<div id='content'>
		<h3>Настройка фильтрации</h3>
	</div>
	<center>
	
	<style>
	TR {
		text-align:left;
	}
	</style>
	
	<table  cellpadding=8 cellspacing=0 >
	<tr bgcolor=E1E1EB>
		<td>Фильтрация по имени пользователя:</td>
		<td><input type='checkbox' name='use_filter_by_username' value='ON' onclick='filter_username_switch ()' ".$check_filter_username."></td>
		<td><input type='text' size=40 name='filter_by_username' ".$value_filter_username."></td>
	</tr>
	
	<tr>
		<td>Фильтрация по имени теста:</td>
		<td><input type='checkbox' name='use_filter_by_testname' value='ON' onclick='filter_testname_switch ()' ".$check_filter_testname."></td>
		<td><input type='text' size=40 name='filter_by_testname'  ".$value_filter_testname."></td>
	</tr>
	
	<tr bgcolor=E1E1EB>
		<td>Фильтрация по дате прохождения:</td>
		<td><input type='checkbox' name='use_filter_by_date' value='ON' onclick='filter_date_switch ()' ".$check_filter_date."></td>
		<td><input type='text' size=40 name='filter_by_date'  ".$value_filter_date." readonly=true
			onfocus='this.select();lcs(this)'	onclick='event.cancelBubble=true;this.select();lcs(this)'></td>
	</tr>
	
	</table>
	
	<p>
		<input type='submit' name='use_filter' value='Применить фильтр'>
		<input type='submit' name='reset_filter' value='Сбросить фильтр'>
		<input type='submit' name='cancel_action' value='Отмена'>
	</p>
	</center>";
}
//Если значения введены правильно, то применяет фильтра
if (isset($_POST['use_filter']) && $validate_filter == true)
{
	if (isset($_POST['use_filter_by_username']) && $_POST['filter_by_username'] != '')
	{
		$_SESSION['rl_filter_by_username'] = $_POST['filter_by_username'];
		$_SESSION['rl_filter_active'] = true;	
	}
	if (isset($_POST['use_filter_by_testname']) && $_POST['filter_by_testname'] != '')
	{
		$_SESSION['rl_filter_by_testname'] = $_POST['filter_by_testname'];
		$_SESSION['rl_filter_active'] = true;
	}
	if (isset($_POST['use_filter_by_date']) && $_POST['filter_by_date'] != '')
	{
		$_SESSION['rl_filter_by_date'] = convert_data_for_sql($_POST['filter_by_date']);
		$_SESSION['rl_filter_active'] = true;
	}
		
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; result_list.php">';
}
//Отключение фильтрации
if(isset($_POST['reset_filter']))
{
	unset($_SESSION['rl_filter_by_username']);
	unset($_SESSION['rl_filter_by_testname']);
	unset($_SESSION['rl_filter_by_date']);
	unset($_SESSION['rl_filter_active']);
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; result_list.php">';
}
//Подготовка печатной формы. Требует выбрать специальность и дисциплину
if (isset($_GET['formid']))
{
	echo '
	<style>
		TD {
		text-align:left;
		}
	</style>
	<div id="content">
		<h3>Подготовка печатной формы</h3>
		<table cellpadding=10 align=center cellspacing=0>
		<tr bgcolor="#E1E1EB">
			<td>Специальность</td>
			<td>
				<select name="name_spec">';
	//Создает список, состоящий из кратких названий специальности
	$load_speciality = mysqli_query($connect_srv, "SELECT * FROM `speciality` ORDER BY ID DESC") or die ('Ошибка');
	if (mysql_num_rows($load_speciality)>0)
	{
		while ($spec_list = mysqli_fetch_array($load_speciality, MYSQLI_ASSOC))
		{
			echo '<option value="'.$spec_list['ID'].'">'.$spec_list['SHORT'].'</option>';
		}
	}
	echo	   '</select>
			</td>
		</tr>
		<tr>
			<td>Дисциплина</td>
			<td>
				<select name="name_disc">';
	//Создает список, состоящий из кратких названий специальности
	$load_discipline = mysqli_query($connect_srv, "SELECT * FROM `discipline` ORDER BY ID DESC") or die ('Ошибка');
	if (mysql_num_rows($load_speciality)>0)
	{
		while ($disc_list = mysqli_fetch_array($load_discipline, MYSQLI_ASSOC))
		{
			echo '<option value="'.$disc_list['ID'].'">'.$disc_list['SHORT'].'</option>';
		}
	}
	echo	   '</select>
			</td>
		</tr>
		<tr bgcolor="#E1E1EB">
			<td>Показывать дату?</td>
			<td><input type=checkbox name="show_date" value="YES"></td>
		</tr>
		</table>
		<p>
			<input type="submit" name="load_form" value="Создать печатную форму">
			<input type="submit" name="cancel_action" value="Отмена">
		</p>
	</div>
	';
}
//Подготовка ссылки, по которой будут переданы данные, нужные при создании печатной формы
if (isset($_POST['load_form']))
{
	//Нужно ли выводить дату на экран?
	if ($_POST['show_date'] == 'YES')
	{
		$show_date = '&show_date=true';
	}
	else
	{
		$show_date = '';
	}
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; print_form.php?formid='.$_GET['formid'].'&name_spec='.$_POST['name_spec'].'&name_disc='.$_POST['name_disc'].''.$show_date.'">';
}
//Общая кнопка отмены действия
if (isset($_POST['cancel_action']))
{
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; result_list.php">';
}
//Вывод ошибки на экран
switch( $report ) 
{ 
	case 'rl_usernamefilter_error': 
        echo "<script> alert('Ошибка! Вы не заполнили фильтр для имени пользователя.');</script>";
		break;
	case 'rl_testnamefilter_error': 
        echo "<script> alert('Ошибка! Вы не заполнили фильтр для имени теста.');</script>";
		break;
	case 'rl_datefilter_error': 
        echo "<script> alert('Ошибка! Вы не заполнили фильтр для даты.');</script>";
		break;
}

?>

</form>

</body>
</html>