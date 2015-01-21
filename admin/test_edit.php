<!--Страница работы с содержимым теста-->

<?
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="imagetoolbar" content="no" />
<link href="../style.css" rel="stylesheet" type="text/css" />
<STYLE type="text/css">
#left_content_block
{
	float:left;
	overflow:auto;
	width:100%;
	height:780px;
}
TH
{
	border: 1px solid #556699;
	font-weight: normal;
	text-align: left;
}
TD
{
	border: 1px solid #F5F5FF;
}
 </STYLE>
<?php
require_once '../connect.php';
require_once '../functions.php';
require_once '../config.php';
session_start();

//Проверяет, был ли отправлен номер теста, который нужно редактировать. Если да, то он сохраняется
if (isset($_GET['editid']))
{
	$_SESSION['test_table'] = 'test'.$_GET['editid']."_1";
}
//Исправляет ссылку на таблицу с тестом так, чтобы редактировать только выбранный в этот раз вариант
if (isset($_GET['variant']))
{
	$new_variant = "_".$_GET['variant'];
	$_SESSION['test_table'] = substr($_SESSION['test_table'], 0, -2);
	$_SESSION['test_table'] .= $new_variant;
}
$current_variant = substr($_SESSION['test_table'], -1);
//Сброс значений проверки правильности ввода значений при добавлении вопроса
if (!isset($_POST['add_quest']) && !isset($_POST['accept_add'])  && isset($_SESSION['validate_create_part1']))
{
	unset($_SESSION['validate_create_part1']);
	unset($_SESSION['validate_create_part2']);
}

if (($_SESSION['username']=='') or ($_SESSION['usergroup']<>$admin_index))
{
	header('Location: ../index.php');
}
if (!isset($_SESSION['test_table']))
{
	header('Location: ../index.php');
}

$table_to_find = substr($_SESSION['test_table'], 0, -2);
$load_test_name = mysql_query ("SELECT * FROM `TEST_LIST` WHERE `TESTTABLE` = '".$table_to_find."'") or die ("капец");
$test_name = mysql_fetch_assoc ($load_test_name);
echo "<title>Редактирование теста &quot;".$test_name['TESTNAME']."&quot;</title>";

if(isset($_GET['close']))
{
	session_destroy();
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; ../index.php">';
}

?>
</head>
<body style='overflow:hidden; overflow-x:hidden; overflow-y:hidden;'>
<div id='pict_box'></div>
<div id='header_menu'>
	<a href='test_edit.php?close=1'>Главная</a> | 
	<a href="../admin.php">Панель администрирования</a> | 
	<a href="test_list.php">Список тестов</a> | 
	<a href="user_list.php">Список пользователей</a> | 
	<a href="result_list.php">Список результатов</a>
</div>

<div id='content'>
<?
//Показ варианта на экране, если всего их больше 1
if ($test_name['TESTVARIANT'] > 1)
{
	$view_var_num = " для варианта ".$current_variant;
}
echo "<h3>Список вопросов теста &quot;".$test_name['TESTNAME']."&quot;".$view_var_num."</h3>";
?>
	<div id='action_header'>
		<a href='test_edit.php'>Cоздать вопрос</a> | 
		<a href='test_edit.php?move_quest=1'>Переставить вопросы местами</a>
<?
//Вывод ссылок для выбора других вариантов теста
	for ($a=1; $a<=$test_name['TESTVARIANT'];$a++)
	{
		if ($current_variant != $a)
		{
			echo " | <a href='test_edit.php?variant=".$a."'>Вариант ".$a."</a>";
		}
	}
?>
	</div>
