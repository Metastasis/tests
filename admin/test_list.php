<!--Страница работы с полным списком тестов-->

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Список тестов</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script src="../calendar.js"></script>

</head>
<body>
<div id='pict_box'></div>
<div id='header_menu'>
	<a href='test_list.php?close=1'>Главная</a> | 
	<a href="../admin.php">Панель администрирования</a> | 
<?php
if (isset($_GET['create']) || isset($_GET['editid'])  || isset($_GET['filter']))
{
	echo "<a href='test_list.php'>Список тестов</a> | 	";
}
else
{
	echo "Список тестов | ";
}
?>
	<a href="user_list.php">Список пользователей</a> | 
	<a href="result_list.php">Список результатов</a>
</div>

<div id='content'>
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



echo "
	<center>
	<div id='center_content_block'
	style ='
		height:600px;
		'>";

//Список тестов
//Содержит применение сортировки и фильтрации к списку
//Перед выводом теста, список групп преобразуется в удобный для просмотра вид
if (!isset($_GET['create']) && !isset($_GET['editid']) && !isset($_GET['filter']) && !isset($_GET['initid']))
{
	//Если сортировка активна, выводит кнопку, позволяющую ее отключить
	//Если сортировка неактивна, выводит кнопку, позволяющую изменить сортировку первого поля
	if (isset($_GET['order']) || isset($_GET['desc']))
	{
		$stop_btn = "<th><a href='test_list.php'><img title='Отменить сортировку' src='../images/buttons/delete_btn.png'  alt='Сортировка'></a></th>";
	}
	else
	{
		$stop_btn = "<th><a href='test_list.php?desc=true'><img title='Сортировать в обратную сторону' src='../images/buttons/updown_btn.png'  alt='Сортировка'></a></th>"; 
	}
	//Позволяет менять поле, по которому происходит сортировка
	if (isset($_GET['order']))
	{
		
		$order = 'TESTNAME'; 
		if (!isset($_GET['desc']))
		{
			$link_name = '?order=name&desc=true';
		}
		else
		{
			$link_name = '?order=name';
		}
	}
	else
	{
		$link_name = '?order=name';
		$order = 'ID'; 
	}
	//Показывает на экране, происходит ли сортировка по возрастанию или убыванию
	if (isset($_GET['desc']))
	{
		$desc = 'DESC';
		$sort_pict ='up_btn.png';
		
	}
	else
	{
		$desc = '';
		$sort_pict ='down_btn.png';
	}
	echo '
	<h3>Список тестов в системе</h3>
	<div id="action_header">
		<a href="test_list.php?create=1">Добавить тест</a> | 
		<a href="test_list.php?filter=1">Фильтрация</a>
	</div>
	';
	//Применяет фильтр, если он активен
	if (isset($_SESSION['tl_filter_active']))
	{
		$filter = "WHERE `TESTNAME` LIKE '%".$_SESSION['tl_filter_by_name']."%'";
	}
	else
	{
		$filter = '';
	}
	$load_list = mysql_query("SELECT * FROM TEST_LIST ".$filter." ORDER BY ".$order." ".$desc."");
	if (mysql_num_rows($load_list)>0) 
	{
		echo '
		<style type="text/css">
		TR {
		text-align: center;
		}
		</style>

		
		<table  cellpadding="5" cellspacing="4"> 
		<tr bgcolor=#DBE7FF>
			<th><a href="test_list.php'.$link_name.'">Названиe теста</a></th>
			<th>Варианты</th>
			<th>Группы</th>
			<th colspan="4">Действия</th>	
			'.$stop_btn.'
		</tr>';
		$j=0;
		while ($test_list = mysql_fetch_assoc($load_list))
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
			<tr ".$tr_color." align=center>
				<td>".$test_list['TESTNAME']."</td>
				<td>".$test_list['TESTVARIANT']."</td>
				<td>";
			$groups = explode (",",$test_list['TESTGROUPS']);
			for ($i=0; $i < count($groups); $i++)
			{
				if ($i % 2 == 0)
				{
					echo $groups[$i];
				}
				else
				{
					echo $groups[$i]."<br>";
				}
			}
				
			echo "</td>
				
				<td><a title='Изменить вопросы и ответы' href='test_edit.php?editid=".$test_list['ID']."&variant=1'><img src='../images/buttons/edit_btn.png'  alt='Редактировать вопросы'></a></td>
				<td><a title='Изменить настройки теста' href='test_list.php?editid=".$test_list['ID']."'><img src='../images/buttons/edit_props_btn.png'  alt='Редактировать тест'></a></td>
				<td><a title='Удалить тест' href='test_list.php?deleteid=".$test_list['ID']."'><img src='../images/buttons/delete_btn.png'  alt='Удалить тест'></a></td>
				<td align=center><a href='test_list.php?initid=".$test_list['ID']."'><img src='../images/buttons/init_test.png' title='Начать тест'  alt='Начать тест'></a></td>";
				
			if ($j == 0)
			{
				echo "<td><img title='Направление сортировки' src='../images/buttons/".$sort_pict."'  alt='Сортировка'></td>";
			}
									
			echo "</tr>";
			$j++;
		}
		
		echo "</table>";
	}

}

