<!--Страница авторизации пользователей в системе-->
<?
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Тестирование</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id='pict_box'></div>
<div id='header_menu'>
	Web-приложение для организации тестирования
</div>

<div id='content' align=center>
<center>

<h3>Авторизация</h3>
<!--Форма ввода-->
<form method="POST">
	<table  cellpadding="5" cellspacing="4"> 
	<tr align='left'>
		<td>Введите свой логин:</td>
		<td><input type="text" name="user_name" id="s" size="40" /></td>
	</tr>
	<tr align='left'>
		<td>Введите свой пароль:</td>
		<td><input type="password" name="user_pass" id="s" size="40" /></td>
	</tr>
	<tr>
		<td align=center colspan=2><input name="submit" type="submit" value="Войти в систему"></td>
	</tr>
	</table>
	
</form>  
	<p>Авторизоваться могут только заранее зарегистрированные пользователи!</p>
</div>

<?php
session_start();
require_once 'connect.php';
require_once 'config.php';
require_once 'functions.php';


//Действия при нажатии на кнопку авторизации.
//Проверяет существование пользователя, его пароль и по группе перенаправляет на нужную страницу
if(isset($_POST['submit']))
{
    $query = mysql_query("SELECT * FROM USERS WHERE USERNAME='".mysql_real_escape_string($_POST['user_name'])."' LIMIT 1");
    $data = mysql_fetch_assoc($query);
	
	
	if (($_SESSION['stats']['login_errors']>=$log_check_login_count) && ($_SESSION['stats']['login_error_writed'] != true)) 
		{
			//if (($log_check_login_user_only == false && $data['USERGROUP']==$admin_write) || ($log_check_login_user_only == true && $data['USERGROUP']!=$admin_write))
			
			$check_date = mysql_query("SELECT NOW()");
			$mas_current_date = mysql_fetch_assoc($check_date);
			$current_date = $mas_current_date ["NOW()"];
			
			$log_file = fopen ("data/useractionlog.txt","a+");
			$save_data = convert_data_for_log($current_date).": пользователь <b>".$_POST['user_name']."</b> перед авторизацией по адресу ".$_SERVER['REMOTE_ADDR']." некорретно ввел данные <b>".$_SESSION['stats']['login_errors']."</b> раз(а).<br>\n";
			if ( !$log_file )
			{
				echo("Ошибка открытия файла");
			}
			else
			{
				fputs ( $log_file, $save_data);
				$_SESSION['stats']['login_error_writed'] == true;
			}
			fclose ($log_file);
			
			
		}
	if($data['USERPASS'] === $_POST['user_pass'])
	{
		$_SESSION['stats']['login_error_writed']=false;
		$_SESSION['stats']['login_errors']=0;
		
		$_SESSION['username']=$data['USERNAME'];
		if ($data['USERGROUP']==$admin_write)
		{
			$_SESSION['usergroup']=$admin_index;
			$_SESSION['log_page']=1;
			echo '<META HTTP-EQUIV=Refresh CONTENT="0; admin.php">';
		}
		else 
		{
			$_SESSION['usergroup']=$data['USERGROUP'];
			$_SESSION['uservariant']=$data['USERVARIANT'];
			echo '<META HTTP-EQUIV=Refresh CONTENT="0; test_lister.php">';
		}
		
	}
	else
	{
		echo '<script>alert("Ошибка! Неправильно введен логин или пароль.");</script>';
		$_SESSION['stats']['login_errors']++;
	}
}
?>


<?
if(isset($_GET['alert']))
{
	echo '<script>alert("Неавторизованные пользователи не могут выполнять это действие!");</script>';
}

?>
</center>
</div>

</body>
</html>