</div>
<div id='left_big_block'>
<?
//Вывод списка вопросов на экран
$load_test = mysql_query("SELECT * FROM `".$_SESSION['test_table']."` ORDER BY ID") or die ("эпик фейл");
if (mysql_num_rows($load_test)>0) 
{	
	echo "
	
	
	<form name='choose_test' method='POST'>

	<div id='left_content_block'
		height:600px;
		clear:both;'>
	<table cellpadding='5'  cellspacing='2' width='100%'>
	";
	$i=1;
	//Обработка и вывод на экран каждой записи таблицы с помощью цикла
	while ($answer = mysql_fetch_assoc($load_test))
	{
		//Выбор цвета строки таблицы
		if ( ($i % 2) == 0)
		{
			$tr_color = "bgcolor='#F5F5FF'";
		}
		else
		{
			$tr_color = "bgcolor='#E1E1EB'";
		}
		//Если вопрос редактируется или показывается, то к нему применяется выделение
		if (($_GET['showid'] == $answer['ID']) || ($_GET['editquestid'] == $answer['ID']))
		{
			$td_th_selector = 'th';
		}
		else
		{
			$td_th_selector = 'td';
		}
		echo "
		<tr ".$tr_color."  >
			<".$td_th_selector.">".$i."</".$td_th_selector.">
			<".$td_th_selector." width='100%'>".$answer['QUESTION']."</".$td_th_selector.">";
		//Если активен процесс перемещения вопросов, то выводит элементы выбора, иначе выводит обычные кнопки
		if (isset($_GET['move_quest']))
		{
			echo "
			<".$td_th_selector." align=center  ><input type='radio' name='move_first_pos' value='".$answer['ID']."'></".$td_th_selector.">
			<".$td_th_selector." align=center ><input type='radio' name='move_second_pos' value='".$answer['ID']."'></".$td_th_selector.">
			";
		}
		else
		{
			echo "
			<".$td_th_selector." ><a title='Показать ответы' href='test_edit.php?showid=".$answer['ID']."&viewid=".$i."'><img src='../images/buttons/show_btn.png' alt='Показать ответы'></a></".$td_th_selector.">
			<".$td_th_selector." ><a title='Редактировать вопрос и ответы' href='test_edit.php?editquestid=".$answer['ID']."&viewid=".$i."'><img src='../images/buttons/edit_btn.png' alt='Редактировать'></a></".$td_th_selector.">
			<".$td_th_selector." ><a title='Удалить вопрос' href='test_edit.php?deleteid=".$answer['ID']."'><img src='../images/buttons/delete_btn.png'  alt='Удалить вопрос'></a></".$td_th_selector."></".$td_th_selector.">
				";
		}
		echo "</tr>";
		$i++;
	}
	echo "</table>";
	echo "</div>";
	echo "</div>";
}
else
{
	echo "<h4 class='header_title' align='center'>В тесте еще нет ни одного вопроса</h4>";
	echo "</div>";
	echo "</div>";
}
//Правая сторона страницы
echo "<div id='right_main_block'>";

//Перемещение вопросов. Используется для того, чтобы поменять местами вопрос 1 и вопрос 2
if (isset($_GET['move_quest']))
{
	$move_error='';
	echo "<div id='right_small_block'>
		<div id='action_header'>
			<h4 class='header_title' align='center'>Перемещение вопросов</h4>
		</div>";
	if (isset($_POST['cancel_moving_quest']))
	{
		echo '<META HTTP-EQUIV=Refresh CONTENT="0; test_edit.php">';
	}
	//Кнопка применения изменений. Если значения введены правильно, то меняет идентификаторы записей в базе. Иначе выводит сообщение об ошибке
	if (isset($_POST['move_quest']))
	{
		if (isset($_POST['move_first_pos']) && isset($_POST['move_second_pos']) && ($_POST['move_first_pos'] != $_POST['move_second_pos']))
		{
			$move_quest_sql1 = mysql_query ("UPDATE  .`".$_SESSION['test_table']."` SET `ID` = '0' WHERE `".$_SESSION['test_table']."`.`ID` =".$_POST['move_first_pos']." LIMIT 1") or die ("new кавабанга 1");
			$move_quest_sql2 = mysql_query ("UPDATE  .`".$_SESSION['test_table']."` SET `ID` = '".$_POST['move_first_pos']."' WHERE `".$_SESSION['test_table']."`.`ID` =".$_POST['move_second_pos']." LIMIT 1") or die ("new кавабанга 2");
			$move_quest_sql3 = mysql_query ("UPDATE  .`".$_SESSION['test_table']."` SET `ID` = '".$_POST['move_second_pos']."' WHERE `".$_SESSION['test_table']."`.`ID` ='0' LIMIT 1") or die ("new кавабанга 3");
			
			echo '<META HTTP-EQUIV=Refresh CONTENT="0; test_edit.php?move_quest=1">';			
		}
		else
		{
			$move_error =  "<script>alert('Ошибка! Вы неправильно выбрали указатели для перемещения');</script>";
		}
	}

	echo "Вместо иконок в списке вопросов появились 2 столбца с указателями. Первый означает вопрос который нужно переместить, а второй указатель означает тот вопрос, который будет заменен первым.
		<p>Чтобы переместить вопрос нужно выделить по одному указателю в каждом столбце. Указатели в двух столбцах не должны быть одинаковыми.</p>
		<p><input type='submit' name='move_quest' value='Переместить'>
			<input type='submit' name='cancel_moving_quest' value='Закрыть'></p>
	
		</div>";
	echo $move_error;
}
echo "</form>"; 

