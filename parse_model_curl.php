<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	

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
			$re='#<a href="(.*?)"#';
			preg_match_all($re, $value, $m);
			echo $m[1][0];
			$photos[]=$m[1][0];
			echo '<br>';
		}
		return $photos;
	}

	function parseModel($id){
		$url="https://monro24.by/model.php?id=$id";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$html = curl_exec($ch);
		curl_close($ch);

		/*$url="https://monro24.by/model.php?id=$id";
		$html=file_get_contents($url);*/
		echo "<br>$id<br>";
		/*$re='#<h1 class="model-title" itemprop="name">(.*)#';
		preg_match_all($re, $html, $m, PREG_SET_ORDER);
		$model_name=$m[0][1];
		echo $model_name;*/

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

		$kit=parseQuery('#<div><b>Комплектация:</b>\s*(.*?)</div>#u', $html);
		$brand=parseQuery('#<div><b>Бренд:</b>\s*(.*?)</div>#u', $html);
		$height=parseQuery('#<div><b>Рост:</b>\s*(.*?)</div>#u', $html);
		$material=parseQuery('#<div><b>Состав:</b>\s*(.*?)</div>#u', $html);
		$season=parseQuery('#<div><b>Сезон:</b>\s*(.*?)</div>#u', $html);


		//Парсинг картинок

		$re='#<a class="color-item" href="(model\.php\?id=.*?)" data-input-value=".*?">#u';
		preg_match_all($re, $html, $m);
		$m[1][]="model.php?id=$id";
		$photos=[];
		foreach ($m[1] as $key => $value) {
			//переделать в многопоток
			$url="https://monro24.by/$value";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$html = curl_exec($ch);
			curl_close($ch);

			$photos=array_merge($photos,parseImages($html));
		}
		var_dump($photos);
	}
?>