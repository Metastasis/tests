<!--Страница со списком доступных для пользователя тестов-->
<?
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Тестирование</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style>
TD {
	text-align:center;

}


</style>
</head>
<body>
<div id='pict_box'></div>
<div id='header_menu'>
	<a href='test_lister.php?close=1'>Главная</a> | 
<?php
if (isset($_GET['testid']) || (isset($_GET['showid'])))
{
	echo "<a href='test_lister.php'>Список тестов</a>";
}	
else
{
	echo "Список тестов";
}
	
?>
</div>
<form name='choose_test' method='POST'>
<div id='content'>

<center>



<?php
require_once 'config.php';
require_once 'connect.php';
require_once 'functions.php';

session_start();
if ($_SESSION['username']=='')
{
	
	session_destroy();
	header('Location: index.php');
}
if(isset($_GET['close']))
{
	session_destroy();
	header('Location: index.php');
}

//Текущая дата
$check_date = mysql_query("SELECT NOW()");
$mas_current_date = mysql_fetch_assoc($check_date);
$current_date = substr ($mas_current_date ["NOW()"],0,10);
$current_date_for_view = convert_data_for_user ($current_date);



//Предварительная проверка параметров теста
if (isset($_GET['testid']))
{
	$load_list = mysql_query("SELECT * FROM `TEST_LIST` WHERE `ID`=".$_GET['testid']);
	$test_list = mysql_fetch_assoc ($load_list);
	
	
	$test_mode = explode(",",$test_list['TESTMODE']);
	
	if ( ($test_mode[0] != 'ACTIVE_OFF') && ($test_mode[0] != 'ACTIVE_LOCK'))
	{
		//Если дата не проверяется или является правильной, то проводит дальнейшую проверку
		if (( $test_mode[1] == 'DATE_ON'  && $test_list['TESTDATE'] == $current_date ) || $test_mode[1] == 'DATE_OFF')
		{
			//Проверяет, нужно ли вводить пароль. Если да, то выводит форму для ввода пароля.
			if ( $test_mode[2] == 'PASS_ON')
			{
				echo "<h4>Введите пароль для запуска теста</h4>
					<input type='password' name='test_pass'>
					<input type='submit' name='begin' value='Начать тест'>";
			}
			else 
			{
				$_POST['begin'] = '1';
			}
		
		}
		else
		{
			echo "
			<script>
				alert ('Ошибка! Тест нельзя запустить в этот день');
			</script>";
			unset ($_GET['testid']);
		}
	}
	else
	{
		echo "
		<script>
			alert ('Ошибка! Тест заблокирован. Можно только смотреть результаты');
		</script>";
		unset ($_GET['testid']);
	}
}

//Итоговая проверка перед запуском теста. Запускается только после предварительной проверки
//Содержит проверку на количество пройденных тестов и генерирурет порядок вопросов в тесте в случае, если тест можно пройти только 1 раз
if (isset($_POST['begin']))
{
	$load_list = mysql_query("SELECT * FROM TEST_LIST WHERE ID =".$_GET['testid']);
	$test_list = mysql_fetch_assoc($load_list);
	
	$test_mode = explode(",",$test_list['TESTMODE']);
	
	$data_accepted = 0;
	if ( $test_mode[2] == 'PASS_ON')
	{
		if ($_POST['test_pass'] == $test_list['TESTPASS'])
		{
			if (!isset($_SESSION['id_graph'][$test_list['TEST_TABLE']]))
			{
				$data_accepted = 1;
			}

		}
		else
		{
			
			echo "Пароль к тесту неверный!";
		}
	}
	
	else
	{
		$data_accepted = 1;
	}
	
	if ($data_accepted == 1)
	{
		$load_result_graph = mysql_query ("SELECT * FROM `RESULTS` WHERE `TESTNAME` LIKE '".$test_list['TESTNAME']."' AND `USERNAME` LIKE '".$_SESSION['username']."';") or die ('error');
		$r = 0;
		while ($result = mysql_fetch_assoc($load_result_graph))
		{
			$r++;
		}
		$load_test_info = mysql_query("SELECT * FROM TEST_LIST WHERE ID=".$_GET['testid']);
		$test_info = mysql_fetch_assoc ($load_test_info);
		
		if ($test_info['TESTCOUNT'] > $r)
		{
			//Выбор итогового варианта
			switch ($test_info['TESTVARIANT'])
			{
				case 1: $final_variant = 1; break;
				
				case 2:
					switch ($_SESSION['uservariant'])
					{
						case 1: $final_variant = 1; break;
						case 2: $final_variant = 2; break;
						case 3: $final_variant = 1; break;
						case 4: $final_variant = 2; break;
					}
					break;
				
				case 3:
					switch ($_SESSION['uservariant'])
					{
						case 1: $final_variant = 1; break;
						case 2: $final_variant = 2; break;
						case 3: $final_variant = 3; break;
						case 4: $final_variant = 1; break;
					}
					break;
					
				case 4: $final_variant = $_SESSION['uservariant']; break;
				
				default: die("<h4>Ошибка. Данные о варианте не обнаружены.</h4>"); break;
			}
			
			
			//Генерация последовательности вопросов. Если последовательность уже существует, то сразу перенаправляет на тест
			if (!isset($_SESSION['id_graph'][$test_info['TESTTABLE']]))
			{
				if ($test_info['TESTCOUNT'] == 1)
				{
					$random_id = 'rand()';
				}
				else
				{
					$random_id = 'ID';
				}
				$load_test = mysql_query("SELECT `ID` FROM ".$test_info['TESTTABLE']."_".$final_variant." ORDER BY ".$random_id." ") or die ("<h4>Ошибка генерации теста.</h4>");
				$i = 0;
				while ($quest = mysql_fetch_assoc($load_test))
				{
					$_SESSION['id_graph'][$test_info['TESTTABLE']][$i] = $quest['ID'];
					$i++;
				}		
				$_SESSION['current_test'] = $test_info['TESTTABLE']."_".$final_variant;
				$_SESSION['current_test_name'] = $test_info['TESTNAME'];
				header('Location: test_loader.php');
			}
			else
			{
				$_SESSION['current_test'] = $test_info['TESTTABLE']."_".$final_variant;
				$_SESSION['current_test_name'] = $test_info['TESTNAME'];
				header('Location: test_loader.php');
				
			}
		}
		else
		{
			echo "<script>alert('Превышен лимит запуска теста')</script>";
			echo '<META HTTP-EQUIV=Refresh CONTENT="0; test_lister.php">';
		}
	}
}

