<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	include "parse_model.php";
	set_time_limit(10000);

	$numberOfPages=17;
	for($i=1;$i<=$numberOfPages;$i++){
		$url="https://monro24.by/catalog.php?p=$i";
		$html2=file_get_contents($url);
		$re='#<a href="model\.php\?id=(.*)" class="overlay" name="model.*?" id="model.*?">#';
		//$re='#<div data-href="(qmodel\.php\?id=.*)" class="preview"#';
		preg_match_all($re, $html2, $m);
		//foreach ($m[1] as $key => $value) {
		for ($i=0; $i <5 ; $i++) { 
			$value=$m[1][$i];
			echo $value;
			parseModel($value);
			echo '<br>';
		}
	}

	$site='monro24.by/';


?>