//Быстрый показ ответов на выделенный вопрос
if (isset($_GET['showid']))
{
	$load_answer = mysql_query("SELECT * FROM ".$_SESSION['test_table']." WHERE ID =".$_GET['showid']);
	$show_answer = mysql_fetch_assoc($load_answer);
	
	echo "<div id='right_small_block'>
	<div id='action_header'><h4 class='header_title' align='center'>Ответы на вопрос №".$_GET['viewid']."</h4></div>";
	
	$answers = explode ("",$show_answer['ANSWERS']);
	//Если существуют сообщения в тесте, то выводит вопросы на экран
	if (count($answers)>0)
	{
		echo "
		<div id='right_content_block' style='float:left;overflow:auto;width:100%;height:580px;' >
		<table cellpadding='5'  cellspacing='2' width=100%>";		
		
			$ans_limit = count($answers);
			for ($j=0;$j<$ans_limit;$j++)
			{
				$num = $j+1;
				//Смена цвета строки таблицы
				if ( ($j % 2) == 0)
				{
					$tr_color = "bgcolor='#E1E1EB'";
				}
				else
				{
					$tr_color = "bgcolor='#F5F5FF'";
				}
				echo "<tr height='30' ".$tr_color." width='400px'>
						<td width='20'>".$num."</td>";
				$answers[$j] = prepear_readed_answer($answers[$j]);
				//Если первый символ звезда, то сообщает, что это правильный ответ
				if ($answers[$j][0]=="*")
				{
					$answers[$j] = substr($answers[$j],1);
					echo "<td>".$answers[$j]." (правильный ответ)</td>";
				}
				else
				{
					
					echo "<td>".$answers[$j]."</td>";
				}
				echo "</tr>";
			}
			
		echo "</table>";
	}
	else
	{
		echo "<h4>В данном вопросе нет ответов.</h4>";
	}
	echo "</div>";
}

