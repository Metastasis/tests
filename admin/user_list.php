<!--Страница работы со списком пользователей-->
<?
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Список пользователей</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id='pict_box'></div>
<div id='header_menu'>
	<a href='user_list.php?close=1'>Главная</a> | 
	<a href="../admin.php">Панель администрирования</a> | 
	<a href="test_list.php">Список тестов</a> | 
<?php
if (isset($_GET['create']) || isset($_GET['editid']) || isset($_GET['import']) || isset($_GET['filter']))
{
	echo "<a href='user_list.php'>Список пользователей</a> | 	";
}
else
{
	echo "Список пользователей | ";
}
?>
<a href="result_list.php">Список результатов</a>
</div>
<?php
require_once '../connect.php';
require_once '../config.php';
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
<form name='form' method='post' enctype="multipart/form-data">
	<center>
<?php

//Вывод на экран списка пользователей
if (!isset($_GET['create']) && !isset($_GET['editid']) && !isset($_GET['import']) && !isset($_GET['filter']))
{
	//Если сортировка была изменена, выводит кнопку, с помощью которой можно ее отключить
	//Если сортировка неактивна, выводит кнопку, позволяющую изменить сортировку первого поля
	if (isset($_GET['order']) || isset($_GET['desc']))
	{
		$stop_btn = "<th><a href='user_list.php'><img title='Отменить сортировку' src='../images/buttons/delete_btn.png'  alt='Сортировка'></a></th>";
	}
	else
	{
		$stop_btn = "<th><a href='user_list.php?desc=true'><img title='Сортировать в обратную сторону' src='../images/buttons/updown_btn.png'  alt='Сортировка'></a></th>"; 
	}
	//Выбор поля, по которому будет происходит сортировка
	switch ($_GET['order'])
	{
		case 'name'; 
			$order = 'USERNAME'; 
			
			if (!isset($_GET['desc']))
			{
				$link_name= '?order=name&desc=true';  $link_group = '?order=group'; 
			}
			else
			{
				$link_name = '?order=name'; $link_group = '?order=group';
			}
			break;
			
		case 'group'; 
			$order = 'USERGROUP';
			
			if (!isset($_GET['desc']))
			{
				$link_name= '?order=name'; $link_group = '?order=group&desc=true'; 
			}
			else
			{
				$link_name = '?order=name'; $link_group = '?order=group';
			}
			break;
			
		default: $order = "ID"; $link_name = '?order=name'; $link_group = '?order=group'; break;
	}
	//Показывает на экране, происходит ли сортировка по возрастанию или убыванию
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
	echo '
	<div id="content">
		
	<h3>Список зарегистрированных пользователей</h3>


		<div id="action_header" >
			<a href="user_list.php?create=true">Добавить пользователя</a> | 
			<a href="user_list.php?import=1">Импорт из файла CSV</a> |
			<a href="user_list.php?filter=1">Фильтрация</a>
		</div>
	</div>';
	
		
	echo "<div id='ul_center_big_block' style='
		height:520px;
		clear:both;
		width:100%;
		overflow:auto;'>";
	echo '
	<table  cellpadding="5" cellspacing="4" bordercolor="#ff0000" > 
	<tr bgcolor=#DBE7FF>
		<th><a href="user_list.php'.$link_name.'">Имя пользователя</a></th>
		<th>Пароль</th>
		<th><a href="user_list.php'.$link_group.'">Группа</a></th>
		<th>Вариант</th>
		<th colspan=2>Действия</th>
		'.$stop_btn.'
	</tr>
	';
	//Применение фильтра, если он активен
	if (isset($_SESSION['filter_active']))
	{
		$filter = "WHERE `USERNAME` LIKE '%".$_SESSION['filter_by_name']."%' AND `USERGROUP` LIKE '%".$_SESSION['filter_by_group']."%'";
	}
	else
	{
		$filter = '';
	}
	$load_list = mysql_query("SELECT * FROM USERS ".$filter." ORDER BY ".$order." ".$desc."");
	if (mysql_num_rows($load_list)>0) 
	{
		$j=0;
		while ($user_list = mysql_fetch_assoc($load_list))
		{
			if ($user_list['USERGROUP'] == $admin_write)
			{
				$user_list['USERGROUP'] = 'Администратор';
				$user_list['USERPASS'] = '---';
			}
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
					<td>".$user_list['USERNAME']."</td>
					<td>".$user_list['USERPASS']."</td>
					<td>".$user_list['USERGROUP']."</td>
					<td>".$user_list['USERVARIANT']."</td>
					<td><a href='user_list.php?editid=".$user_list['ID']."'><img title='Изменить пользователя' src='../images/buttons/edit_btn.png'  alt='Изменить'></a></td>
					<td><a href='user_list.php?deleteid=".$user_list['ID']."'><img title='Удалить пользователя' src='../images/buttons/delete_btn.png'  alt='Удалить'></a></td>";		
			if ($j == 0)
			{
				echo "<td><img title='Направление сортировки' src='../images/buttons/".$sort_pict."'  alt='Сортировка'></td>";
			}
			echo "</tr>";
			$j++;
		}
		echo "</table>";
	}			
	else
	{
		echo "</table>";
	}
	echo "</div>";
}

