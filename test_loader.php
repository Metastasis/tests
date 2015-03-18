<!--Страница прохождения выбранного теста-->
<?php
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<html  style='overflow:auto;'>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body style='overflow-x:hidden;'>

<?php
  
require_once 'connect.php';
require_once 'config.php';
require_once 'functions.php';
session_start();

if(isset($_GET['close']))
{
	
	session_destroy();
	header('Location: index.php');
}

if ($_SESSION['username']=='')
{
	session_destroy();
	header('Location: index.php');
}	
if (!isset($_SESSION['id_graph']))
{
	header('Location: test_lister.php');
}	

//Вывод разных элементов интерфейса в зависимости от группы пользователя
//Если идет пробный запуск теста из панели администрирования, то выводятся ссылки на другие страницы панели админстрирования
//Если идет обычный запуск, то выводятся ссылки, доступные только пользователю, и видна информация о пользователе
if ($_SESSION['usergroup'] == $admin_index)
{
	echo "
	<div id='header_menu'>
		<a href='admin/result_loader.php?close=1'>Главная</a> | 
		<a href='admin.php'>Панель администрирования</a> | 
		<a href='admin/test_list.php'>Список тестов</a> | 
		<a href='admin/user_list.php'>Список пользователей</a> | 
		<a href='admin/result_list.php'>Список результатов</a>
	</div>";

}
else
{
	echo  "
	<div id='header_menu'>
		<a href='test_loader.php?close=1'>Главная</a> | 
		<a href='test_lister.php'>Список тестов</a>
	</div>
	";
	
	echo '
	<div id="user_name">Вы вошли как "'.$_SESSION['username'].'" из группы "'.$_SESSION['usergroup'].'"</div>';
}

$view_testname = prepear_readed_answer($_SESSION['current_test_name']);
echo "<title>Прохождение теста &quot;".$view_testname."&quot;</title>";

echo '
<div id="content">
<h3>Прохождение теста &quot;'.$view_testname.'&quot;</h3>
</div>';

//Сохранение названия таблицы в специальную переменную, которая потом будет использоваться при ссылке на порядковые номера
$test_graph_id = substr($_SESSION['current_test'], 0, -2);

//Предварительная проверка теста на то, что все вопросы отвечены
//Если хоть один вопрос не был отвечен, программа прекращает обработку теста и выводит сообщение об ошибке
if(isset($_POST['submit']))
{	
	$i=0;
	$validate_test = true;
	while ($i <= count($_SESSION['id_graph'][$test_graph_id]))
	{
		$load_switcher = mysql_query("SELECT * FROM ".$_SESSION['current_test']." WHERE `ID`=".$_SESSION['id_graph'][$test_graph_id][$i]);
		$switcher = mysql_fetch_assoc($load_switcher);
		$answers = explode ("",$switcher['ANSWERS']); //
		$ans_limit = count ($answers);
		if ($switcher['SELECTTYPE'] == 'radio')
		{
			if (!isset($_POST['Q'.$i]))
			{		
				$validate_test = false;
			}
		}
		elseif ($switcher['SELECTTYPE'] == 'check')
		{
			$count_answers = 0;
			for ($j=0;$j<$ans_limit;$j++)
			{	
				if (isset($_POST['Q'.$i.'_'.$j]))
				{
					$count_answers++;
				}
			}

			if ($count_answers == 0)
			{
				$validate_test = false;
			}
		}	
		$i++;
	}
	if ($validate_test == false)
	{
		echo "
		<script>
		alert('Вы ответили не на все вопросы');
		</script>
		";
		$_POST['active'] = true;
		unset ($_POST['submit']);
	}	

}


