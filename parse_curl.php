<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	include "parse_model_curl.php";

	$ch[]=[];
	$mh=curl_multi_init();
	for($i=0;$i<=16;$i++){
		$ch[$i]=curl_init();
		curl_setopt($ch[$i], CURLOPT_URL, 'https://monro24.by/catalog.php?p=$i');
		curl_setopt($ch[$i], CURLOPT_HEADER, 0);
		curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, 1);
		curl_multi_add_handle($mh, $ch[$i]);
	}

	$running=null;
	do{
		curl_multi_exec($mh, $curRunning);
		if ($curRunning != $running) {
			$mhinfo = curl_multi_info_read($mh);
		 
			if (is_array($mhinfo) && ($ch = $mhinfo['handle'])) {
				// 3. один из запросов выполнен, можно получить информацию о нем
				$info = curl_getinfo($ch);
		

				$working_urls[] = $info['url'];
		 		$html=curl_multi_getcontent($ch);
		 		$re='#<a href="model\.php\?id=(.*)" class="overlay" name="model.*?" id="model.*?">#';
				preg_match_all($re, $html, $m);
				foreach ($m[1] as $key => $value) {
					
					//echo $value;
					parseModel($value);
					echo '<br><br><br><br>';
				}
				curl_multi_remove_handle($mh, $mhinfo['handle']);
				curl_close($mhinfo['handle']);
		 
				// 8. добавим новый урл
				//add_url_to_multi_handle($mh, $url_list);
				$running = $curRunning;
			}	
		}
	} while($curRunning>0);

	curl_multi_close($mh);

	/*$numberOfPages=1;
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
	**/

?>