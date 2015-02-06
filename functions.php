<?php
//Файл, хранящий пользовательские функции

//Функция обработки входящей строки при создании или редактировании теста
//Нужна для стирания "опасных" символов, которые могут нарушить разметку страницы
function prepear_writed_answer($prepeared_string)
{
	$prepeared_string = str_replace ("<","&#060;",$prepeared_string);
	$prepeared_string = str_replace (">","&#062;",$prepeared_string);
	$prepeared_string = str_replace ("*","&#042;",$prepeared_string); 
	return $prepeared_string;
}
//Функция обработки исходящей строки при показе или прохождении теста
//Нужна для добавления элементов разметки
function prepear_readed_answer($prepeared_string)
{
	$prepeared_string = str_replace ("
","<br>",$prepeared_string);
	$prepeared_string = str_replace ("[b]","<b>",$prepeared_string);
	$prepeared_string = str_replace ("[/b]","</b>",$prepeared_string);
	$prepeared_string = str_replace ("[i]","<i>",$prepeared_string);
	$prepeared_string = str_replace ("[/i]","</i>",$prepeared_string);
	$prepeared_string = str_replace ("[s]","<s>",$prepeared_string);
	$prepeared_string = str_replace ("[/s]","</s>",$prepeared_string); 
	return $prepeared_string;
}
//Функция обработки входящих данных из массива $_POST или $_GET о переключателях возможностей теста
//Нужна для преобразования информации, чтобы записать ее в поле TESTMODE
//Для показа результатов нужно убрать комментарии с echo и поставить die() в конце работы функции
function test_mode_encoder ($test_mode)
{

	switch ($test_mode['test_active'])
	{
		case 'ON':
		//echo '<h4>Активность: да</h4>'; 
		$mode_active = 'ACTIVE_ON'; break;
		case 'OFF':
		//echo '<h4>Активность: нет</h4>';
		$mode_active = 'ACTIVE_OFF'; 		break;
		case 'LOCK':
		//echo '<h4>Активность: заперт</h4>';
		$mode_active = 'ACTIVE_LOCK'; 		break;
		default: $mode_active = 'ACTIVE_NONE'; break;
	}
	switch ($test_mode['select_date'])
	{
		case 'ON':
		//echo '<h4>Проверка даты: да</h4>'; 
		$mode_date = 'DATE_ON';  break;	
		case 'OFF':
		//echo '<h4>Проверка даты: нет</h4>';
		$mode_date = 'DATE_OFF'; break;
		default: $mode_date = 'DATE_NONE'; break;
	}
	
	switch ($test_mode['select_pass'])
	{
		case 'ON':
		//echo '<h4>Пароль: да</h4>';
		$mode_pass = 'PASS_ON'; break;
		case 'OFF':
		//echo '<h4>Пароль: нет</h4>'; 
		$mode_pass = 'PASS_OFF'; break;
		default: $mode_pass = 'PASS_NONE'; break;
	}
	
	$final_test_mode = $mode_active . "," . $mode_date . "," . $mode_pass;
	return $final_test_mode;
}


function test_mode_decoder ($test_mode)
{
	$test_mode = explode(",",$test_mode);
	print_r ($test_mode);
	return $test_mode;
}


//Функция конвертирования данных из обычного вида в формат поля SQL
//Нужно для правильной записи информации в таблицу
function convert_data_for_sql ($init_date)
{	
	
		$new_date =
		$init_date[6]. 
		$init_date[7].
		$init_date[8].
		$init_date[9]."-".
		$init_date[3].
		$init_date[4]."-".
		$init_date[0].
		$init_date[1];
	
	return $new_date;
}
//Функция конвертирования данных из формата поля для SQL в обычный вид
//Нужно для корректного вывода информации на экран
function convert_data_for_user ($init_date)
{	
	
		$new_date =
		$init_date[8]. 
		$init_date[9]."-".
		$init_date[5].
		$init_date[6]."-".
		$init_date[0].
		$init_date[1].
		$init_date[2].
		$init_date[3];
	
	return $new_date;
}

//Функция конвертирования данных из формата поля для SQL в обычный вид
//Нужно для корректного вывода информации на экран
function convert_data_for_log ($init_date)
{	
		$new_time = substr($init_date,10);
		$new_date =
		$init_date[8]. 
		$init_date[9]."-".
		$init_date[5].
		$init_date[6]."-".
		$init_date[0].
		$init_date[1].
		$init_date[2].
		$init_date[3].
		$new_time;
	
	return $new_date;
}
//Функция записи реакции на попытку войти в файлы администратора без предварительной авторизации
//Нужно для защиты от взлома системы
function write_entry_attention($user_ip)
{
	$check_date = mysql_query("SELECT NOW()");
	$mas_current_date = mysql_fetch_assoc($check_date);
	$current_date = $mas_current_date ["NOW()"];
	
	$log_file = fopen ("data/useractionlog.txt","a+");
	$save_data = convert_data_for_log($current_date).": пользователь по адресу ".$user_ip." произвел попытку входа в панель администрирования.<br>\n";
	
	if ( !$log_file )
	{
		echo("Ошибка открытия файла");
	}
	else
	{
		fputs ( $log_file, $save_data);
	}
	fclose ($log_file);
}

?>