//Выводит сообщение о том, удалять ли выбранного пользователя
if (isset($_GET['deleteid']))
{
	echo  '
	<SCRIPT LANGUAGE="javascript">
	if (confirm("Удалить пользователя?")) {
		parent.location="user_list.php?deleteidaccepted='.$_GET['deleteid'].'";
		}
	</SCRIPT>';
}
//Удаление пользователя из списка. Работает после подтверждения
if (isset($_GET['deleteidaccepted']))
{
	$delete_user = mysql_query("
	DELETE FROM `users` WHERE `users`.`ID` = ".mysql_real_escape_string($_GET['deleteidaccepted'])." LIMIT 1 ");
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; user_list.php">';
}	

//Организация фильтрации
//Использует переменные в глобальном массиве $_SESSION для указания того, активна ли фильтрация, и передачи к списку введенных фрагментов для фильтрации
if (isset($_GET['filter']))
{
	//Проверка вводимых полей и установлен ли флажок слева от поля ввода фильтра
	if (isset($_POST['use_filter']))
	{
		$validate_filter = true;
		if ($_POST['use_filter_by_name'] == 'ON'  && $_POST['filter_by_name'] == '' )
		{
			$validate_filter = false;
			$report = 'ul_namefilter_error';
		}
		elseif  ($_POST['use_filter_by_group'] == 'ON'  && $_POST['filter_by_group'] == '')
		{
			$validate_filter = false;
			$report = 'ul_groupfilter_error';
		}
		if ($validate_filter == false)
		{
			if ($_POST['use_filter_by_name'] == 'ON')
			{
				$value_filter_name = 'value="'.$_POST['filter_by_name'].'"';
				$check_filter_name = 'checked=true';
			}
			else
			{
				$value_filter_name = '';
				$check_filter_name = '';
			}
			if ($_POST['use_filter_by_group']=='ON')
			{
			
				$value_filter_group = 'value="'.$_POST['filter_by_group'].'"';
				$check_filter_group = 'checked=true';			
			}
			else
			{
				$value_filter_group = '';
				$check_filter_group = '';
			}
		}
	}
	else
	{
		if (isset($_SESSION['filter_by_name']))
		{
			$value_filter_name = 'value="'.$_SESSION['filter_by_name'].'"';
			$check_filter_name = 'checked=true';
		}
		else
		{
			$value_filter_name = '';
			$check_filter_name = '';
		}
		if (isset($_SESSION['filter_by_group']))
		{
		
			$value_filter_group = 'value="'.$_SESSION['filter_by_group'].'"';
			$check_filter_group = 'checked=true';			
		}
		else
		{
			$value_filter_group = '';
			$check_filter_group = '';
		}
	}
	//Форма ввода фильтра
	echo "
	<div id='content'>
		<h3>Настройка фильтрации</h3>
	</div>

	
	<table  cellpadding=8 cellspacing=0 >
	<tr bgcolor=E1E1EB>
		<td>Фильтрация по имени:</td>
		<td><input type='checkbox' name='use_filter_by_name' value='ON' onclick='filter_name_switch ()' ".$check_filter_name."></td>
		<td><input type='text' size=40 name='filter_by_name' ".$value_filter_name."></td>
	</tr>
	
	<tr>
		<td>Фильтрация по группе:</td>
		<td><input type='checkbox' name='use_filter_by_group' value='ON' onclick='filter_group_switch ()' ".$check_filter_group."></td>
		<td><input type='text' size=40 name='filter_by_group'  ".$value_filter_group."></td>
	</tr>
	
	</table>
	
	<p>
		<input type='submit' name='use_filter' value='Применить фильтр'>
		<input type='submit' name='reset_filter' value='Сбросить фильтр'>
		<input type='submit' name='cancel_action' value='Отмена'>
	</p>";
}
//Активация фильтра на странице
if (isset($_POST['use_filter']) && $validate_filter == true)
{
	if (isset($_POST['use_filter_by_name']) && $_POST['filter_by_name'] != '')
	{
		$_SESSION['filter_by_name'] = $_POST['filter_by_name'];
		$_SESSION['filter_active'] = true;	
	}
	if (isset($_POST['use_filter_by_group']) && $_POST['filter_by_group'] != '')
	{
		$_SESSION['filter_by_group'] = $_POST['filter_by_group'];
		$_SESSION['filter_active'] = true;
	}
		
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; user_list.php">';
}
//Отключение фильтрации
if(isset($_POST['reset_filter']))
{
	unset($_SESSION['filter_by_name']);
	unset($_SESSION['filter_by_group']);
	unset($_SESSION['filter_active']);
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; user_list.php">';
}
//Импортирование из файла CSV
if (isset($_GET['import']) || isset($_POST['load_data']))
{
	echo "
	<style>
	TD {
		text-align:center;
	}
	</style>
		<div id='content'>
			<h3>Импорт из внешнего CSV-файла</h3>
		</div>";
		
	
	echo '<input type="file" name="input_file" size="50" value="1"/>';
	echo "<h4><input type='submit' name='load_data' value='Загрузить файл'>";
	//Если файл существует, то выводит его на экран
	if (isset($_FILES["input_file"]["name"]) && $_FILES["input_file"]["name"] != '')
	{
		$file_exp = explode(".", $_FILES["input_file"]["name"]); 
		if ($file_exp[1] == "csv") 
		{
			
			echo "<input type='submit' name='accept_import' value='Подтвердить импортирование'>
				<input type='submit' name='cancel_action' value='Отмена'>
				<input type='hidden' name='file_name' value='".$_FILES["input_file"]["tmp_name"]."'></h4>
				";
				
				
			echo "<div id='ul_center_big_block' style='
					height:400px;
					clear:both;
					width:100%;
					overflow:auto;'>";
			
			
			echo "<table  cellpadding=5 cellspacing=4 bordercolor='#ff0000' > 
				  <tr align='left' bgcolor=#DBE7FF>
					<th>Имя пользователя</th>
					<th>Пароль</th>
					<th>Группа</th>
					<th>Вариант</th>
				  <tr>";
			
			//Вывод содрежимого файла на экран по записям
			if(move_uploaded_file ($_FILES["input_file"]["tmp_name"],$copy_file_dir."/input.csv"))
			{
				$file_csv = fopen ($copy_file_dir."/input.csv","r");
				$j=0;
				while ($data = fgetcsv ($file_csv, 1000, ";"))
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
						<tr align='left' ".$tr_color.">
							<td>".$data[0]."</td>
							<td>".$data[1]."</td>
							<td>".$data[2]."</td>
							<td>".$data[3]."</td>
						</tr>	";
					$j++;
				}
				echo "</table></div>";
				fclose ($file_csv);	
			}
		}
		else 
		{
			$report = 'ul_csvwrongfile_error';
		}
	}
	else
	{
		echo "<input type='submit' name='cancel_action' value='Отмена'></h4>";
	}
}
//Подтверждение импорта из файла
if (isset($_POST['accept_import']))
{
	//Выбирает пересохраненный файл
	$file_csv = fopen ($copy_file_dir."/input.csv","r");
	$validate_input_data = true;
	
	$i=0;
	//Проверка правильности содержимого файла
	while ($data = fgetcsv ($file_csv, 1000, ";"))
	{
		$usernames = mysql_query ("SELECT `ID` FROM USERS WHERE `USERNAME` LIKE '".$data[0]."'"); 
		
		if (mysql_num_rows ($usernames) > 0)
		{
			
			$validate_input_data = false;
			$report = 'ul_csvexists_error';
		}
		elseif ($data[3] > 4 || $data[3] < 1)
		{
			$report = 'ul_csvvariant_notice';
		}
		$i++;
		$data_0[$i] = $data[0];
		
	}
	fclose ($file_csv);	
	//Добавление содержимого файла в таблицу USERS
	if ($validate_input_data == true)
	{
		$file_csv = fopen ($copy_file_dir."/input.csv","r");
		print_r ($data_0);
		$i=0;
		while ($data = fgetcsv ($file_csv, 1000, ";"))
		{
			$i++;
			if (!array_search($data[0], $data_0, TRUE) || array_search($data[0], $data_0, TRUE) == $i)
			{
				if ($data[3] > 4 || $data[3] < 1)
				{
					$data[3] = 1;
				}
				$import_csv = mysql_query("
				INSERT INTO  .`users` 
				VALUES (NULL , '".$data[0]."', '".$data[1]."', '".$data[2]."', '".$data[3]."'); ");
				
			}
			else
			{
				$report = 'ul_csvinexists_notice';
			}
		}
		echo '<META HTTP-EQUIV=Refresh CONTENT="0; user_list.php">';
		fclose ($file_csv);	
	}
	else
	{
		echo '<META HTTP-EQUIV=Refresh CONTENT="0; user_list.php?import=1">';
	}
}
//Создание или редактирование пользователя
//Кнопки имеют общий алгоритм, только при редактировании и проверке правильности ввода значений программа автоматически заполняет поля ввода значений
if (isset($_GET['create']) || isset($_GET['editid']))
{
	//Проверка правильности ввода значений
	if (isset($_POST['create_user']) || isset($_POST['edit_user']))
	{
		$usernames = mysql_query ("SELECT `ID` FROM USERS WHERE `USERNAME` LIKE '".mysql_real_escape_string($_POST['username'])."'"); 
		
		$validate_user_list = true;
		if ($_POST['username']== '')
		{
			$validate_user_list = false;
			$report = 'ul_name_error';
		}
		elseif ($_POST['userpass']== '')
		{
			$validate_user_list = false;
			$report = 'ul_pass_error';
		}
		elseif ($_POST['uservariant']== '')
		{
			$validate_user_list = false;
			$report = 'ul_variant_error';
		}
		elseif (($_POST['uservariant'] > 4 || $_POST['uservariant'] < 1) && $_POST['select_group'] != 'ADMIN')
		{
			$validate_user_list = false;
			$report = 'ul_variantmax_error';
		}
		elseif ($_POST['select_group'] != 'ADMIN' && $_POST['usergroup']== '')
		{
			$validate_user_list = false;
			$report = 'ul_group_error';
		}
		elseif (mysql_num_rows ($usernames) > 0)
		{
			if (isset($_GET['editid']))
			{
				
				$str = 0;
				while ($ids = mysql_fetch_assoc($usernames))
				{
					$str++;
					if ($ids['ID'] == $_GET['editid'])
					{
						$row_finded = 'yes';
					}
				}
				if ($str == 1 && $row_finded == 'yes')
				{
					$its_this_row = 'yes';
				}
				
				
			}
			if (isset($_GET['create']) || $its_this_row != 'yes')
			{
				$validate_user_list = false;
				$report = 'ul_nameexists_error';
			}
		}
	}
	//Автоматическое заполнение форм ввода. Работает только в случаех, если пользователь редактируется, или поля были заполнены неправильно
	if (isset($_GET['editid']))
	{
		$load_list = mysql_query("SELECT * FROM USERS WHERE ID=".mysql_real_escape_string($_GET['editid']));
		$user_list = mysql_fetch_assoc($load_list);
		
		$value_username = "value='".$user_list['USERNAME']."'";
		$value_userpass = "value='".$user_list['USERPASS']."'";
		$value_uservariant = "value='".$user_list['USERVARIANT']."'";
		
		if ($user_list['USERGROUP'] == $admin_write)
		{
			$user_list['USERGROUP'] = '---';
			$off_usergroup ='checked=true';
			$on_usergroup = '';
			$disable_usergroup = 'disabled=true';
		}
		else
		{
			$value_usergroup = "value='".$user_list['USERGROUP']."'";
			$on_usergroup = 'checked=true';
			$off_usergroup = $disable_usergroup = '';
		}
		
	}
	else
	{
		$value_username = $value_userpass = $value_usergroup = $value_uservaiant = $off_usergroup = $disable_usergroup = '';
		$on_usergroup = 'checked=true';
	}
	
	if (isset($validate_user_list) && ($validate_user_list == false))
	{
		$value_username = "value='".$_POST['username']."'";
		$value_userpass = "value='".$_POST['userpass']."'";
		$value_uservariant = "value='".$_POST['uservariant']."'";
		$value_usergroup = "value='".$_POST['usergroup']."'";
		
		if ($_POST['select_group'] == 'USER')
		{
			$on_usergroup = 'checked=true';
			$off_usergroup = $disable_usergroup = '';
		}
		elseif ($_POST['select_group'] == 'ADMIN')
		{
			$off_usergroup ='checked=true';
			$on_usergroup = '';
			$disable_usergroup = 'disabled=true';
		}
	}
	//Вывод на экран формы ввода
	echo "
	<style>
	TR { text-align:left;}
	</style>
	
	<script>
	function group_switch ()
	{
		document.all['usergroup'].disabled=document.all['admin'].checked;
	}
	</script>
	
	<div id=content>";
	if (isset($_GET['create']))
	{
		echo'<h3>Создание нового пользователя</h3>';
	}
	else
	{
		echo'<h3>Редактирование пользователя</h3>';
	}
	echo "
	</div>
	<table  cellpadding=8 cellspacing=0 >
	<tr bgcolor=E1E1EB>
		<td>Имя: </td>
		<td colspan=3><input type='text' size=40 name='username' ".$value_username."></td>
	</tr>
	<tr>
		<td>Пароль: </td>
		<td><input type='text' name='userpass' size=20 ".$value_userpass." ></td>
		<td>Вариант: </td>
		<td><input type='text' name='uservariant' size=3 maxlength=1 ".$value_uservariant."></td>
	</tr>
	<tr bgcolor=E1E1EB>
		<td>Группа: </td>
		<td><input type=radio name='select_group' value='USER' id='user' ".$on_usergroup."  onclick='group_switch ()'><input type='text' size=17 name='usergroup' ".$value_usergroup."  ".$disable_usergroup."></td>
		<td colspan=2><input type=radio name='select_group' id='admin' ".$off_usergroup." value='ADMIN' onclick='group_switch ()'>Администратор</td>
	</tr>
	
	</table>";

	if (isset($_GET['create']))
	{
	echo "
	<p>
		<input type='submit' name='create_user' value='Создать пользователя'>
		<input type='submit' name='cancel_action' value='Отмена'>
	</p>";
	}
	else
	{
	echo "
	<p>
		<input type='submit' name='edit_user' value='Применить изменения'>
		<input type='submit' name='cancel_action' value='Отмена'>
	</p>";
	}
}
//Применение значенйи к пользователю, если он редактируется
if (isset($_POST['edit_user']) && $validate_user_list == true)
{
	if ($_POST['select_group'] == 'ADMIN')
	{
		$_POST['usergroup'] = $admin_write;
	}
	$edit_user = mysql_query("
	UPDATE  .`users`
		SET `USERNAME` = '".mysql_real_escape_string($_POST['username'])."',
	`USERPASS` = '".mysql_real_escape_string($_POST['userpass'])."',
	`USERGROUP` = '".mysql_real_escape_string($_POST['usergroup'])."',
	`USERVARIANT` = '".mysql_real_escape_string($_POST['uservariant'])."'
	WHERE `users`.`ID` =".mysql_real_escape_string($_GET['editid'])." LIMIT 1 ;");
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; user_list.php">';
	
}
//Общая кнопка отмены действия
if (isset($_POST['cancel_action']))
{
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; user_list.php">';
}
//Добавление новой записи в таблицу, если пользователь создается
if (isset($_POST['create_user']) && $validate_user_list == true)
{
	if ($_POST['select_group'] == 'ADMIN')
	{
		$_POST['usergroup'] = $admin_write;
	}
	else
	{
		$_POST['usergroup'] = str_replace (","," ",$_POST['usergroup']);
	}
	
	$create_user = mysql_query("
	INSERT INTO  .`users` 
	VALUES (NULL , '".mysql_real_escape_string($_POST['username'])."',
	'".mysql_real_escape_string($_POST['userpass'])."',
	'".mysql_real_escape_string($_POST['usergroup'])."',
	'".mysql_real_escape_string($_POST['uservariant'])."'); ")or die ("ошибка");
	header ('Location: user_list.php');
}

//Вывод сообщения об ошибке
switch( $report ) 
{ 
    case 'ul_name_error': 
        echo "<script> alert('Ошибка! Вы не ввели имя пользователя.');</script>";
		break;
	case 'ul_nameexists_error': 
        echo "<script> alert('Ошибка! Пользователь с таким именем уже существует.');</script>";
		break;
	case 'ul_pass_error': 
        echo "<script> alert('Ошибка! Вы не ввели пароль.');</script>";
		break;
	case 'ul_variant_error': 
        echo "<script> alert('Ошибка! Вы не ввели вариант.');</script>";
		break;
	case 'ul_variantmax_error': 
        echo "<script> alert('Ошибка! Вы неправильно ввели вариант. Допустимо только число от 1 до 4.');</script>";
		break;
	case 'ul_group_error': 
        echo "<script> alert('Ошибка! Вы не ввели группу пользователя.');</script>";
		break;
	case 'ul_namefilter_error': 
        echo "<script> alert('Ошибка! Вы не заполнили фильтр для имени.');</script>";
		break;
	case 'ul_groupfilter_error': 
        echo "<script> alert('Ошибка! Вы не заполнили фильтр для группы.');</script>";
		break;
	case 'ul_csvexists_error': 
        echo "<script> alert('Ошибка! В файле CSV присутствуют пользователи, уже зарегистрированные в системе.');</script>";
		break;
	case 'ul_csvwrongdata_error': 
        echo "<script> alert('Ошибка! Данные в файле CSV не соответствуют необходимому формату.');</script>";
		break;
	case 'ul_csvinexists_notice': 
        echo "<script> alert('Предупреждение! При импорте из файла CSV были обнаружены однородные элементы и они были пропущены.');</script>";
		break;
	case 'ul_csvwrongfile_error': 
        echo "<script> alert('Ошибка! Внешний файл должен быть только в формате CSV.');</script>";
		break;
	case 'ul_csvvariant_notice': 
        echo "<script> alert('Предупреждение! При импорте из файла CSV некоторые значения варианта превысили допустимый лимит и были записаны как первый вариант.');</script>";
		break;
}

?>
</center>
</form>
</body>
</html>