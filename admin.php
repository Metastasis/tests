<!-- Основная страница панели администрирования -->
<?php
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Панель администрирования</title>
<link href="style.css" rel="stylesheet" type="text/css" />

</head>
<body style="overflow:auto;">
<div id='pict_box'></div>
<div id='header_menu'>
	<a href='admin.php?close=1'>Главная</a> |
<?php
if (isset($_GET['spec']) || isset($_GET['disc']) || isset ($_GET['setup']))
{
	echo "<a href='admin.php'>Панель администрирования</a> | ";
}
else
{
	echo "Панель администрирования | ";
}
?>
	<a href="admin/test_list.php">Список тестов</a> | 
	<a href="admin/user_list.php">Список пользователей</a> | 
	<a href="admin/result_list.php">Список результатов</a>
</div>

<div id='content'>
<form name='mainform' method='POST'>
<center>


<?php
require_once 'connect.php';
require_once 'config.php';
require_once 'functions.php';
session_start();
if (($_SESSION['username']=='') or ($_SESSION['usergroup']<>$admin_index))
{
	if ($log_check_admin_panel == true)
	{
		write_entry_attention($_SERVER['REMOTE_ADDR']);
	}
	session_destroy();
	header('Location: index.php');
}
if(isset($_GET['close']))
{
	session_destroy();
	header('Location: index.php');
}
if (isset($_GET['monitor']))
{
	if ($_GET['monitor']=='next')
	{
		if ($_SESSION['log_max_page']==$_SESSION['log_page'])
		{
			$report = 'a_monitor_max_page_error';
		}
		else
		{
			$_SESSION['log_page']++;
			echo '<META HTTP-EQUIV=Refresh CONTENT="0; admin.php">';
		}
	}
	
	if ($_GET['monitor']=='prev')
	{
		if ($_SESSION['log_page']==1)
		{
			$report = 'a_monitor_min_page_error';
		}
		else
		{
			$_SESSION['log_page']--;
			echo '<META HTTP-EQUIV=Refresh CONTENT="0; admin.php">';
		}
	}
}


if (!isset($_GET['spec']) && !isset($_GET['disc']) && !isset($_GET['setup']))
{
	echo '
	<h3>Панель администрирования</h3>
	<div id="action_header">
		<a href="admin.php?spec=1">Список специальностей</a> | 
		<a href="admin.php?disc=1">Список дисциплин</a> |&nbsp; | <!--
		Стр.'.$_SESSION['log_page'].' | 
		<a href="admin.php?monitor=prev">Назад</a> | 
		<a href="admin.php?monitor=next">Вперед</a> |-->
		<a href="admin.php?setup=1">Файл лога</a>
	</div>
	
	<style>
	TR { text-align:left;}
	</style>';
	
	echo '<table>';
	$log_file = file ("data/useractionlog.txt");
	if ($log_file)
	{
		$log_file_r = array_reverse($log_file);
		
		if (count($log_file_r) > $log_view_limit)
		{
			$_SESSION['log_max_page']=(integer)(count($log_file_r)/$log_view_limit);
			$check_division = count($log_file_r) % $log_view_limit;
			if ($check_division != 0) 
			{
				$_SESSION['log_max_page']++;
			}
			unset ($check_division);
		}
		else
		{
			$_SESSION['log_max_page'] = 1;
		}
		//echo '<script> alert("мегачисло '.$_SESSION['log_max_page'].'");</script>';
		$limit_index=1;
		foreach ($log_file_r as $value) 
		{
			//if (($limit_index > ($_SESSION['log_page']-1)*$log_view_limit) && ($limit_index <= ($_SESSION['log_page'])*$log_view_limit))
			if ($limit_index < $log_view_limit+1)
			{
				if ($log_show_numbers == true)
				{
					echo "<tr><td><b>".$limit_index."</b>: ".$value."</td></tr>"; 
				}
				else
				{
					echo "<tr><td>".$value."</td></tr>"; 
				}			
			}
			$limit_index++;
		}
	
	}
	echo '</table>';
}

if (isset($_GET['setup']))
{
	$log_file = file ("data/useractionlog.txt");
	if ($log_file)
	{
		$log_file_r = array_reverse($log_file);
		echo '<h3>Настройка лога ошибок</h3>';
		echo 'Всего записей в файле: '.count($log_file).'<br>Максимальное число записей на странице: '.$log_view_limit.'<br>Настройка лога в файле config.php';
		
	}

}