//Вывод предупреждения перед удалением теста
if (isset($_GET['deleteid']))
{

	$load_list = mysql_query("SELECT `TESTNAME` FROM TEST_LIST WHERE ID=".$_GET['deleteid']);
	$test_list = mysql_fetch_assoc($load_list);
	echo  '
	
	<SCRIPT LANGUAGE="javascript">
	if (confirm("Удалить тест \''.$test_list['TESTNAME'].'\'?")) {
		parent.location="test_list.php?deleteidaccepted='.$_GET['deleteid'].'";
		}
	</SCRIPT>';
}
	
//Удаление записи в таблице TEST_LIST и всех вариантов для выбранного теста
if (isset($_GET['deleteidaccepted']))
{
	$load_list = mysql_query("SELECT * FROM TEST_LIST WHERE ID=".$_GET['deleteidaccepted']);
	$test_list = mysql_fetch_assoc($load_list);
	
	for ($a=1; $a<= $test_list['TESTVARIANT']; $a++)
	{
		$delete_table = mysql_query("DROP TABLE ".$test_list['TESTTABLE']."_".$a);
	}
	
	$delete_test = mysql_query("
	DELETE FROM `test_list` WHERE `test_list`.`ID` = ".$_GET['deleteidaccepted']." LIMIT 1 ");
	
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; test_list.php">';
}

//Пробный запуск теста. Генерирует последовательность вопросов и перенаправляет на странциу прохождения теста
if (isset($_GET['initid']))
{
	$load_list = mysql_query("SELECT * FROM TEST_LIST WHERE ID=".$_GET['initid']);
	$test_info = mysql_fetch_assoc ($load_list);
	if ($test_info['TESTCOUNT'] == 1)
	{
		$random_id = 'rand()';
	}
	else
	{
		$random_id = 'ID';
	}
	$load_test = mysql_query("SELECT `ID` FROM ".$test_info['TESTTABLE']."_1 ORDER BY ".$random_id." ") or die ("<h4>Ошибка генерации теста.</h4>");
	$i = 0;
	while ($quest = mysql_fetch_assoc($load_test))
	{
		$_SESSION['id_graph'][$test_info['TESTTABLE']][$i] = $quest['ID'];
		$i++;
	}		
	$_SESSION['current_test'] = $test_info['TESTTABLE']."_1";
	$_SESSION['current_test_name'] = $test_info['TESTNAME'];
	header('Location: ../test_loader.php');
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
			$report = 'tl_namefilter_error';
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
		}
	}
	else
	{
		if (isset($_SESSION['tl_filter_by_name']))
		{
			$value_filter_name = 'value="'.$_SESSION['tl_filter_by_name'].'"';
			$check_filter_name = 'checked=true';
		}
		else
		{
			$value_filter_name = '';
			$check_filter_name = '';
		}
		
	}
	
	//Форма ввода значений фильтрации
	echo "
	<div id='content'>
		<h3>Настройка фильтрации</h3>
	</div>
	
	<form name='main_form' method='post'>
	<table  cellpadding=8 cellspacing=0 >
	<tr bgcolor=E1E1EB>
		<td>Фильтрация по имени:</td>
		<td><input type='checkbox' name='use_filter_by_name' value='ON' onclick='filter_name_switch ()' ".$check_filter_name."></td>
		<td><input type='text' size=40 name='filter_by_name' ".$value_filter_name." ></td>
	</tr>
	
	
	</table>
	
	<p>
		<input type='submit' name='use_filter' value='Применить фильтр'>
		<input type='submit' name='reset_filter' value='Сбросить фильтр'>
		<input type='submit' name='cancel_action' value='Отмена'>
	</p>
	</form>";
}

