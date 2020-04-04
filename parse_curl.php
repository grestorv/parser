<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	include "parse_model_curl.php";

	$ch[]=[];
	$mh=curl_multi_init();
	$numberOfPages=5;
	$listOfUrls=array();
	for($i=0;$i<$numberOfPages;$i++){
		$ch[$i]=curl_init();
		curl_setopt($ch[$i], CURLOPT_URL, "https://monro24.by/catalog.php?p=$i");
		curl_setopt($ch[$i], CURLOPT_HEADER, 0);
		curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch[$i], CURLOPT_CONNECTTIMEOUT, 30);
		curl_multi_add_handle($mh, $ch[$i]);
	}

/*	$running=null;
	while( ($mrc = curl_multi_exec($mh, $running))==CURLM_CALL_MULTI_PERFORM );
	while($running && $mrc == CURLM_OK){
		if($running and curl_multi_select($mh)!=-1 ){
			do{
				$mrc = curl_multi_exec($mh, $running);
				if( $info=curl_multi_info_read($mh) and $info['msg'] == CURLMSG_DONE ){
					$ch = $info['handle'];
					$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);
					$html=curl_multi_getcontent($info['handle']);
			 		$re='#<a href="model\.php\?id=(.*)" class="overlay" name="model.*?" id="model.*?">#';
					preg_match_all($re, $html, $m);
					$listOfUrls= array_merge($listOfUrls,$m[1]);
					/*foreach ($m[1] as $key => $value) {
						
						//echo $value;
						parseModel($value);
						echo '<br><br><br><br>';
					}*/
					/*
					curl_multi_remove_handle($mh, $ch);
					curl_close($ch);
				}
			}while ($mrc == CURLM_CALL_MULTI_PERFORM);
		}
		usleep(100);
	}
	var_dump($listOfUrls);*/

	/*v2
	$running=null;
	while( ($mrc = curl_multi_exec($mh, $running))==CURLM_CALL_MULTI_PERFORM );
	while($running && $mrc == CURLM_OK){
		if($running and curl_multi_select($mh)!=-1 ){
			do{
				$mrc = curl_multi_exec($mh, $running);
				if( $info=curl_multi_info_read($mh) and $info['msg'] == CURLMSG_DONE ){
					$ch = $info['handle'];
					$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);
					$html=curl_multi_getcontent($info['handle']);
			 		$re='#<a href="model\.php\?id=(.*)" class="overlay" name="model.*?" id="model.*?">#';
					preg_match_all($re, $html, $m);
					//$listOfUrls= array_merge($listOfUrls,$m[1]);
					$ch2=array();
					$mh2=curl_multi_init();
					foreach ($m[1] as $key => $value) {
						$ch2[$i]=curl_init();
						curl_setopt($ch2[$i], CURLOPT_URL, "https://monro24.by/model.php?id=$value");
						curl_setopt($ch2[$i], CURLOPT_HEADER, 0);
						curl_setopt($ch2[$i], CURLOPT_RETURNTRANSFER, 1);
						curl_multi_add_handle($mh2, $ch2[$i]);
					}
					$running2=null;
					while( ($mrc2 = curl_multi_exec($mh2, $running2))==CURLM_CALL_MULTI_PERFORM );
					while($running2 && $mrc2 == CURLM_OK){
						if($running2 and curl_multi_select($mh2)!=-1 ){
							do{
								$mrc2 = curl_multi_exec($mh2, $running2);
								if( $info2=curl_multi_info_read($mh2) and $info2['msg'] == CURLMSG_DONE ){
									$ch2 = $info2['handle'];
									$status=curl_getinfo($ch2,CURLINFO_HTTP_CODE);
									$id=curl_getinfo($info2['handle'], CURLINFO_EFFECTIVE_URL);//https://monro24.by/model.php?id=12274759569
									$id=preg_replace('#https://monro24\.by/model\.php\?id=#','', $id);
									echo $id.' ';
									$html=curl_multi_getcontent($info2['handle']);
									parseModel($html,$id);
									curl_multi_remove_handle($mh2, $ch2);
									curl_close($ch2);
								}
							} while ($mrc2 == CURLM_CALL_MULTI_PERFORM);///неправильно вложен цикл

						}
						usleep(100);
					}
					curl_multi_remove_handle($mh, $ch);
					curl_close($ch);
				}
			}while ($mrc == CURLM_CALL_MULTI_PERFORM);
		}
		usleep(100);
	}*/




	$running=null;
	while( ($mrc = curl_multi_exec($mh, $running))==CURLM_CALL_MULTI_PERFORM );
	while($running && $mrc == CURLM_OK){
		if($running and curl_multi_select($mh)!=-1 ){
			do{
				$mrc = curl_multi_exec($mh, $running);
				if( $info=curl_multi_info_read($mh) and $info['msg'] == CURLMSG_DONE ){
					$ch = $info['handle'];
					$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);
					$html=curl_multi_getcontent($info['handle']);
			 		$re='#<a href="model\.php\?id=(.*)" class="overlay" name="model.*?" id="model.*?">#';
					preg_match_all($re, $html, $m);
					downloadPage($m[1]);

					curl_multi_remove_handle($mh, $ch);
					curl_close($ch);
				}
			}while ($mrc == CURLM_CALL_MULTI_PERFORM);
		}
		usleep(100);
	}

	function downloadPage($m){

		$ch=array();
		$mh=curl_multi_init();
		foreach ($m as $key => $value) {
			$ch[$value]=curl_init();
			curl_setopt($ch[$value], CURLOPT_URL, "https://monro24.by/model.php?id=$value");
			curl_setopt($ch[$value], CURLOPT_HEADER, 0);
			curl_setopt($ch[$value], CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt($ch[$value], CURLOPT_CONNECTTIMEOUT, 30);
			curl_multi_add_handle($mh, $ch[$value]);
		}

		$running=null;
		while( ($mrc = curl_multi_exec($mh, $running))==CURLM_CALL_MULTI_PERFORM );
		while($running && $mrc == CURLM_OK){
			if($running and curl_multi_select($mh)!=-1 ){
				do{
					$mrc = curl_multi_exec($mh, $running);
					if( $info=curl_multi_info_read($mh) and $info['msg'] == CURLMSG_DONE ){
						$ch = $info['handle'];
						$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);
						$html=curl_multi_getcontent($info['handle']);
						$url=curl_getinfo($info['handle'], CURLINFO_EFFECTIVE_URL);
						echo $url.'<br>';
						$id=curl_getinfo($info['handle'], CURLINFO_EFFECTIVE_URL);//https://monro24.by/model.php?id=12274759569
						$id=preg_replace('#https://monro24\.by/model\.php\?id=#','', $id);
						parseModel($html, $id);
						curl_multi_remove_handle($mh, $ch);
						curl_close($ch);
					}
				}while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
			usleep(100);
		}
	}
	/*

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
	} while($curRunning>0);*/

	curl_multi_close($mh);
	//curl_multi_close($mh2);

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