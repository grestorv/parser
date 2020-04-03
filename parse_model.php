<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	
	$id='20256612';
	$url="https://monro24.by/model.php?id=$id";
	$modUrl='model.php?id=$id';
	$html=file_get_contents($url);

	/*$re='#<h1 class="model-title" itemprop="name">(.*)#';
	preg_match_all($re, $html, $m, PREG_SET_ORDER);
	$model_name=$m[0][1];
	echo $model_name;*/

	$re='#<span class="model-SKU"><b>Артикул:</b> (.*)</span>#';
	preg_match_all($re, $html, $m);
	$model=$m[1][0];
	echo $model.'<br>';

	$re='#<div class="current-cost"><span class="number">(.*?)</span>#su';
	preg_match_all($re, $html, $m);
	$model=$m[1][0];
	echo $model.'<br>';

	$re='#<div class="input-select-block select-size">(.*?)</div>#su';
	preg_match_all($re, $html, $m);
	$tempHtml=$m[0][0];
	$re='#<a href class="input-select-item(?: active)?" data-input-value=".*?" data-input-name="size">(.*?)</a>#su';
	preg_match_all($re, $tempHtml, $m);
	$sizes=$m[1];
	//var_dump($sizes);
	$sizes=implode(',', $sizes).'<br>';
	echo $sizes;

	$re="#<div><b>Комплектация:</b>\s+(.*?)</div>#u";
	preg_match_all($re, $html, $m);
	$kit=$m[1][0];
	echo($kit.'<br>');

	$re="#<div><b>Бренд:</b>\s+(.*?)</div>#u";
	preg_match_all($re, $html, $m);
	$brand=$m[1][0];
	echo($brand.'<br>');
	
	$re="#<div><b>Рост:</b>\s+(.*?)</div>#u";
	preg_match_all($re, $html, $m);
	$height=$m[1][0];
	echo($height.'<br>');

	$re="#<div><b>Состав:</b>\s+(.*?)</div>#u";
	preg_match_all($re, $html, $m);
	$material=$m[1][0];
	echo($material.'<br>');

	$re="#<div><b>Сезон:</b>\s+(.*?)</div>#u";
	preg_match_all($re, $html, $m);
	$season=$m[1][0];
	echo($season.'<br>');

	//Парсинг картинок
	function parseImages($html){
		$re='#<div class="gallery-image">(.*?)</div>#';
		preg_match_all($re, $html, $m);
		foreach ($m[1] as $key => $value) {
			$re='#<a href="(.*?)"#';
			preg_match_all($re, $value, $l);
			echo $l[1][0];
			echo '<br>';
		}
	}

	$re='#<a class="color-item" href="(model\.php\?id=.*?)" data-input-value=".*?">#u';
	preg_match_all($re, $html, $m);
	$m[1][]="model.php?id=$id";
	foreach ($m[1] as $key => $value) {
		//var_dump($value);
		//echo '<br>';
		$url="https://monro24.by/$value";
		$html=file_get_contents($url);
		parseImages($html);
	}

?>