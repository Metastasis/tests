<!--Страница создания печатной формы-->
<?
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Печатная форма</title>
<!--таблица стилей, уникальная для этой страницы-->
<style>
body {
	margin: 0 10px;
	padding:0;
}
ul {
	list-style-type: none;
	margin:0;
	padding:0;
}

ul li {
	padding:0px;
	margin:0;
	margin-top:7px;
	font-weight:bold;
}

ul p {
	margin:0;
	padding:0;
	padding-left:30px;
	text-align:left;
}

li b {
	font-weight:bolder;
}

p {
	margin:0;
	padding:0;
	text-align:center;
}

</style>
</head>
<body>
<?
require_once '../connect.php';
require_once '../config.php';
require_once '../functions.php';

//Вывод на экран названия организации, которое хранится в файле config.php
echo "<p><b>".PGPK."</b></p>";

//Вывод специальности на экран. Зависит от выбранного краткого названия в списке, находящимся на  прошлой странице
$load_speciality = mysql_query ("SELECT * FROM `speciality` WHERE `ID`=".mysql_real_escape_string($_GET['name_spec'])) or die ('Ошибка');
$spec_list = mysql_fetch_assoc($load_speciality);
echo "<p>Специальность: ".$spec_list['ID']." &quot;".$spec_list['NAME']."&quot;</p>";

//Вывод дисциплины на экран. Зависит от выбранного краткого названия в списке, находящимся на  прошлой странице
$load_discipline = mysql_query ("SELECT * FROM `discipline` WHERE `ID`=".mysql_real_escape_string($_GET['name_disc'])) or die ('Ошибка');
$disc_list = mysql_fetch_assoc($load_discipline);
echo "<p>Дисциплина: &quot;".$disc_list['NAME']."&quot;</p>";

//Загрузка нужной записи из базы данных
$load_result = mysql_query ('SELECT * FROM RESULTS WHERE ID='.mysql_real_escape_string($_GET['formid'])) or die ('Ошибка!');
$result_list = mysql_fetch_assoc($load_result);

//Вывод заголовка и даты
echo "<p>Отчет о прохождении теста &quot;".$result_list['TESTNAME']."&quot;";
if (isset($_GET['show_date']))
{
	echo " за ".convert_data_for_user($result_list['TESTDATE'])."</p>";
}
else
{
	echo "</p>";
}
//Вывод информации о пользователе
$load_user =  mysql_query ("SELECT * FROM USERS WHERE `USERNAME`='".$result_list['USERNAME']."'") or die ('Ошибка!');
$user_list = mysql_fetch_assoc($load_user);

echo "<p>Студент: ".$user_list['USERNAME'].", ";
echo "группа: ".$user_list['USERGROUP']."</p>";

//Вывод статистики и отвеченных вопросов
$fail_answers = $result_list['RESALL'] - $result_list['RESRIGHT'];

echo '<p>Количество правильных ответов: '.$result_list['RESRIGHT'].', 
	количество неправильных ответов: '.$fail_answers.', 
	количество вопросов: '.$result_list['RESALL'].', 
	оценка: '.$result_list['RESBALL'].'</p>
	';
	
echo prepear_readed_answer($result_list['TESTLOG']);
?>
</body>
</html>