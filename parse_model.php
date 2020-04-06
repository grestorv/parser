<?php
	include "db_init.php";
	set_time_limit(600);

	function parseQuery($regQuery, $html){
		preg_match_all($regQuery, $html, $m);
		$m=$m[1][0];
		return $m;
	}
	function parseImages($html){
		$re='#<div class="gallery-image">(.*?)</div>#';
		preg_match_all($re, $html, $m);
		$photos=[];
		foreach ($m[1] as $key => $value) {
			$re='#<a .*? data-big-image="(.*?)"#';
			preg_match_all($re, $value, $m);

			$photos[]=$m[1][0];

		}
		return $photos;
	}

	function parseModel($html,$id, $db_inserts){

		$model=parseQuery('#<span class="model-SKU"><b>Артикул:</b> (.*?)(\s.*?)?</span>#u', $html);
		$color='';
		$color=parseQuery('#<span class="model-SKU"><b>Артикул:</b> .*?(\s.*?)?</span>#u', $html);
		$price=parseQuery('#<div class="current-cost"><span class="number">(.*?)</span>#su', $html);

		$re='#<div class="input-select-block select-size">(.*?)</div>#su';
		preg_match_all($re, $html, $m);
		$tempHtml=$m[0][0];
		$re='#<a href class="input-select-item(?: active)?" data-input-value=".*?" data-input-name="size">(.*?)</a>#su';
		preg_match_all($re, $tempHtml, $m);
		$sizes=$m[1];
		$sizes=implode(',', $sizes);

		$kit=parseQuery('#<div><b>Комплектация:</b>\s*(.*?)</div>#su', $html);

		$brand=parseQuery('#<div><b>Бренд:</b>\s*(.*?)</div>#su', $html);
		$height=parseQuery('#<div><b>Рост:</b>\s*(.*?)</div>#su', $html);
		if(!isset($height)) {
			$height=0;
		}
		$material=parseQuery('#<div><b>Состав:</b>\s*(.*?)</div>#su', $html);
		$season=parseQuery('#<div><b>Сезон:</b>\s*(.*?)</div>#su', $html);
		$photos=parseImages($html);
		$photos=implode(',', $photos);

		return "($id, '$brand', '$model', '$sizes', '$season', '$kit', '$material', '$color', '$height', '$photos', $price),";
	}
?>