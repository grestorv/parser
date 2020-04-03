<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	for($i=1;$i<=17;$i++){
		$url="https://monro24.by/catalog.php?p=$i";
		$html=file_get_contents($url);
		$re='#<a href="(model\.php\?id=.*)" class="overlay" name="model.*?" id="model.*?">#';
		//$re='#<div data-href="(qmodel\.php\?id=.*)" class="preview"#';
		preg_match_all($re, $html, $m, PREG_SET_ORDER);
		foreach ($m as $key => $value) {
			var_dump($value);
			echo '<br>';
		}
	}

	$site='monro24.by/';


?>