//Добавление вопроса в тест
//Проверка правильности ввода значений при добавлении вопроса
if (isset($_POST['add_quest']))
{
	$_SESSION['validate_create_part1'] = true;
	
	if (($_POST['question'] == '') )
	{
		$_SESSION['validate_create_part1'] = false;
		$report = 'create1_quest_error';
	}
	elseif (preg_match ("/^[0-9]/",$_POST['answernum']) == false)
	{
		$_SESSION['validate_create_part1'] = false;
		$report = 'create1_ansnum_error';
	}
	elseif ($_POST['answernum']>20)
	{
		$_SESSION['validate_create_part1'] = false;
		$report = 'create1_bigansmun_error';
	}
	$view_quest = $_POST['question'];
	$view_ansnum = $_POST['answernum'];
}
//Форма для ввода изначальных характеристик нового вопроса
if (!isset($_GET['showid']) && !isset($_GET['editquestid']) && !isset($_GET['move_quest']) && $_SESSION['validate_create_part1'] != true)
{
	echo "
	<div id='right_small_block'>
	<form name='create_quest' method='post'>
	<div id='action_header'><h4 class='header_title' align='center'>Создание вопроса</h4></div>";
	$view_quest = $view_ansnum = $check_checkbox = '';
	$check_radio = 'checked=checked';
	
	//Если при добавлении вопроса была произведена ошибка, то заполняет поля для ввода
	if ($_SESSION['validate_create_part1'] == false)
	{
		$view_quest = $_POST['question'];
		$view_ansnum = $_POST['answernum'];
		if ($_SESSION['selecttype'] == 'check')
		{
			$check_checkbox = 'checked=checked';
			$check_radio = '';
		}	
	}
	echo "
		<textarea rows='2' cols='50' name='question' placeholder='Введите текст вопроса'>".$view_quest."</textarea>
		<p title='Сколько правильных ответов будет в вопросе'>Тип выбора:<br><font title='Выберите если на вопрос существует только один ответ'>
		<input type='radio' name='selecttype' value='radio' ".$check_radio.">Только один.</font>
		<font title='Выберите если на вопрос существует больше одного ответа'><input type='radio' name='selecttype' value='check' ".$check_checkbox.">Два и более<br></font>
		</p>
		<p title='Сколько всего ответов будет в вопросе'>Число ответов:<input type='text' name='answernum' size='3' value='".$view_ansnum."'></p>

		<input type='submit' name='add_quest' value='Добавить вопрос'>
		
	</form>";
		
	echo "</div>";
	
	//Скрипт изменения размера формы
	echo "
	<script>
		document.forms.create_quest.question.cols = document.documentElement.clientWidth/18;
	</script>";
}
//Проверка правильности ввода ответов на новый вопрос
if (isset($_POST['accept_add']))
{
	$_SESSION['validate_create_part2'] = true;
	$checked_boxes = 0;
	for ($i=1; $i <= $_SESSION['answernum']; $i++)
	{

		if ( $_POST['answer'.$i]=='')
		{
			$_SESSION['validate_create_part2'] = false;
			$report = 'create2_answer_error';
		}
		elseif ($_SESSION['selecttype']=='radio')
		{
			if ($_POST['right_answer']== '')
			{
				$_SESSION['validate_create_part2'] = false;
				$report = 'create2_radio_error';
			}
		}
		else
		{
			if (isset($_POST['right_answer'.$i]))
			{
				$checked_boxes++;
			}
		}
	}
	if ((!isset($report) || $report=='' ) && $_SESSION['selecttype']=='check')
	{
		if ($checked_boxes == 0)
		{
			$_SESSION['validate_create_part2'] = false;
			$report = 'create2_check_error';
		}
		elseif ($checked_boxes == $_SESSION['answernum'])
		{
			$_SESSION['validate_create_part2'] = false;
			$report = 'create2_check_all_error';
		}
	}	
}
//Форма для ввода ответов на вопрос
if ((isset($_POST['add_quest']) || isset($_SESSION['question'])) && $_SESSION['validate_create_part1'] == true && $_SESSION['validate_create_part2'] != true )
{
	if (isset($_POST['add_quest']))
	{
		$_SESSION['question']=$_POST['question'];
		$_SESSION['selecttype']=$_POST['selecttype'];
		$_SESSION['answernum']=$_POST['answernum'];
	}
	
	if (strlen($_SESSION['question']) > 50)
	{
		$viewable_answer = substr($_SESSION['question'],0,50) . "..";
	}
	else
	{
		$viewable_answer = $_SESSION['question'];
	}
	echo "
	<div id='right_small_block'>
		<div id='action_header'><h4 class='header_title' align='center'>Введите ответы на вопрос &quot;".$viewable_answer."&quot;</h4></div>
	
		<form name='create_answers' method='post'>";
		
	
	echo "
		<div id='right_edit_block' style='float:left;overflow:auto;width:100%;height:600px;' >
		<table cellpadding=0 cellspacing=0>";
	
	
	for ($i=1; $i <= $_SESSION['answernum']; $i++)
	{
		$view_answer = '';
		$view_check = '';
		if ($_SESSION['validate_create_part2'] == false)
		{
			$view_answer = $_POST['answer'.$i];
			if (isset($_POST['right_answer'.$i]))
			{
				$view_check = "checked='checked'";
			}
		}
		echo "
		<tr>
			<td>".$i.":</td>";
			
		if ($_SESSION['selecttype']=='radio')
		{
			echo "<td><input type='radio' title='Правильный ответ' name='right_answer' value='".$i."' ></td>";
		}
		else
		{
			echo "<td><input type='checkbox'  title='Правильный ответ' name='right_answer".$i."' value='".$i."' ".$view_check."></td>";
		}	
			
		echo "<td valing=center><textarea rows='2' cols='45' class='answer_area' name='answer".$i."'>".$view_answer."</textarea></td>";
		echo "</tr>	
		";
		
		echo "
		<script>
			document.forms.create_answers.answer".$i.".cols = document.documentElement.clientWidth/21;
		</script>
		";
	}
	echo "
		</table>
		<input type='submit' name='accept_add' value='Создать вопрос'>
		<input type='submit' name='cancel_add' value='Отмена'>
		</form>
		
		</div>";	
}

