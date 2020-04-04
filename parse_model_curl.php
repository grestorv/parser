<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	$host = 'localhost'; //имя хоста, на локальном компьютере это localhost
	$user = 'root'; //имя пользователя, по умолчанию это root
	$password = ''; //пароль, по умолчанию пустой
	$db_name = 'user334'; //имя базы данных
	$link = mysqli_connect($host,$user,$password,$db_name);
	mysqli_query($link,"SET NAMES 'utf8'");
	set_time_limit(600);

	function parseQuery($regQuery, $html){
		preg_match_all($regQuery, $html, $m);
		$m=$m[1][0];
		echo $m.'<br>';
		return $m;
	}
	function parseImages($html){
		$re='#<div class="gallery-image">(.*?)</div>#';
		preg_match_all($re, $html, $m);
		$photos=[];
		foreach ($m[1] as $key => $value) {
			//$re='#<a href="(.*?)"#';data-big-image
			$re='#<a .*? data-big-image="(.*?)"#';
			preg_match_all($re, $value, $m);
			echo $m[1][0];
			$photos[]=$m[1][0];
			echo '<br>';
		}
		return $photos;
	}

	function parseModel($html,$id){

		include "db_init.php";
		echo '<br>2222222222222<br>';
		$model=parseQuery('#<span class="model-SKU"><b>Артикул:</b> (.*)</span>#', $html);
		$price=parseQuery('#<div class="current-cost"><span class="number">(.*?)</span>#su', $html);

		$re='#<div class="input-select-block select-size">(.*?)</div>#su';
		preg_match_all($re, $html, $m);
		$tempHtml=$m[0][0];
		$re='#<a href class="input-select-item(?: active)?" data-input-value=".*?" data-input-name="size">(.*?)</a>#su';
		preg_match_all($re, $tempHtml, $m);
		$sizes=$m[1];
		$sizes=implode(',', $sizes).'<br>';
		echo $sizes;

		$kit=parseQuery('#<div><b>Комплектация:</b>\s*(.*?)</div>#su', $html);
		$color='';
		$brand=parseQuery('#<div><b>Бренд:</b>\s*(.*?)</div>#su', $html);
		$height=parseQuery('#<div><b>Рост:</b>\s*(.*?)</div>#su', $html);
		if(!isset($height)) {
			$height=0; echo $height;
		}
		$material=parseQuery('#<div><b>Состав:</b>\s*(.*?)</div>#su', $html);
		$season=parseQuery('#<div><b>Сезон:</b>\s*(.*?)</div>#su', $html);
		$photos=parseImages($html);
		$photos=implode(',', $photos);
		//echo $photos;

		$query = "INSERT INTO data SET id=$id, brand='$brand', model='$model', size='$sizes', season='$season', kit='$kit', material='$material', color='$color', height='$height', photos='$photos', price=$price";
		$result=mysqli_query($link,$query) or die(mysqli_error($link));
	}
?>