<?
include_once ("includes.inc");
include_once ("settings.inc");
include_once ("excel.php");


if ( !isset($_POST['step']) )
	$_POST['step'] = 0;
?>
<html>
<head>

<STYLE>
<!--
body, table, tr, td {font-size: 12px; font-family: Verdana, MS sans serif, Arial, Helvetica, sans-serif}
td.index {font-size: 10px; color: #000000; font-weight: bold}
td.empty {font-size: 10px; color: #000000; font-weight: bold}
td.dt_string {font-size: 10px; color: #000090; font-weight: bold}
td.dt_int {font-size: 10px; color: #909000; font-weight: bold}
td.dt_float {font-size: 10px; color: #007000; font-weight: bold}
td.dt_unknown {font-size: 10px; background-color: #f0d0d0; font-weight: bold}
td.empty {font-size: 10px; background-color: #f0f0f0; font-weight: bold}
-->
</STYLE>
</head>
<body text="#000000" link="#000000" vlink="#000000" alink="#000000" topmargin="0" leftmargin="2" marginwidth="0" marginheight="0">

<table width="100%" align="center" bgcolor="#006699">
<tr>
	<td>&nbsp;</td>
	<td width="60%"><font color="#FFFFFF" size="+2">ABC Excel Parser Pro plugin</font></td>
	<td width="40%" align="right"><font color="#FFFFFF" size="+1">MS Excel->MySQL builder</font></td>
	<td>&nbsp;</td>
</tr>
</table>

<?php

// Outputting fileselect form (step 0)

if ( $_POST['step'] == 0 ){
?>
<table width="100%" border="0" align="center" bgcolor="#7EA9D3">
<tr>
<td>&nbsp;</td>
<td>
<p>&nbsp;</p>
Выбирете на своем локальном компьютере Excel файл
<p>&nbsp;</p>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>

<table border="0">
<form name="exc_upload" method="post" action="" enctype="multipart/form-data">

<tr><td>Excel файл:</td><td><input type="file" size=30 name="excel_file"></td></tr>
<tr><td>Использовать первую строку как имена полей:</td><td><input type="checkbox" name="useheaders"></td></tr>
<tr><td colspan="2" align="right">
<input type="hidden" name="step" value="1">
<input type="button" value="Дальше" onClick="
javascript:
if( (document.exc_upload.excel_file.value.length==0))
{ alert('Сначала Вы должны определить имя файла'); return; }; submit();
"></td></tr>


</form>
</table>

</td>
</tr>


<tr>
<td>&nbsp;</td>
<td align="right">
<p>&nbsp;</p>
<a href="http://www.zakkis.ca" style="font-size: 9px; text-decoration: none; font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;">ZAKKIS Tech. 2003  All Rights Reserved.</a>&nbsp;&nbsp;
</td>
</tr>
</table>

<?
}
// Обработка excel файла (шаг 1)
print 'Обработка excel файла (шаг 1)'.'</ br>' ;

if ($_POST['step'] == 1) {
	
	echo "<br>";
	
	// Загрузка файла
print 'Загрузка файла'.'</ br>' ;
	
	$excel_file = $_FILES['excel_file'];
	if( $excel_file )
		$excel_file = $_FILES['excel_file']['tmp_name'];

	if( $excel_file == '' ) fatal("Нет файла для загрузки1");
	
	move_uploaded_file( $excel_file, 'upload/' . $_FILES['excel_file']['name']);	
	$excel_file = 'upload/' . $_FILES['excel_file']['name'];
	
	
	$fh = @fopen ($excel_file,'rb');
	if( !$fh ) fatal("Нет файла для загрузки3");
	if( filesize($excel_file)==0 ) fatal("Нет файла для загрузки2");

	$fc = fread( $fh, filesize($excel_file) );
	@fclose($fh);
	if( strlen($fc) < filesize($excel_file) )
		fatal("Невозможно считать файл3");	
	
	
	// Проверка excel файла
print 'Проверка excel файла0-'.$exc.'</ br>' ;

	//$exc = new ExcelFileParser("debug.log", ABC_NO_LOG );
	$exc = new ExcelFileParser ();
print 'Проверка excel файла1-'.$exc.'</ br>' ;
	$res = $exc-> ParseFromFile ($fc); //ParseFromString($fc);
	print 'Проверка excel файла2'.'</ br>' ;
	switch ($res) {
		case 0: break;
		case 1: fatal("Невозможно открыть файл4");
		case 2: fatal("Файл, слишком маленький чтобы быть файлом Excel5");
		case 3: fatal("Ошибка чтения заголовка файла6");
		case 4: fatal("Ошибка чтения файла7");
		case 5: fatal("Это - не файл Excel или файл, сохраненный в Excel < 5.0");
		case 6: fatal("Битый файл");
		case 7: fatal("Ненайдены данные в Excel  файле");
		case 8: fatal("Нероддерживаемая версия файла");

		default:
			fatal("Неизвестная ошибка77");
	}
	
		
	// Обрабортка рабочего листа
print 'Обрабортка рабочего листа'.'</ br>' ;
	
	$ws_number = count($exc->worksheet['name']);
	if( $ws_number < 1 ) fatal("Не найден рабочий лист в Excel файле.");
	
	$ws_number = 1; // Установка, чтобы обработать только первый рабочий лист
	
	for ($ws_n = 0; $ws_n < $ws_number; $ws_n++) {
		
		$ws = $exc -> worksheet['data'][$ws_n]; // Получение данных из рабочего листа
			
		if ( !$exc->worksheet['unicode'][$ws_n] )
			$db_table = $ws_name = $exc -> worksheet['name'][$ws_n];
		else 	{
			$ws_name = uc2html( $exc -> worksheet['name'][$ws_n] );
			$db_table = convertUnicodeString ( $exc -> worksheet['name'][$ws_n] );
			}
		
		echo "<div align=\"center\">Рабочий лист: <b>$ws_name</b></div><br>";

		
		$max_row = $ws['max_row'];
		$max_col = $ws['max_col'];
		
		if ( $max_row > 0 && $max_col > 0 )
			getTableData ( &$ws, &$exc ); // Получение структуры и данных рабочего листа
		else fatal("Пустой рабочий лист");
		
	}
	
}

if ( $_POST['step'] == 2 ) { // вставка данных в mysql (шаг 2)
		
	echo "<br>";
	
	extract ($_POST);
		
	$db_table = ereg_replace ( "[^a-zA-Z0-9$]", "", $db_table );
	$db_table = ereg_replace ( "^[0-9]+", "", $db_table );
	
	if ( empty ( $db_table ) )
		$db_table = "Table1";
	
	// Проверка соединения с базой данных
	
	if ( !$link = @mysql_connect ($db_host, $db_user, $db_pass) )
        fatal("Ошибка при соединении с базой данных. Пожалуста проверьте конфигурационные установки.");
	
	if ( !$connect = mysql_select_db ($db_name ) )
        fatal("Неправильное имя базы данных.");
		
	if ( empty ($db_table) )
		fatal("Пустое имя таблицы.");
	
	if ( !isset ($fieldcheck) )
		fatal("Нет выбранных полей.");
	
	if ( !is_array ($fieldcheck) )
		fatal("ет выбранных полей.");
	
	$tbl_SQL .= "CREATE TABLE IF NOT EXISTS $db_table ( ";
	
	foreach ($fieldcheck as $fc)
		if ( empty ( $fieldname[$fc] ) )
			fatal("Пустое имя поля для выбранного поля $fc.");
		else {
			//Подготовка структуры таблицы
			
			$fieldname[$fc] = ereg_replace ( "[^a-zA-Z0-9$]", "", $fieldname[$fc] );
			$fieldname[$fc] = ereg_replace ( "^[0-9]+", "", $fieldname[$fc] );
			if ( empty ( $fieldname[$fc] ) )
					$fieldname[$fc] = "field" . $fc;
			
			$tbl_SQL .= $fieldname[$fc] . " text NOT NULL,";
			
		}
	
	$tbl_SQL = rtrim($tbl_SQL, ',');
	
	$tbl_SQL .= ") TYPE=MyISAM";

	
	$fh = @fopen ($excel_file,'rb');
	if( !$fh ) fatal("Невозможно загрузить файл");
	if( filesize($excel_file)==0 ) fatal("Невозможно загрузить файл");

	$fc = fread( $fh, filesize($excel_file) );
	@fclose($fh);
	if( strlen($fc) < filesize($excel_file) )
		fatal("Невозможно считать файл");		
	
	
	$exc = new ExcelFileParser;
	$res = $exc->ParseFromString($fc);
	
	switch ($res) {
		case 0: break;
		case 1: fatal("Невозможно открыть файл");
		case 2: fatal("Файл, слишком маленький чтобы быть файлом Excel");
		case 3: fatal("Ошибка чтения заголовка файла");
		case 4: fatal("Ошибка чтения файла");
		case 5: fatal("Это - не файл Excel или файл, сохраненный в Excel < 5.0");
		case 6: fatal("Битый файл");
		case 7: fatal("Не найдены данные в Excel файле");
		case 8: fatal("Неподдерживаемая версия файла");

		default:
			fatal("Неизвестная ошибка");
	}
	
	// Подготовка рабочего листа
	
	$ws_number = count($exc->worksheet['name']);
	if( $ws_number < 1 ) fatal("Нет рабочего листа в Excel файле.");
	
	$ws_number = 1; // Установлено, чтобы обработать только первый рабочий лист
	
	for ($ws_n = 0; $ws_n < $ws_number; $ws_n++) {
		
		$ws = $exc -> worksheet['data'][$ws_n]; // Получение данных рабочего листа
			
		$max_row = $ws['max_row'];
		$max_col = $ws['max_col'];
		
		if ( $max_row > 0 && $max_col > 0 )
			$SQL = prepareTableData ( &$exc, &$ws, $fieldcheck, $fieldname );
		else fatal("Пустой рабочий лист");
		
	}
	
		
	if (empty ( $SQL ))
		fatal("Ошибка вывода в таблицу");


	// Выходные данные в базу данных
	
	
	// Уничтожение таблицы
	
	if ( isset($db_drop) ) {
	
		$drop_tbl_SQL = "DROP TABLE IF EXISTS $db_table";
		
		if ( !mysql_query ($drop_tbl_SQL) )
			fatal ("Ошибка при удалении таблицы");
	
	}
	
	//Создание таблицы
	
	if ( !mysql_query ($tbl_SQL) )
		fatal ("Ошибка при создании таблицы");
	
	$sql_pref = "INSERT INTO " . $db_table . " SET ";
	
	$err = "";	
	$nmb = 0; // число вставленных строк
	
	foreach ( $SQL as $sql ) {
	
		$sql = $sql_pref . $sql;
		
		if ( !mysql_query ($sql) ) {
		$err .= "<b>SQL ошибка в</b> :<br>$sql <br>";
			
		}
		else $nmb++;
			
	}
	
	if ( empty ($err) ) {
		echo <<<SUCC
		<br><br>
		<div align="center">
		<b>Операции Вывода, обработанны успешно.</b><br><br>
		$nmb строк(и), вставленны в таблицу "$db_table"<br>
		<br><a href="">Начать</a>
		</div>
SUCC;
	}
	else 	echo "<br><br><font color=\"red\">$err</font><br><br><div align=\"center\"><a href=\"\">Начать</a></div>";
	
	@unlink ($excel_file);

	echo <<<ZAKKIS
	
	<br><br>
	<div align="right">
	<a href="http://www.zakkis.ca" style="font-size: 9px; text-decoration: none; font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;">ZAKKIS Tech. 2003  All Rights Reserved.</a>&nbsp;&nbsp;
	</div>
	
ZAKKIS;
	
}		
		
?>