//Список доступных тестов
//Добавляет тест из базы данных в список  при условии, что соответствует группа пользователя и тест активен
//Содержит ссылку на просмотр результатов предыдущих прохождений выбранного теста и ссылку на запуск теста
if (!isset($_GET['testid']) && (!isset($_GET['showid'])))
{
	$load_list = mysql_query("SELECT * FROM TEST_LIST ORDER BY ID");
	if (mysql_num_rows($load_list)>0) 
	{
		echo '
		<h3>Доступные тесты на '.$current_date_for_view.'</h3>
		<table  cellpadding="5" cellspacing="4">
		<tr bgcolor="DBE7FF">
			<th>Название теста</th>
			<th>Попытки</th>
			<th colspan=3>Действия</th>
		</tr>
		';
		$j = 0;
		while ($test_list = mysql_fetch_assoc($load_list))
		{				
			if ( ($j % 2) == 0)
			{
				$tr_color = "bgcolor='#F5F5FF'"; //BBCCDD // E1E1EB
			}
			else
			{
				$tr_color = "bgcolor='#E1E1EB'"; //AABBCC // F5F5FF
			}
			
			$grp = explode (",",$test_list['TESTGROUPS']);
			unset ($group_exists);
							
			for ($i=0;$i < count($grp); $i++)
			{
				if (trim($grp[$i]) == $_SESSION['usergroup'])
				{
					$group_exists = 1;
				}
			}	
			
			$test_mode = explode(",",$test_list['TESTMODE']);
			if (($test_mode[0] == 'ACTIVE_ON' || $test_mode[0] == 'ACTIVE_LOCK') && isset($group_exists))
			{
				$load_result_graph = mysql_query ("SELECT * FROM `RESULTS` WHERE `TESTNAME` LIKE '".$test_list['TESTNAME']."' AND `USERNAME` LIKE '".$_SESSION['username']."';") or die ('error');
				$r = 0;
				while ($result = mysql_fetch_assoc($load_result_graph))
				{
					$r++;
				}
				$r_for_user = $test_list['TESTCOUNT'] - $r;
				$if_locked = "";
				if ($test_mode[0] != 'ACTIVE_LOCK')
				{
					$if_locked = "<a href='test_lister.php?testid=".$test_list['ID']."'><img src='images/buttons/init_test.png' title='Начать тест'  alt='Начать тест'></a>";
				}
				echo "
					<tr ".$tr_color.">
						<td>".$test_list['TESTNAME']."</td>
						<td>".$r_for_user." из ".$test_list['TESTCOUNT']."</td>
						<td align=center><a href='test_lister.php?showid=".$test_list['ID']."'><img src='images/buttons/show_btn.png' title='Статистика' alt='Узнать результат'></a></td>
						<td align=center>".$if_locked."</td>
					</tr>";		
				$j++;						
			}
			
		}
		echo "</table>";
	}
}

//Функция просмотра пройденных тестов
//Проверяет соответствие имени пользователя и имени теста и вносит их в список
//Список содержит информацию о количестве положительно и отрицательно отвеченных вопросов, а также суммарную оценку за тест
if (isset($_GET['showid']))
{
	$load_test_info = mysql_query("SELECT * FROM TEST_LIST WHERE ID=".$_GET['showid']);
	$test_info = mysql_fetch_assoc ($load_test_info);
	echo "<h3>Статистика прохождения теста &quot;".$test_info['TESTNAME']."&quot;</h3>";
	$load_result_graph = mysql_query ("SELECT * FROM `RESULTS` WHERE `TESTNAME` LIKE '".$test_info['TESTNAME']."' AND `USERNAME` LIKE '".$_SESSION['username']."';") or die ('капец');
	
	if (mysql_num_rows($load_result_graph)>0) 
	{
		
		echo "
		<table cellpadding=5 cellspacing=4>
		<tr bgcolor=#DBE7FF>
			<th>Дата</th>
			<th>Оценка</th>
			<th>Правильные ответы</th>
			<th>Неправильные ответы</th>
		</tr>
			";
		$j=0;
		while ($result_graph = mysql_fetch_assoc($load_result_graph))
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
			
			$resfail = $result_graph['RESALL'] - $result_graph['RESRIGHT'];
			echo "
			<tr ".$tr_color.">
				<td>".convert_data_for_user($result_graph['TESTDATE'])."</td>
				<td>".$result_graph['RESBALL']."</td>
				<td>".$result_graph['RESRIGHT']."</td>
				<td>".$resfail."</td>
				
			</tr>
			";
		}
	}
	else
	{
		echo "<h4>По данному тесту информации не обнаружено</h4>";
	}
}

echo '
<div id="user_name">Вы вошли как "'.$_SESSION['username'].'" из группы "'.$_SESSION['usergroup'].'"</div>';

?>
</center>

</center>
 </div>
 </form>
</body>
</html>