//Активация фильтра на странице
if (isset($_POST['use_filter']) && $validate_filter == true)
{
	if (isset($_POST['use_filter_by_name']) && $_POST['filter_by_name'] != '')
	{
		$_SESSION['tl_filter_by_name'] = $_POST['filter_by_name'];
		$_SESSION['tl_filter_active'] = true;	
	}
		
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; test_list.php">';
}

//Деактивация фильтра
if(isset($_POST['reset_filter']))
{
	unset($_SESSION['tl_filter_by_name']);
	unset($_SESSION['tl_filter_active']);
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; test_list.php">';
}


//Организация добавления и редактирования тестов
//Обе возможности используют общий алгоритм, только при создании теста все поля в формах ввода изначально пустые, то при редактировании они уже заполнены
if ( $_GET['create']=='1' || isset($_GET['editid']))
{
	//Сообщения, выводимые в виде всплывающих подсказок
	$tl_text_action_on = "Этот тест виден пользователям, находящимся в нужной группе, и они могут его пройти";
	$tl_text_action_off = "Этот тест невидим пользователям, даже если они находятся в нужной группе";
	$tl_text_action_lock = "Этот тест виден пользователям, но запускать его нельзя";

	$tl_text_groups = "Перечисленные группы пользователей смогут видеть и проходить данный тест";

	$tl_text_date_on = "Выберите указатель и дату в календаре, чтобы она учитывалась при запуске теста";
	$tl_text_date_off = "Выберите указатель, если при прохождении теста не должна учитываться дата";

	$tl_text_pass_on = "Выберите указатель и введите пароль, чтобы он запрашивался перед каждым запуском теста";
	$tl_text_pass_off = "Выберите указатель, если тест должен запускаться без пароля";

	$tl_text_count = "Введите число попыток, максимально доступных для одного пользователя. Если поставлен параметр &quot;Состояние:закрыт&quot;, то число попыток будет равно нулю";

	$tl_text_ball = "Выберите процент, требуемый для получения соответствующей оценки за прохождение";

	$tl_text_variant = "Выберите количество вариантов для данного теста";

	//Проверка правильности заполнения полей и существует ли уже в системе тест с таким названием
	if (isset($_POST['create_test']) || isset($_POST['accept_edit']))
	{
		$validate_test = true;
		$testnames = mysql_query ("SELECT `ID` FROM TEST_LIST WHERE `TESTNAME` LIKE '".$_POST['test_name']."'"); 
		//print_r($_POST);
		if ($_POST['test_name'] == '')
		{
			$validate_test = false;
			$report='tl_name_error';
		}
		elseif (empty($_POST['test_groups']))
		{
			$validate_test = false;
			$report='tl_groups_error';
		}
		elseif ($_POST['select_date'] == 'ON' && $_POST['test_date'] == '')
		{
			$validate_test = false;
			$report='tl_date_error';
		}
		elseif ($_POST['select_pass'] == 'ON' && !isset($_POST['test_pass'])  )
		{
			
			$validate_test = false;
			$report='tl_pass_error';
		}
		elseif ( preg_match ("/^[0-9]/",$_POST['ball5']) == false ||
		         preg_match ("/^[0-9]/",$_POST['ball4']) == false ||
				 preg_match ("/^[0-9]/",$_POST['ball3']) == false)
		{
			$validate_test = false;
			$report='tl_ball_error';
		}
		elseif ( $_POST['ball5'] <= $_POST['ball4'] || $_POST['ball4'] <= $_POST['3'] || $_POST['ball5'] <= $_POST['ball3'])
		{
			$validate_test = false;
			$report='tl_ballsequence_error';
		}
		
		elseif (preg_match ("/^[0-9]/",$_POST['test_count']) == false)
		{
			$validate_test = false;
			$report='tl_count_error';
		}
		elseif ($_POST['test_count'] > 9)
		{
			$validate_test = false;
			$report='tl_countnum_error';
		}
		elseif (mysql_num_rows ($testnames) > 0)
		{
			if (isset($_GET['editid']))
			{
				
				$str = 0;
				while ($ids = mysql_fetch_assoc($testnames))
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
				$validate_test = false;
				$report = 'tl_nameexists_error';
			}
		}
	}
	//Предварительная обратка в случае, если тест редактируется
	//Выполняет заполнение полей и чтение значений поля TESTMODE, которое означает выполнение некоторых функций, таких как проверка даты или пароля перед запуском теста
	if (isset($_GET['editid']))
	{
	
		$load_test_props = mysql_query("SELECT * FROM TEST_LIST WHERE ID=".$_GET['editid']);
		$test_prop = mysql_fetch_assoc($load_test_props);
		
		if ($test_prop['TESTDATE'] == '0000-00-00')
		{
			$test_prop['TESTDATE'] = '';
		}
		else
		{
			$test_prop['TESTDATE'] = convert_data_for_user ($test_prop['TESTDATE']);
		}
		
		$test_mode = explode(",",$test_prop['TESTMODE']);
		
		$value_test_name = $value_test_groups = $value_test_date = $value_test_pass = $value_test_count = $off_test_date = $off_test_pass = '';
		$active_on = $active_off = $active_lock = $date_disabled = $pass_disabled = '';
		
		$date_placeholder = 'placeholder="Кликните для ввода" ';
		$pass_placeholder = 'placeholder="Введите пароль"';
		
		switch ($test_mode[0])
		{
			case 'ACTIVE_ON': $active_on = 'checked=true'; break;
			case 'ACTIVE_OFF':  $active_off = 'checked=true'; break;
			case 'ACTIVE_LOCK': $active_lock = 'checked=true'; break;
		}
		switch ($test_mode[1])
		{
			case 'DATE_OFF': $off_test_date = 'checked=true'; $date_disabled='disabled=true'; $date_placeholder=''; break;
			case 'DATE_ON': $on_test_date = 'checked=true';  break;
		}
		switch ($test_mode[2])
		{
			case 'PASS_ON': $on_test_pass = 'checked=true'; break;
			case 'PASS_OFF': $off_test_pass = 'checked=true'; $pass_disabled='disabled=true'; $pass_placeholder=''; break;
		}	
		
		$value_test_name = $test_prop['TESTNAME'];
		$value_test_groups = "value='".$test_prop['TESTGROUPS']."'";
		$value_test_date = "value='".$test_prop['TESTDATE']."'"; //"value='".$_test_prop['']."'"
		$value_test_pass = "value='".$test_prop['TESTPASS']."'";
		$value_test_count = "value='".$test_prop['TESTCOUNT']."'";
		
		$value_ball5 = "value='".$test_prop['BALL5']."'";
		$value_ball4 = "value='".$test_prop['BALL4']."'";
		$value_ball3 = "value='".$test_prop['BALL3']."'";
	}
	//Заполнение полей в случае, если тест создается
	elseif (isset($_GET['create']) && !isset($_POST['create_test']))
	{
		$value_test_name = $value_test_groups = $value_test_date = $value_test_pass =
		$value_test_count = $off_test_date = $off_test_pass = $date_disabled = $pass_disabled = '';
		$on_test_date = $on_test_pass = 'checked=true';
		$value_ball5 = 'value=90';
		$value_ball4 = 'value=70';
		$value_ball3 = 'value=50';
	}
	
	//Заполнение форм ввода текста в том случае, если их содержимое не прошло проверку на правильность внесенных значений
	
	if (isset ($validate_test) && $validate_test == false)
	{
		$value_test_name = $_POST['test_name'];
		$value_test_groups = "value='".$_POST['test_groups']."'";
		$value_test_count = "value='".$_POST['test_count']."'";
		$value_ball5 = "value='".$_POST['ball5']."'";
		$value_ball4 = "value='".$_POST['ball4']."'";
		$value_ball3 = "value='".$_POST['ball3']."'";
		if ($_POST['select_date'] == 'ON')
		{
			$value_test_date = "value='".$_POST['test_date']."'";
			$on_test_date = 'checked=true';
			$off_test_date = '';
			$date_disabled ='';
			
		}
		elseif ($_POST['select_date'] == 'OFF')
		{
			$off_test_date ='checked=true';
			$on_test_date = $date_placeholder = '';
		}
		if ($_POST['select_pass'] == 'ON')
		{
			$value_test_pass = "value='".$_POST['test_pass']."'";
			$on_test_pass = 'checked=true';
			$off_test_pass = '';
			$pass_disabled = '';
		}
		elseif ($_POST['select_pass'] == 'OFF')
		{
			$off_test_pass = 'checked=true';
			$on_test_pass = $pass_placeholder = '';
		}
	}
	
	
	//Вывод внешнего вида страницы. Некоторые элементы зависят от исходных данных.
	//Если происходит создание нового теста, то все поля пустые
	//Если происходит редактирование или была неуспешно проведена валидация, то поля буду автоматически заполнены
	echo '<form name="main_form" method="post">';
	if (isset($_GET['editid']))
	{
		echo '<h3 align="center">Редактирование настроек теста</h3>';
	}
	else
	{
		
		echo '<h3 align="center">Создание нового теста</h3>';
	}
	echo 
	'
	<script>
	function date_switch ()
	{
		document.all["test_date"].disabled=document.all["off_date"].checked;
	}
	function pass_switch ()
	{
		document.all["test_pass"].disabled=document.all["off_pass"].checked;
	}
	function count_switch ()
	{
		document.all["test_count"].disabled=document.all["off_count"].checked;
	}
	</script>	
	
	<table  cellpadding="9" cellspacing="0" > 
	<tr bgcolor=#E1E1EB>
		<td>Состояние:</td>
		<td> <span title="'.$tl_text_action_on.'"><input type=radio name=test_active value="ON" '.$active_on.'>Доступен</span>
		<span title="'.$tl_text_action_off.'"><input type=radio name=test_active value="OFF" '.$active_off.'>Невидим</span>
		<span title="'.$tl_text_action_lock.'"><input type=radio name=test_active value="LOCK" '.$active_lock.'>Закрыт</span></td>
	</tr>
	<tr>
		<td>Название теста:</td>
		<td><textarea rows="2" cols="49" name="test_name" placeholder="Введите название теста">'.$value_test_name.'</textarea></td>
	</tr>
	<tr bgcolor=#E1E1EB>
		<td>Группы:</td>
		<td title="'.$tl_text_groups.'"><input type="text" name="test_groups" size="50" maxlength="50" placeholder="Введите список групп через запятую" '.$value_test_groups.'></td>
	</tr>
	<tr>
		<td>Дата:</td>
		<td colspan=3><span title="'.$tl_text_date_on.'"><input type=radio name=select_date value="ON" id="on_date" onclick="date_switch ()" '.$on_test_date.'><input type=text name="test_date" size=20 readonly="on" '.$date_placeholder.' '.$date_disabled.' '.$value_test_date.' onfocus="this.select();lcs(this)"	onclick="event.cancelBubble=true;this.select();lcs(this)"></span>
		<span colspan=2 title="'.$tl_text_date_off.'"><input type=radio name=select_date value="OFF" id="off_date" onclick="date_switch ()" '.$off_test_date.'>Не проверяется</span></td>
	</tr>
	
	<tr bgcolor=#E1E1EB>
		<td>Пароль:</td>
		<td><span title="'.$tl_text_pass_on.'"><input type=radio name=select_pass value="ON" onclick="pass_switch ()" '.$on_test_pass.'><input type="text" maxlength="20" name="test_pass" size="20" '.$pass_placeholder.' '.$pass_disabled.' '.$value_test_pass.'></span>
		<span colspan="2" title="'.$tl_text_pass_off.'"><input type=radio name=select_pass value="OFF" id="off_pass" onclick="pass_switch ()" '.$off_test_pass.'>Не требуется</span></td>		
	</tr>


	<tr>
		<td>Оценки:</td>
		<td><span title="'.$tl_text_ball.'">5:<input type="text" name="ball5" size="3" maxlength="3" '.$value_ball5.'>
		4:<input type="text" name="ball4" size="3" maxlength="2" '.$value_ball4.'>
		3:<input type="text" name="ball3" size="3" maxlength="2" '.$value_ball3.'></span>
		<span  title="'.$tl_text_count.'" colspan=2>Попытки:<input type="text" maxlength="20" name="test_count" size="3" '.$value_test_count.'></span></td>
	</tr>';
	
	if (isset($_GET['editid']))
	{
		echo '
		</table>
		<p>
		<input type="submit" name="accept_edit" value=" Применить изменения ">
		<input type="submit" name="cancel_action" value=" Отмена "></p>';
		
	}
	else
	{
		echo '
		<tr bgcolor=#E1E1EB align=center>
			
			<td title="'.$tl_text_variant.'">Варианты:</td>
			<td>
			<span title="'.$tl_text_variant.'">1:<input type="radio" name="test_variant" value="1" checked=true>
				2:<input type="radio" name="test_variant" value="2">
				3:<input type="radio" name="test_variant" value="3">
				4:<input type="radio" name="test_variant" value="4"></span></td>
		</tr>
		
		</table>
		<p>
		<input type="submit" name="create_test" value=" Создать новый тест ">
		<input type="submit" name="cancel_action" value=" Отмена "></p>
		';
	}
	echo '</form>';
}

