<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'off');
	$host = 'localhost'; //имя хоста, на локальном компьютере это localhost
	$user = 'user334'; //имя пользователя, по умолчанию это root
	$password = 'Ibuwe53466#'; //пароль, по умолчанию пустой
	$db_name = 'user334'; //имя базы данных
	$link = mysqli_connect($host,$user,$password,$db_name);
	mysqli_query($link,"SET NAMES 'utf8'");

?>