//Работа со списком специальностей
if (isset($_GET['spec']))
{
	//Список специальностей. Выводит информацию о всех специальностях, а также ссылки на их удаление и добавление
	if (!isset($_GET['create']))
	{
		echo '
		<h3>Список специальностей</h3>	';
		$load_speciality = mysql_query("SELECT * FROM `speciality` ORDER BY ID DESC") or die ("Ошибка загрузки специальностей");
		echo "
			
		<table cellpadding=5 cellspacing=4>	
		<tr  bgcolor=#DBE7FF>
			<th>Номер</th>
			<th>Название</th>
			<th>Краткое имя</th>
			<th><a title='Добавить специальность' href='admin.php?spec=1&create=1'><img src='images/buttons/add_btn.png'  alt='Доб.'></a></th>
		</tr>
		";
		if (mysql_num_rows($load_speciality)>0) 
		{
			$j=0;
			while ($spec_list = mysql_fetch_assoc($load_speciality))
			{
				if ( ($j % 2) == 0)
				{
					$tr_color = "bgcolor='#F5F5FF'";					
				}
				else
				{
					$tr_color = "bgcolor='#E1E1EB'";
				}
				$j++;
				echo "
				<tr ".$tr_color.">
					<td>".$spec_list['ID']."</td>
					<td>".$spec_list['NAME']."</td>
					<td>".$spec_list['SHORT']."</td>
					<td><a title='Удалить специальность' href='admin.php?spec=1&deleteid=".$spec_list['ID']."'><img src='images/buttons/delete_btn.png'  alt='Удал.'></a></td>
				</tr>
				
				";
			}
		}
		echo "</table>";
	}	
	else
	{
		$value_spec_name = $value_spec_num = $value_spec_short = '';
		//Проверка заполнения полей при добавлении специальности
		if (isset($_POST['accept_add']))
		{
			$validate_add_spec = true;
			$load_number = mysql_query("SELECT `ID` FROM `speciality` WHERE ID=".mysql_real_escape_string($_POST['spec_num']);
			$load_short = mysql_query("SELECT `ID` FROM `speciality` WHERE `SHORT` LIKE '".mysql_real_escape_string($_POST['spec_short'])."'");
			
			if ($_POST['spec_name']=='')
			{
				$validate_add_spec = false;
				$report = 'a_spec_name_error';
			}
			elseif ($_POST['spec_num'] == '' || preg_match ("/^[0-9]/",$_POST['spec_num']) == false)
			{
				$validate_add_spec = false;
				$report = 'a_spec_number_error';
			}
			elseif (mysql_num_rows($load_number)>0)
			{
				$validate_add_spec = false;
				$report = 'a_spec_numberexists_error';
			}
			
			elseif ($_POST['spec_short']=='')
			{
				$validate_add_spec = false;
				$report = 'a_spec_short_error';
			}
			elseif (mysql_num_rows($load_short)>0)
			{
				$validate_add_spec = false;
				$report = 'a_spec_shortexists_error';
			}
			
			//Занесение данных в таблицу базы данных или вывод содержимого полей на страницу, если они были неправильно заполнены
			if ($validate_add_spec == true)
			{
				$insert_spec = mysql_query ("INSERT INTO  .`speciality` VALUES (
				'".mysql_real_escape_string($_POST['spec_num'])."',
				'".mysql_real_escape_string($_POST['spec_name'])."',
				'".mysql_real_escape_string($_POST['spec_short'])."'
				);") or die ("ошибка фатальная");
				echo '	<META HTTP-EQUIV=Refresh CONTENT="0; admin.php?spec=1">';			
			}
			else
			{
				$value_spec_name = "value='".htmlspecialchars($_POST['spec_name'])."'";
				$value_spec_num = "value='".htmlspecialchars($_POST['spec_num'])."'";
				$value_spec_short = "value='".htmlspecialchars($_POST['spec_short'])."'";
			}
		}
		//Форма добавления специальности.
		//Если проверка ввода полей была проведена и ее результат был отрицательным, то выводит поля на экран в соответствующие формы
		echo '
		<h3>Добавление специальности</h3>
		
		<style>
		TD {
		text-align:left;
		}
		</style>
		
		<table cellpadding=9 cellspacing=0>
		<tr  bgcolor="#E1E1EB">
			<td>Название специальности:</td>
			<td colspan=3><input type=text name="spec_name" placeholder="Введите полное название специальности" '.$value_spec_name.' size=80></td>
		</tr>
		<tr>
			<td>Номер специальности:</td>
			<td><input type=text name="spec_num" placeholder="Введите номер" '.$value_spec_num.'></td>
			<td>Краткое название:</td>
			<td><input type=text name="spec_short" placeholder="Введите краткое название" size=30 '.$value_spec_short.'></td>
		</tr>
		</table>
		<p>
			<input type="submit" name="accept_add" value="Добавить специальность">
			<input type="submit" name="cancel_action_spec" value="Отмена">
		
		';
		
	}
	
	//Удаление специальности. Выводит сообщение, в котором можно нажать "Да" или "Нет".
	if (isset($_GET['deleteid']))
	{
		echo  '
		
		<SCRIPT LANGUAGE="javascript">
		if (confirm("Удалить специальность?")) {
			parent.location="admin.php?spec=1&deleteidaccepted='.$_GET['deleteid'].'";
			}
		</SCRIPT>';
	}
	//Удаление специальности. Запускается, если нажата кнопка "Да" в сплывающем окне
	if (isset($_GET['deleteidaccepted']))
	{
		$delete_spec = mysql_query ("DELETE FROM `speciality` WHERE `ID`=".mysql_real_escape_string($_GET['deleteidaccepted'])) or die ("Ошибка");
		header('Location:admin.php?spec=1');
	}
	

}
	
//Работа со списком дисциплин (алгоритм аналогичен тому, который представлен для списка специальностей)
if (isset($_GET['disc']))
{
	//Список дисциплин
	if (!isset($_GET['create']))
	{
		
		echo '
		<h3>Список дисциплин</h3>	';
		$load_discipline = mysql_query("SELECT * FROM `discipline` ORDER BY NAME DESC") or die ("Ошибка загрузки дисциплин");
		
		
		echo "
		
		<table cellpadding=5 cellspacing=4>	
		<tr  bgcolor=#DBE7FF>
			<th>Название</th>
			<th>Краткое имя</th>
			<th><a title='Добавить дисциплину' href='admin.php?disc=1&create=1'><img src='images/buttons/add_btn.png'  alt='Доб.'></a></th>
		</tr>
		";
		if (mysql_num_rows($load_discipline)>0) 
		{
			$j=0;
			while ($disc_list = mysql_fetch_assoc($load_discipline))
			{
				if ( ($j % 2) == 0)
				{
					$tr_color = "bgcolor='#F5F5FF'"; //BBCCDD // E1E1EB
					
				}
				else
				{
					$tr_color = "bgcolor='#E1E1EB'"; //AABBCC // F5F5FF
				}
				$j++;
				echo "
				<tr ".$tr_color.">
					<td>".$disc_list['NAME']."</td>
					<td>".$disc_list['SHORT']."</td>
					<td><a title='Удалить дисциплину' href='admin.php?disc=1&deleteid=".$disc_list['ID']."'><img src='images/buttons/delete_btn.png'  alt='Удал.'></a></td>
				</tr>
				
				";
			}
		echo "</table>";
		}
	}
	//Формы добавления новой дисциплиныы
	else
	{
		$value_disc_name = $value_disc_num = $value_disc_short = '';
		//Проверка вводимых полей
		if (isset($_POST['accept_add']))
		{
			$validate_add_disc = true;
			$load_short = mysql_query("SELECT `ID` FROM `discipline` WHERE `SHORT` LIKE '".mysql_real_escape_string($_POST['disc_short'])."'");
			
			if ($_POST['disc_name']=='')
			{
				$validate_add_disc = false;
				$report = 'a_disc_name_error';
			}
			elseif ($_POST['disc_short']=='')
			{
				$validate_add_disc = false;
				$report = 'a_disc_short_error';
			}
			elseif (mysql_num_rows($load_short)>0)
			{
				$validate_add_disc = false;
				$report = 'a_disc_shortexists_error';
			}
			
			//Добавление записи, если проверка была выполнена правильно, и вывод промежуточных данных на форму, если проверка не была успешной
			if ($validate_add_disc == true)
			{
				$insert_disc = mysql_query ("INSERT INTO  .`discipline` VALUES (
				NULL,
				'".mysql_real_escape_string($_POST['disc_name'])."',
				'".mysql_real_escape_string($_POST['disc_short'])."'
				);") or die ("ошибка фатальная");
				echo '	<META HTTP-EQUIV=Refresh CONTENT="0; admin.php?disc=1">';			
			}
			else
			{
				$value_disc_name = "value='".htmlspecialchars($_POST['disc_name'])."'";
				$value_disc_num = "value='".htmlspecialchars($_POST['disc_num'])."'";
				$value_disc_short = "value='".htmlspecialchars($_POST['disc_short'])."'";
			}
		}
		//Вывод формы добавления дисциплины
		echo '
		<h3>Добавление дисциплины</h3>
		
		<style>
		TD {
		text-align:left;
		}
		</style>
		
		<table cellpadding=9 cellspacing=0>
		<tr  bgcolor="#E1E1EB">
			<td>Название дисциплины:</td>
			<td><input type=text name="disc_name" placeholder="Введите полное название дисциплины" '.$value_disc_name.' size=80></td>
		</tr>
		<tr>
			<td>Краткое название:</td>
			<td><input type=text name="disc_short" placeholder="Введите краткое название" size=30 '.$value_disc_short.'></td>
		</tr>
		</table>
		<p>
			<input type="submit" name="accept_add" value="Добавить дисциплину">
			<input type="submit" name="cancel_action_disc" value="Отмена">
		
		';
		
	}
	
	//Вывод предупреждения перед удалением
	if (isset($_GET['deleteid']))
	{
		echo  '
		
		<SCRIPT LANGUAGE="javascript">
		if (confirm("Удалить дисциплину?")) {
			parent.location="admin.php?disc=1&deleteidaccepted='.$_GET['deleteid'].'";
			}
		</SCRIPT>';
	}
	//Подтверждение удаления
	if (isset($_GET['deleteidaccepted']))
	{
		$delete_disc = mysql_query ("DELETE FROM `discipline` WHERE `ID`=".mysql_real_escape_string($_GET['deleteidaccepted'])) or die ("Ошибка");
		header('Location:admin.php?disc=1');
	}

}	

//Кнопки отмены при работе со специальностями и дисциплинами
if (isset($_POST['cancel_action_spec']))
{
	echo '	<META HTTP-EQUIV=Refresh CONTENT="0; admin.php?spec=1">';
}	

if (isset($_POST['cancel_action_disc']))
{
	echo '	<META HTTP-EQUIV=Refresh CONTENT="0; admin.php?disc=1">';
}	
		
?>
</center>
</form>
</div>
<?php

//Сообщения об ошибке
switch( $report ) 
{ 
    case 'a_spec_name_error': 
        echo "<script> alert('Ошибка! Вы не ввели полное название специальности.');</script>";
		break;
	case 'a_spec_number_error': 
        echo "<script> alert('Ошибка! Вы неправильно ввели номер специальности.');</script>";
		break;
	case 'a_spec_numberexists_error': 
        echo "<script> alert('Ошибка! Вы ввели уже существующий номер специальности.');</script>";
		break;
	case 'a_spec_short_error': 
        echo "<script> alert('Ошибка! Вы не ввели краткое название специальности.');</script>";
		break;
	case 'a_spec_shortexists_error': 
        echo "<script> alert('Ошибка! Вы ввели уже существующее краткое название специальности.');</script>";
		break;
	case 'a_disc_name_error': 
        echo "<script> alert('Ошибка! Вы не ввели полное название дисциплины.');</script>";
		break;
	case 'a_disc_short_error': 
        echo "<script> alert('Ошибка! Вы не ввели краткое название дисциплины.');</script>";
		break;
	case 'a_disc_shortexists_error': 
        echo "<script> alert('Ошибка! Вы ввели уже существующее краткое название дисциплины.');</script>";
		break;
	case 'a_monitor_min_page_error':
		echo "<script>alert('Ошибка! Текущая страница является первой.');</script>";
		break;
	case 'a_monitor_max_page_error':
		echo "<script>alert('Ошибка! Текущая страница является последней.');</script>";
		break;
}


?>

</body>
</html>