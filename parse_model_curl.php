<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
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
			$re='#<a href="(.*?)"#';
			preg_match_all($re, $value, $m);
			echo $m[1][0];
			$photos[]=$m[1][0];
			echo '<br>';
		}
		return $photos;
	}

	function parseModel($html,$id){

		/*$url="https://monro24.by/model.php?id=$id";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$html = curl_exec($ch);
		curl_close($ch);*/

		/*$url="https://monro24.by/model.php?id=$id";
		$html=file_get_contents($url);*/
		//echo "<br>$id<br>";
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

		$kit=parseQuery('#<div><b>Комплектация:</b>\s*(.*?)</div>#su', $html);
		$brand=parseQuery('#<div><b>Бренд:</b>\s*(.*?)</div>#su', $html);
		$height=parseQuery('#<div><b>Рост:</b>\s*(.*?)</div>#su', $html);
		$material=parseQuery('#<div><b>Состав:</b>\s*(.*?)</div>#su', $html);
		$season=parseQuery('#<div><b>Сезон:</b>\s*(.*?)</div>#su', $html);


		//Парсинг картинок

		/*$re='#<a class="color-item" href="(model\.php\?id=.*?)" data-input-value=".*?">#u';
		preg_match_all($re, $html, $m);
		$m[1][]="model.php?id=$id";
		$photos=[];

		$ch=array();
		$mh=curl_multi_init();
		foreach ($m[1] as $key => $value) {
			echo $value;
			$ch[$value]=curl_init();
			curl_setopt($ch[$value], CURLOPT_URL, "https://monro24.by/$value");
			curl_setopt($ch[$value], CURLOPT_HEADER, 0);
			curl_setopt($ch[$value], CURLOPT_RETURNTRANSFER, 1);
			curl_multi_add_handle($mh, $ch[$value]);
		}


		$running=null;
		//просто запускаем все соединения
		while( ($mrc = curl_multi_exec($mh, $running))==CURLM_CALL_MULTI_PERFORM );
		while($running && $mrc == CURLM_OK){
			if($running and curl_multi_select($mh)!=-1 ){
				do{
				$mrc = curl_multi_exec($mh, $running);
				// если поток завершился
					if( $info=curl_multi_info_read($mh) and $info['msg'] == CURLMSG_DONE ){
						$ch = $info['handle'];
						// смотрим http код который он вернул
						$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);
						$html=curl_multi_getcontent($info['handle']);
						$photos=array_merge($photos,parseImages($html));
						curl_multi_remove_handle($mh, $ch);
						curl_close($ch);
					}
				}while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		  usleep(100);
		}


		curl_multi_close($mh);
		var_dump($photos);*/


		/*v1
		$running=null;
		do{
			curl_multi_exec($mh, $curRunning);
			if ($curRunning != $running) {
				$mhinfo = curl_multi_info_read($mh);
			 
				if (is_array($mhinfo) && ($ch = $mhinfo['handle'])) {
					
					$info = curl_getinfo($ch);
					$working_urls[] = $info['url'];
			 		$html=curl_multi_getcontent($ch);
					$photos=array_merge($photos,parseImages($html));
					curl_multi_remove_handle($mh, $mhinfo['handle']);
					curl_close($mhinfo['handle']);

					// 8. добавим новый урл
					//add_url_to_multi_handle($mh, $url_list);
					$running = $curRunning;
				}	
			}
		} while($curRunning>0);*/

		/*foreach ($m[1] as $key => $value) {
			//переделать в многопоток
			$url="https://monro24.by/$value";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$html = curl_exec($ch);
			curl_close($ch);

			$photos=array_merge($photos,parseImages($html));
		}*/
	}
?>