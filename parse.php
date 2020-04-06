<?php
	include "db_init.php";
	include "parse_model.php";
	
	$db_inserts=[];
	$mh=curl_multi_init();
	$numberOfPages=17;

	for($i=1;$i<=17; $i++){
		//настраиваем соединение к каталогу с моделями
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://monro24.by/catalog.php?p=$i");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$html = curl_exec($ch);
		//выделяем из каталога отдельные страницы с моеделями
		$re='#<a href="model\.php\?id=(.*)" class="overlay" name="model.*?" id="model.*?">#';
		preg_match_all($re, $html, $m);
		$a=downloadPage($m[1]);//подаем массив страниц с моделями функции, получая массив запросов вставки в бд
		$db_inserts= array_merge($db_inserts,$a);//объединяем его с основным запросос вставки

		curl_close($ch);
	}

	$db_inserts=array_unique($db_inserts);//
	$db_inserts=array_diff($db_inserts, array(''));//удаляем повторяющиеся модели
	$query="INSERT INTO data(id, brand, model, size, season, kit, material, color, height, photos, price) VALUES ";//формируем запрос к бд
	$numberOfInserts=1000;
	for($i=0;$i<$numberOfInserts;$i++){
		if(isset($db_inserts[$i])){
			$query.=$db_inserts[$i];
		}
		else {
			$numberOfInserts++;
		}
	}
	$query=preg_replace('#,$#', '', $query);//удаляем последнюю запятую
	$result=mysqli_query($link,$query) or die(mysqli_error($link));
	//Необходимое условие
	echo 'Done';
	function downloadPage($m){//функция асинхронной загрузки страниц, для последующего парсинга

		$db_inserts=[];
		$max_connection=5;//устанавливаем количество одновременных потоков
		$ch=array();
		$mh=curl_multi_init();
		for($i=0; $i<$max_connection; $i++){
			add_url_to_multi_handle($mh, $m);
		}

		$running=null;
		while( ($mrc = curl_multi_exec($mh, $running))==CURLM_CALL_MULTI_PERFORM );//реализуем многопоточный парсинг
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
							$id=curl_getinfo($info['handle'], CURLINFO_EFFECTIVE_URL);
							$id=preg_replace('#https://monro24\.by/qmodel\.php\?id=#','', $id);
							$db_inserts[]= parseModel($html, $id, $db_inserts);
						}
						else {
							$m[]=$url;

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
		add_url_to_multi_handle($mh, 0);//обнуляем индекс внутри функции
		return $db_inserts;
	}

function add_url_to_multi_handle($mh, $url_list) {
	static $index = 0;
 	if($url_list!=0){
		if (isset($url_list[$index])) {
			// все как обычно
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://monro24.by/qmodel.php?id=".$url_list[$index]);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			curl_multi_add_handle($mh, $ch);
			$index++;
		}
	}
	else $index=0;
}
?>