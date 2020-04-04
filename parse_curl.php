<?php

	include "parse_model_curl.php";

	$ch[]=[];
	$mh=curl_multi_init();
	$numberOfPages=17;
	$listOfUrls=array();
	for($i=1;$i<=$numberOfPages;$i++){
		$listOfUrls[]="https://monro24.by/catalog.php?p=$i";
	}
	/*for($i=0;$i<$numberOfPages;$i++){
		$ch[$i]=curl_init();
		curl_setopt($ch[$i], CURLOPT_URL, "https://monro24.by/catalog.php?p=$i");
		curl_setopt($ch[$i], CURLOPT_HEADER, 0);
		curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch[$i], CURLOPT_CONNECTTIMEOUT, 30);
		curl_multi_add_handle($mh, $ch[$i]);
	}*/
	for($i=0; $i<5; $i++){
		add_url_to_multi_handle2($mh, $listOfUrls);
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
			 		$re='#<a href="model\.php\?id=(.*)" class="overlay" name="model.*?" id="model.*?">#';
					preg_match_all($re, $html, $m);
					$url_list=$m[1];
					echo '1111111111111';
					downloadPage($m[1]);
					add_url_to_multi_handle($mh, 0);
					curl_multi_remove_handle($mh, $ch);
					curl_close($ch);
					add_url_to_multi_handle2($mh, $listOfUrls);
				}
				
			}while ($mrc == CURLM_CALL_MULTI_PERFORM);
		}
		
		usleep(100);
	}
	//Необходимое условие
	echo 'Done';
	function downloadPage($m){


		$max_connection=8;
		$ch=array();
		$mh=curl_multi_init();
		/*foreach ($m as $key => $value) {
			$ch[$value]=curl_init();
			curl_setopt($ch[$value], CURLOPT_URL, "https://monro24.by/model.php?id=$value");
			curl_setopt($ch[$value], CURLOPT_HEADER, 0);
			curl_setopt($ch[$value], CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt($ch[$value], CURLOPT_CONNECTTIMEOUT, 30);
			curl_multi_add_handle($mh, $ch[$value]);
		}*/
		for($i=0; $i<$max_connection; $i++){
			add_url_to_multi_handle($mh, $m);
		}

		$running=null;
		while( ($mrc = curl_multi_exec($mh, $running))==CURLM_CALL_MULTI_PERFORM );
		while($running && $mrc == CURLM_OK){
			if($running and curl_multi_select($mh)!=-1 ){
				do{
					$mrc = curl_multi_exec($mh, $running);
					if( $info=curl_multi_info_read($mh) and $info['msg'] == CURLMSG_DONE ){
						$ch = $info['handle'];
						$url=curl_getinfo($info['handle'], CURLINFO_EFFECTIVE_URL);
						$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);
						if($status=='200'){
							$html=curl_multi_getcontent($info['handle']);
							//echo $url.'<br>';
							$id=curl_getinfo($info['handle'], CURLINFO_EFFECTIVE_URL);//https://monro24.by/model.php?id=12274759569
							$id=preg_replace('#https://monro24\.by/qmodel\.php\?id=#','', $id);
							parseModel($html, $id);
						}
						else {
							$listOfUrls[]=$url;
							echo 'Status: '.$status;

						}
						curl_multi_remove_handle($mh, $ch);
						curl_close($ch);
						add_url_to_multi_handle($mh, $m);
					}
				}while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
			usleep(100);
		}
		curl_multi_close($mh);
	}
	curl_multi_close($mh);
function add_url_to_multi_handle($mh, $url_list) {
	static $index = 0;
 	if($url_list!=0){
		if (isset($url_list[$index])) {
			// все как обычно
			$ch = curl_init();
	 
			// устанавливаем опции
			curl_setopt($ch, CURLOPT_URL, "https://monro24.by/qmodel.php?id=".$url_list[$index]);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	 
			// добавляем к мульти-дескриптору
			curl_multi_add_handle($mh, $ch);
	 		echo "https://monro24.by/model.php?id=".$url_list[$index];
			$index++;
		}
	}
	else $index=0;
}
function add_url_to_multi_handle2($mh, $url_list) {
	static $index = 0;
 	if($url_list!=0){
		if (isset($url_list[$index])) {
			// все как обычно
			$ch = curl_init();
	 
			// устанавливаем опции
			curl_setopt($ch, CURLOPT_URL, $url_list[$index]);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	 
			// добавляем к мульти-дескриптору
			curl_multi_add_handle($mh, $ch);
	 		echo $url_list[$index];
			$index++;
		}
	}
	else $index=0;
}

?>