//Добавление нового теста в таблицу
//Предварительно проводит некоторые операции над данными
//Если поле даты пустое, то заносит обнуленное значение в таблицу
//Собирает разрозненные переключатели в единый параметр TESTMODE
if (isset($_POST['create_test']) && isset($validate_test) && $validate_test == true )
{
	$load_test_props = mysql_query ("SHOW TABLE STATUS LIKE 'test_list'");
	$test_props = mysql_fetch_assoc ($load_test_props);
	$test_table = 'test'.$test_props['Auto_increment'];
	
	$write_test_mode = test_mode_encoder ($_POST);
	
	if ($_POST['test_date'] == '')
	{
		$_POST['test_date'] = '0000-00-00';
	}
	$_POST['test_date'] = convert_data_for_sql($_POST['test_date']);
	
	$add_to_test_list = mysql_query ("INSERT INTO  `test_list` 
	VALUES (
	NULL ,
	'".$_POST['test_name']."', 
	'".$test_table."', 
	'".$_POST['test_pass']."', 
	'".$_POST['ball5']."', '".$_POST['ball4']."', '".$_POST['ball3']."', 
	'".$_POST['test_groups']."', 
	'".$write_test_mode."', 
	'".$_POST['test_date']."', 
	'".$_POST['test_variant']."', 
	'".$_POST['test_count']."'
	);") or die ('<h3>капец</h3>');
	
	for ($i=1; $i<=$_POST['test_variant']; $i++)
	{
		$tl_new_test = mysql_query ("
		CREATE TABLE   `".$test_table."_".$i."` (
		`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`QUESTION` VARCHAR( 255 ) NOT NULL ,
		`ANSWERS` TEXT NOT NULL ,
		`SELECTTYPE` VARCHAR( 15 ) NOT NULL 
		) ENGINE = MYISAM ");
	}
	
	echo '	<META HTTP-EQUIV=Refresh CONTENT="0; test_list.php">';
}

//Применение изменений в таблице при редактировании теста
//Проводить аналогичные операции, что и при создании, только не генерирует имя таблицы
if (isset($_POST['accept_edit']) && isset($validate_test) && $validate_test == true)
{
	$new_test_mode = test_mode_encoder($_POST);
	if ($_POST['test_date'] == '')
	{
		$_POST['test_date'] = '0000-00-00';
	}
	$_POST['test_date'] = convert_data_for_sql($_POST['test_date']);
	
	$edit_test_props = mysql_query ("

	UPDATE  `test_list` SET 
	    `TESTNAME` = '".$_POST['test_name']."',
		`TESTPASS` = '".$_POST['test_pass']."',
		`BALL5` = '".$_POST['ball5']."',
		`BALL4` = '".$_POST['ball4']."',
		`BALL3` = '".$_POST['ball3']."',
		`TESTGROUPS` = '".$_POST['test_groups']."',
		`TESTMODE` = '".$new_test_mode."',
		`TESTDATE` = '".$_POST['test_date']."',
		`TESTCOUNT` = '".$_POST['test_count']."' WHERE `test_list`.`ID` =".$_GET['editid']) or die ('<h4>полный привет</h4>');

	echo '	<META HTTP-EQUIV=Refresh CONTENT="0; test_list.php">';
}

//Общай кнопка отмены текущего действия
if (isset($_POST['cancel_action']))
{
	echo '	<META HTTP-EQUIV=Refresh CONTENT="0; test_list.php">';
}

echo "
</center>
</div>";

?>




<?php
//Сообщения об ошибке
switch( $report ) 
{ 
    case 'tl_name_error': 
        echo "<script> alert('Ошибка! Вы не ввели название теста.');</script>";
		break;
	case 'tl_nameexists_error': 
        echo "<script> alert('Ошибка! Тест с таким именем уже существует.');</script>";
		break;
	case 'tl_groups_error': 
        echo "<script> alert('Ошибка! Вы не ввели список групп.');</script>";
		break;
	case 'tl_date_error': 
        echo "<script> alert('Ошибка! Вы не ввели дату.');</script>";
		break;
	case 'tl_pass_error': 
        echo "<script> alert('Ошибка! Вы не ввели пароль.');</script>";
		break;
	case 'tl_ball_error': 
        echo "<script> alert('Ошибка! Вы неправильно ввели проценты для получения оценки.');</script>";
		break;
	case 'tl_ballsequence_error': 
        echo "<script> alert('Ошибка! Вы ввели проценты для получения оценки в неправильной последовательности.');</script>";
		break;
	case 'tl_count_error': 
        echo "<script> alert('Ошибка! Вы неправильно ввели количество попыток.');</script>";
		break;
	case 'tl_countnum_error': 
        echo "<script> alert('Ошибка! Вы ввели слишком большое число попыток. Максимальное число попыток: 9 штук.');</script>";
		break;
	case 'tl_namefilter_error': 
        echo "<script> alert('Ошибка! Вы не заполнили фильтр для имени.');</script>";
		break;
}


?>
<script>
//Автовыравнивание
	if (document.getElementById('center_content_block'))
	{
	document.getElementById('center_content_block').style.height = (document.documentElement.clientHeight -105)+'px'
	}
</script>
</div>
</body>
</html>