//Организация вывода вопросов на экран
//В зависимости от типа вопроса, выводятся различные интерфейсные элементы
//Если вопрос имеет тип "radio", то можно выбрать только один ответ
//Если вопрос имеет тип "check", то можно выбрать все ответы
if (!isset($_POST['submit']))
{
	echo "<ol><form name='test' method='POST'>";
	$i=0;
	while ($i <= count($_SESSION['id_graph'][$test_graph_id]))
	{
		$load_test = mysql_query("SELECT * FROM ".$_SESSION['current_test']." WHERE `ID`=".$_SESSION['id_graph'][$test_graph_id][$i]);
		$quest = mysql_fetch_assoc($load_test);
		$answers = explode ("",$quest['ANSWERS']); //
		
		if ($quest['SELECTTYPE']=='radio') 
		{
			echo " 
				<li>".$quest['QUESTION']."</li>";
			$ans_limit = count($answers);
				
			for ($j=0;$j<$ans_limit;$j++)
			{
				if (isset($_POST['active']) && $_POST['Q'.$i] == 'A'.$j)
				{
					$check = "checked=true";
				}
				else
				{
					$check = "";
				}
				$answers[$j] = prepear_readed_answer($answers[$j]);
				if ($answers[$j][0]=="*")
				{
					$answers[$j] = substr($answers[$j],1);
				}
				echo " <input type=radio name=Q".$i." value=A".$j." ".$check.">".$answers[$j]."<br>";
			}
		}
		
		if ($quest['SELECTTYPE']=='check') 
		{
			echo " 
			<li>".$quest['QUESTION']."</li>";
			$ans_limit = count($answers);
			
			for ($j=0;$j<$ans_limit;$j++)
			{	
				if (isset($_POST['active']) && isset($_POST['Q'.$i.'_'.$j]))
				{
					$check = "checked=true";
				}
				else
				{
					$check = "";
				}
				$answers[$j] = prepear_readed_answer($answers[$j]);
				if ($answers[$j][0]=="*")
				{
					$answers[$j] = substr($answers[$j],1);
				}
				echo "<input type=checkbox name=Q".$i."_".$j." value=A1 ".$check.">". $answers[$j]."<br>";
			}
		}	
		echo "<br>";
		$i++;
	}
	
	echo '</ol>
	<center><input name="submit" type="submit" value="Закончить тест" />
	</center>
	</form>';
}
else
//Подсчет результатов прохождения теста и сохранение текста вопросов и отвеченных ответов
//Метод записи различается в зависимости от типа вопроса
//Если вопрос имеет тип 'radio', то может быть записан только один ответ
//Если вопрос имеет тип 'check', то могут быть записаны несколько ответов на вопрос
//В ходе записи также проверяется, правильно ли отвечен вопрос. Значения заносятся в специальные переменные, которые объявляются в самом начале работы алгоритма
{
	$test_log = '<ul>';
	
	$i=0;
	$max_answers = 0;
	$right_answers = 0;
	$fail_answers = 0;
	$unsetted_answer = false;
	while ($i <= count($_SESSION['id_graph'][$test_graph_id]))
	{
		$load_test = mysql_query("SELECT * FROM ".$_SESSION['current_test']." WHERE `ID`=".$_SESSION['id_graph'][$test_graph_id][$i]);
		$quest = mysql_fetch_assoc($load_test);
		
		$answers = explode ("",$quest['ANSWERS']);
		$max_answers++;
		$i_view = $i+1;
		$answer_block = '';
		
		if ($quest['SELECTTYPE']=='radio') 
		{				
			$ans_limit = count($answers);
			$q_state="";	
			for ($j=0;$j<$ans_limit;$j++)
			{
				if ($_POST['Q'.$i]=="A".$j)
				{
					if ($answers[$j][0]=="*")
					{	
						$answers[$j] = substr($answers[$j],1);
						$answer_block .= "<p class=right_answer>";
						$q_state = "right";
					}
					else
					{
						$answer_block .= "<p class=fail_answer>";
						$q_state = "fail";
					}
					$answer_block .= $answers[$j]."</p>";
				}
				elseif (!isset($_POST['Q'.$i]))
				{
					$unsetted_answer = true;
				}
				
			}
			if ($q_state == "right")
			{
				$test_log .= "<li>&nbsp;<b>+</b>&nbsp;	";
				$right_answers++;
			}
			else
			{
				$test_log .= "<li><b>—</b>	";
				$fail_answers++;
			}
			$test_log .= $i_view.".	".$quest['QUESTION']."</li>".$answer_block;
		}
	
		if ($quest['SELECTTYPE']=='check') 
		{
			$right_answer_max=0;
			$right_answer=0;
			$fail_answer=0;
			
			$ans_limit = count($answers);
			for ($j=0;$j<$ans_limit;$j++)
			{
				if ($answers[$j][0]=="*")
				{
					$right_answer_max++;
				}
				
				if (isset($_POST['Q'.$i.'_'.$j]))
				{
					if ($answers[$j][0]=="*")
					{	
						$answers[$j] = substr($answers[$j],1);
						$answer_block .= "<p class=right_answer>";
						$right_answer++;
					}
					else
					{
						$answer_block .= "<p class=fail_answer>";
						$fail_answer++;
					}
					$answer_block .= $answers[$j]."</p>";
				}
				elseif (!isset($_POST['Q'.$i.'_'.$j]))
				{
					$unsetted_answer = true;
				}
			}	
			
			if (($right_answer_max == $right_answer) && ($fail_answer == 0))
			{
				$test_log .= "<li>&nbsp;<b>+</b>&nbsp;	";
				$right_answers++;
			}
			else 
			{
				$test_log .= "<li><b>—</b>	";
				$fail_answers++;
			}
			
			$test_log .= $i_view.".	".$quest['QUESTION']."</li>".$answer_block;
		}		
		$i++;
	}	
	
	$test_log .= "</ul>";	
		
	
	$check_date = mysql_query("SELECT NOW()");
	$mas_current_date = mysql_fetch_assoc($check_date);
	$current_date = $mas_current_date ["NOW()"];
	
	$all_answers = $fail_answers + $right_answers;
	$right_percent = ($right_answers * 100) / ($all_answers);
	
	$load_list = mysql_query("SELECT * FROM `test_list` WHERE `TESTNAME` = '".$_SESSION['current_test_name']."'") or die ("капец");
	$test_list = mysql_fetch_assoc($load_list);

	if ($right_percent > $test_list['BALL5']) { $final_ball = 5;}
	elseif ($right_percent > $test_list['BALL4']) { $final_ball = 4;}
		elseif ($right_percent > $test_list['BALL3']) { $final_ball = 3;}
			else { $final_ball = 2; }
			
	if (isset($_SESSION['id_graph']))
	{
		$write_result = mysql_query ("INSERT INTO  .`results` (`ID`, `USERNAME`, `TESTNAME`, `TESTDATE`, `RESRIGHT`, `RESALL`, `RESBALL`, `TESTLOG`)
		VALUES (NULL, '".$_SESSION['username']."', '".$_SESSION['current_test_name']."', '".$current_date."', '".$right_answers."', '".$all_answers."', '".$final_ball."', '".$test_log."');")
				
		or die ("<script>alert('Ошибка записи теста! Рекомендуется не закрывать данную страницу и обратиться к администратору, иначе результаты прохождения теста будут утеряны.')</script>");
	}
	unset ($_SESSION['id_graph'][$test_graph_id]);
	
	//Если пользователь администратор, то он перемещаемся на страницу полного списка тестов
	//Если пользователь не администратор, то он перемещается на страницу списка доступных тестов
	if ($_SESSION['usergroup'] == $admin_index)
	{
		header('Location: admin/test_list.php');
	}
	else
	{
		header('Location: test_lister.php');
	}

}

?>
 
</body>
</html>