//Если поля заполнены правильно, то добавляет новый вопрос
if (isset($_POST['accept_add']) && $_SESSION['validate_create_part2'] == true )
{
	$answer = '';
	for ($i=1; $i <= $_SESSION['answernum']; $i++)
	{
		$_POST['answer'.$i] = prepear_writed_answer($_POST['answer'.$i]); //замена лишних элементов на эскейп-последовательности
		
		if ($_SESSION['selecttype']=='radio')
		{
			if ($_POST['right_answer']==$i)
			{
				$_POST['answer'.$i] = '*'.$_POST['answer'.$i];
			}
			if ($i == $_SESSION['answernum'])
			{
				$answer .= $_POST['answer'.$i];
			}
			else
			{
				$answer .= $_POST['answer'.$i].'';
			}
		}
		else
		{
			if (isset($_POST['right_answer'.$i]))
			{
				$_POST['answer'.$i] = '*'.$_POST['answer'.$i];
			}
			if ($i == $_SESSION['answernum'])
			{
				$answer .= $_POST['answer'.$i];
			}
			else
			{
				$answer .= $_POST['answer'.$i].'';
			}
		}
	}
	$add_new_question = mysql_query ("
		INSERT INTO  .`".$_SESSION['test_table']."` 
		VALUES (
		NULL ,
		'".$_SESSION['question']."', 
		'".$answer."', 
		'".$_SESSION['selecttype']."'
		);"	);
	unset ($_SESSION['validate_create_part1']);
	unset ($_SESSION['validate_create_part2']);
	echo "<script>
			parent.location='test_edit.php';
		</script>";
}
//Кнопка отмены действия
if (isset($_POST['cancel_add']))
{
	unset ($_SESSION['validate_create_part1']);
	unset ($_SESSION['validate_create_part2']);
	unset ($report);
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; test_edit.php">';
}
//Редактирование вопросов в тесте
//Заголовок блока редактирования
if (isset($_GET['editquestid']))
{
	$load_list = mysql_query("SELECT * FROM ".$_SESSION['test_table']." WHERE ID=".$_GET['editquestid']);
	$answer = mysql_fetch_assoc($load_list);
	
	$class1 = $class2 = $class3 = 'class=nonclicked_button';
	//Если выбрано действие, то кнопка особенно выделяется
	switch ($_GET['editaction'])
	{
		case '1':
			$class1 = 'class=clicked_button'; break;
		case '2':
			$class2 = 'class=clicked_button'; break;
		case '3':
			$class3 = 'class=clicked_button'; break;
	}
	
	echo "<div id='right_small_block'>
	<div id='action_header'><h4 class='header_title' align='center'>Редактирование вопроса №".$_GET['viewid']."</h4></div>
	<center>
	<button ".$class1." name='edit1' onclick=init_edit1() >Изменить вопрос</button>
	<button ".$class2." name='edit1' onclick=init_edit2() >Изменить ответы</button>
	<button ".$class3." name='edit1' onclick=init_edit3() >Добавить ответ</button>
	</center>
	<form name='edit_quest' method='post'>";
	//Обработка кнопок перехода по функциям редактирования
	echo "
	<script>
		function init_edit1 ()
		{
			parent.location='test_edit.php?editquestid=".$_GET['editquestid']."&editaction=1&viewid=".$_GET['viewid']."';
		}
		function init_edit2 ()
		{
			parent.location='test_edit.php?editquestid=".$_GET['editquestid']."&editaction=2&viewid=".$_GET['viewid']."';
		}
		function init_edit3 ()
		{
			parent.location='test_edit.php?editquestid=".$_GET['editquestid']."&editaction=3&viewid=".$_GET['viewid']."';
		}
	</script>";
}
//Кнопка изменения текста вопроса и типа выбора его ответов
if (isset($_GET['editquestid']) && ($_GET['editaction']=='1'))
{
	$choose_radio = $choose_check = '';
	if ($answer['SELECTTYPE']=='radio')
	{
		$choose_radio = "checked='checked'";
	}
	else
	{
		$choose_check = "checked='checked'";
	}
	//Проверка вводимых значений
	if (isset($_POST['edit_quest_btn']))
	{
		$validate_edit_part1 = true;
		
		if (($_POST['question'] == ''))
		{
			$validate_edit_part1 = false;
			$report='edit1_quest_error';
		}
	}
	if (!isset($validate_edit_part1))
	{
		$view_question = $answer['QUESTION'];
	}
	else
	{
		$view_question = $_POST['question'];
	}
	echo "
	<p><textarea rows='2' cols='50' name='question'>".$view_question."</textarea></p>
	<p>Тип выбора:</br>
		<input type='radio' name='selecttype' value='radio' ".$choose_radio." onclick=alerter()>Только один. 
		<input type='radio' name='selecttype' value='check' ".$choose_check." onclick=alerter()>Два и более.</p>
		<input type='submit' name='edit_quest_btn' value='Применить изменения'>";
		
	echo "
	<script>
		document.forms.edit_quest.question.cols = document.documentElement.clientWidth/18;
	</script>";
	//Если проверка правильности ввода была верной, то применяет изменения к вопросу
	if (isset($_POST['edit_quest_btn']) && $validate_edit_part1== true)
	{
		if ($answer['SELECTTYPE'] != $_POST['selecttype'])
		{
			$writable_answer = str_replace ("*","",$answer['ANSWERS']);
		}
		else 
		{
			$writable_answer = $answer['ANSWERS'];
		}
		
		$edit_quest_main_params = mysql_query ("
		UPDATE  .`".$_SESSION['test_table']."` 
		SET
		`QUESTION` = '".$_POST['question']."',
		`SELECTTYPE` = '".$_POST['selecttype']."',
		`ANSWERS` = '".$writable_answer."'
		WHERE `".$_SESSION['test_table']."`.`ID` =".$_GET['editquestid']." LIMIT 1 ;");
		echo "Основные настройки вопроса обновлены";
		
		echo '<META HTTP-EQUIV=Refresh CONTENT="0; test_edit.php?editquestid='.$_GET['editquestid'].'&viewid='.$_GET['viewid'].'">';
	}
}
//Кнопка изменения текста ответов и указателей на правильный ответ
if (isset($_GET['editquestid']) && ($_GET['editaction']=='2'))  
{
	if ($answer['ANSWERS']!= '')
	{
		$answers = explode ("",$answer['ANSWERS']); //
		$ans_limit = count($answers);
		$_SESSION['answernum'] = $ans_limit;
		//Проверка правильности ввода ответов и указателей на верный ответ
		if (isset($_POST['edit_answer_btn']))
		{
			$validate_edit_part2 = true;
			$checked_boxes = 0;
			unset ($report);
			for ($i=0; $i < $ans_limit; $i++)
			{
				if (($_POST['answer'.$i] == '') )
				{
					$validate_edit_part2 = false;
					$report = 'edit2_answer_error';
				}
				elseif ($answer['SELECTTYPE'] == 'radio')
				{
					if ($_POST['right_answer'] > $ans_limit)
					{
						$validate_edit_part2 = false;
						$report = 'edit2_outsideradio_error';
					}
					if ($_POST['right_answer'] == '')
					{
						$validate_edit_part2 = false;
						$report = 'edit2_radio_error';
					}
				}
				else
				{
					if (isset($_POST['right_answer'.$i]))
					{
						$checked_boxes++;
					}
				}		
			}
			if ((!isset($report) || $report=='' ) && ($answer['SELECTTYPE']=='check'))
			{
				$edit_not_checked_boxes = 0;
				
				if ($checked_boxes == $ans_limit)
				{
					$validate_edit_part2 = false;
					$report = 'edit2_check_all_error';
				}
				elseif ($checked_boxes == 0)
				{
					$validate_edit_part2 = false;
					$report = 'edit2_check_error';
				}				
			}
		}
		echo "
		<div id='right_edit_block' style='float:left;overflow:auto;width:100%;height:500px;' >
		<table cellspacing='0px'>";
		//Вывод полей ввода ответов и указателей на верный ответ
		for ($i=0; $i < $ans_limit; $i++)
		{
			$num = $i+1;
			echo "
			<tr>
				<td height='70px'>".$num.":</td>";
				
			$view_answer = $view_check = '';
			if (isset($validate_edit_part2))
			{
				$view_answer = $_POST['answer'.$i];
				if (isset($_POST['right_answer'.$i]))
				{
					$view_check = "checked='checked'";
				} 
				elseif ($_POST['right_answer']==$num)
				{
					$view_check = "checked='checked'";
				}
			}
			else 
			{
				if ($answers[$i][0]=="*")
				{
					$answers[$i] = substr($answers[$i],1);
					$view_check = " checked='checked'";
				}
				$view_answer = $answers[$i];
			}
			if ($answer['SELECTTYPE']=='radio')
			{
				echo "<td><input type='radio' name='right_answer' value='".$num."'".$view_check."></td>";
			}
			else
			{
				echo "<td><input type='checkbox' name='right_answer".$i."' value='".$i."'".$view_check."></td>";
			}	
			echo "<td><textarea rows='2' cols='40' name='answer".$i."'>".$view_answer."</textarea></td>
				<td><a href='test_edit.php?delanswerid=".$i."&editquestid=".$_GET['editquestid']."&viewid=".$_GET['viewid']."'><img src='../images/buttons/delete_btn.png' alt='Удалить вопрос'></a></td>
				</tr>";
			echo "
			<script>
				document.forms.edit_quest.answer".$i.".cols = document.documentElement.clientWidth/21;
			</script>";
		}
		echo "
		</table>
		<input type='submit' name='edit_answer_btn' value='Применить изменения'>
		</div>";
	}
	else
	{
		echo "<h4>В данном вопросе нет ответов</h4>";
	}	
	//Если поля заполнены правильно, то применяет изменения к ответам
	if (isset($_POST['edit_answer_btn'])   && $validate_edit_part2 == true )
	{
		$answers = '';
		$ans_limit = $_SESSION['answernum']-1;
		for ($i=0; $i <= $ans_limit ; $i++)
		{
			$num = $i+1; 
			$_POST['answer'.$i] = prepear_writed_answer($_POST['answer'.$i]);

			if ($answer['SELECTTYPE']=='radio')
			{			
				if ( $num == $_POST['right_answer'])
				{
					$_POST['answer'.$i] = '*'.$_POST['answer'.$i];
				}
				if ($i == $ans_limit)
				{
					$answers .= $_POST['answer'.$i];
				}
				else
				{
					$answers .= $_POST['answer'.$i].'';
				}
			}
			else
			{			
				if (isset($_POST['right_answer'.$i]))
				{
					$_POST['answer'.$i] = '*'.$_POST['answer'.$i];
				}
				if ($i == $ans_limit)
				{
					$answers .= $_POST['answer'.$i];
				}
				else
				{
					$answers .= $_POST['answer'.$i].'';
				}
			}
		}
		$edit_quest_answers = mysql_query ("
		UPDATE  .`".$_SESSION['test_table']."` 
		SET
		`ANSWERS` = '".$answers."'
		WHERE `".$_SESSION['test_table']."`.`ID` =".$_GET['editquestid']." LIMIT 1 ;");
				
		echo '<META HTTP-EQUIV=Refresh CONTENT="0; test_edit.php?editquestid='.$_GET['editquestid'].'&editaction=2&viewid='.$_GET['viewid'].'">';
	}
}
//Удаление ответа из списка
if (isset($_GET['delanswerid']))
{
	$answers = explode ("",$answer['ANSWERS']);

	$ans_lenght = count($answers);
	unset ($answers[$_GET['delanswerid']]);
	
	$new_answer ='';
	
	for ($i=0; $i < $ans_lenght; $i++)
	{
		if ($i != $_GET['delanswerid'])
		{
			$new_answer .= $answers[$i];
			if  ($i != count($answers))
			{
				$new_answer .= "";
			}	
		}			
	}
	if ($ans_lenght-1 == $_GET['delanswerid'])
	{
		$new_answer = substr($new_answer,0,-1);
	}
	
	$delete_answer = mysql_query ("
	UPDATE  .`".$_SESSION['test_table']."` 
	SET
	`ANSWERS` = '".$new_answer."'
	WHERE `".$_SESSION['test_table']."`.`ID` =".$_GET['editquestid']." LIMIT 1 ;");	

	echo '
		<META HTTP-EQUIV=Refresh CONTENT="0; test_edit.php?editquestid='.$_GET['editquestid'].'&editaction=2&viewid='.$_GET['viewid'].'">';	
}
//Кнопка добавления дополнительных ответов к вопросу
if (isset($_GET['editquestid']) && ($_GET['editaction']=='3'))
{
	
	//Проверка правильности ввода значений
	if (isset($_POST['insert_answer']))
	{
		$validate_edit_part3 = true;
		
		if (($_POST['new_answer'] == '') )
		{
			$validate_edit_part3 = false;
			$report = 'edit3_quest_error';
		}
	}
	
	if (!isset($validate_edit_part1))
	{
		$view_question = $answer['QUESTION'];
	}
	else
	{
		$view_question = $_POST['question'];
	}
	
	echo "
	<table>
	<tr>";
		
	if ($answer['SELECTTYPE']=='radio')
	{
		echo "<td><input type='radio' name='right_answer' title='Правильный ответ' value='right' ></td>";
	}
	else
	{
		echo "<td><input type='checkbox' name='right_answer_new' title='Правильный ответ' value='right'></td>";
	}	
	echo "
	<td height='70px'><textarea placeholder='Введите текст нового вопроса' rows='2' cols='40' name='new_answer'></textarea></td>
	</tr>
	</table>
	<input type='submit' name='insert_answer' value='Добавить ответ'>";
	
	echo "
	<script>
		document.forms.edit_quest.new_answer.cols = document.documentElement.clientWidth/20;
	</script>";
	
	//Если поля заполнены правильно, то добавляет ответ к общему списку и перезаписывает их
	if (isset($_POST['insert_answer']) && $validate_edit_part3 == true)
	{
		$load_list = mysql_query("SELECT * FROM ".$_SESSION['test_table']." WHERE ID=".$_GET['editquestid']);
		$insert_answer = mysql_fetch_assoc($load_list);
		
		if ($_POST['right_answer'] == 'right')
		{
			$insert_answer['ANSWERS'] = str_replace ("*","",$insert_answer['ANSWERS']);
			$_POST['new_answer'] = "*".$_POST['new_answer'];
		}
		elseif ($_POST['right_answer_new'] == 'right')
		{
			$_POST['new_answer'] = "*".$_POST['new_answer'];
		}
		
		if ($answer['ANSWERS'] == '')
		{
			$new_answer = $_POST['new_answer'];
		}
		else
		{
			$new_answer = $insert_answer['ANSWERS']."".$_POST['new_answer'];
		}
		$insert_answer = mysql_query ("
		UPDATE  .`".$_SESSION['test_table']."` 
		SET
		`ANSWERS` = '".$new_answer."'
		WHERE `".$_SESSION['test_table']."`.`ID` =".$_GET['editquestid']." LIMIT 1 ;");
		
		echo '
		<META HTTP-EQUIV=Refresh CONTENT="0; test_edit.php?editquestid='.$_GET['editquestid'].'&editaction=2&viewid='.$_GET['viewid'].'">';
	}
}
//Окночательная часть блока вопросов
if (isset($_GET['editquestid']))
{	
	echo "</form></div>";
}
//Удаление вопроса из списка после подвтерждения
if (isset($_GET['deleteidaccepted']))
{
	$delete_answer = mysql_query("
	DELETE FROM `".$_SESSION['test_table']."` WHERE `".$_SESSION['test_table']."`.`ID` = ".$_GET['deleteidaccepted']." LIMIT 1") or die ("не пашет");
	
	echo '
	<META HTTP-EQUIV=Refresh CONTENT="0; URL=test_edit.php">';
}
//Отмена удаления вопроса
if (isset($_POST['cancel_delete']))
{
	echo '
	<META HTTP-EQUIV=Refresh CONTENT="0; test_edit.php">';
}
//Удаление вопроса. Вывод сообщения о предупреждении
if (isset($_GET['deleteid']))
{
	echo  '
	
	<SCRIPT LANGUAGE="javascript">
	if (confirm("Удалить выбранный вопрос?")) {
		parent.location="test_edit.php?deleteidaccepted='.$_GET['deleteid'].'";
		}
	</SCRIPT>';
}

?>
<div style="clear: both;"> </div>
<!--Изменение размеров форм ввода под размер экрана-->
	<script>
		if (document.getElementById('left_content_block'))
		{
		document.getElementById('left_content_block').style.height = (document.documentElement.clientHeight -230)+'px'
		}
		if (document.getElementById('right_content_block'))
		{
			document.getElementById('right_content_block').style.height = (document.documentElement.clientHeight - 290)+'px'
		}
		if (document.getElementById('right_edit_block'))
		{
			document.getElementById('right_edit_block').style.height = (document.documentElement.clientHeight - 330)+'px'
		}
		function alerter()
		{
			alert('Внимание! При изменении значения "Тип выбора" все указатели на правильные ответы в этом вопросе будут удалены!');
		}

	</script>	
<?php
//Вывод сообщений об ошибках
switch( $report ) 
 { 
    case 'create1_quest_error': 
        echo "<script> alert('Ошибка! Вы неправильно ввели вопрос.');</script>";
		break;
	case 'create1_ansnum_error':
		echo "<script> alert('Ошибка! Вы неправильно ввели число ответов.');</script>";
		break;
	case 'create1_bigansmun_error':
		echo "<script> alert('Ошибка! Нельзя создавать больше 20 ответов.');</script>";
		break;
	case 'create2_answer_error':
		echo "<script>alert('Ошибка! Вы заполнили не все поля для ответов.')</script>";
		break;
	case 'create2_radio_error':
		echo "<script>alert('Ошибка! Вы не указали верный ответ.')</script>";
		break;
	case 'create2_check_error':
		echo "<script>alert('Ошибка! Вы не указали ни одного верного ответа.')</script>";
		break;
	case 'create2_check_all_error':
		echo "<script>alert('Ошибка! Вы указали все вопросы правильными.')</script>";
		break;
	case 'edit1_quest_error':
		echo "<script>alert('Ошибка! Вы неверно ввели текст вопроса.')</script>";
		break;
	case 'edit2_answer_error':
		echo "<script>alert('Ошибка! Не все ответы были правильно введены.')</script>";
		break;
	case 'edit2_outsideradio_error':
		echo "<script>alert('Ошибка! Вы выделили не тот указатель на верный ответ.')</script>";
		break;
	case 'edit2_radio_error':
		echo "<script>alert('Ошибка! Вы не указали правильный ответ.')</script>";
		break;
	case 'edit2_check_error':
		echo "<script>alert('Ошибка! Вы не указали ни одного верного ответа.')</script>";
		break;
	case 'edit2_check_all_error':
		echo "<script>alert('Ошибка! Вы выделили все ответы как правильные.')</script>";
		break;
	case 'edit3_quest_error':
		echo "<script>alert('Ошибка! Вы выделили все ответы как правильные.')</script>";
		break;			
} 

?>
	
</body>
</html>