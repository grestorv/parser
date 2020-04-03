<?php
// URL-адреса страниц, которые надо скачать
$pages = array();
for($i=1;$i<=15;$i++){
	$pages[]="https://monro24.by/catalog.php?p=$i";
	var_dump($pages);
	echo '<br>';
}
var_dump($pages);
// инициализируем "контейнер" мультизапросов (мультикурл)
$multi_init = curl_multi_init();

// массив отдельных заданий
$job = array();

// проходим по каждому URL-адресу
foreach ($pages as $page) {

	// подключаем отдельный поток (URL-адрес)
	$init = curl_init($page);

	// если произойдёт перенаправление, то перейти по нему
	curl_setopt($init, CURLOPT_FOLLOWLOCATION, 1);

	// curl_exec вернёт результат
	curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);

	// таймаут соединения 10 секунд
	curl_setopt($init, CURLOPT_CONNECTTIMEOUT, 10);

	// таймаут ожидания также 10 секунд
	curl_setopt($init, CURLOPT_TIMEOUT, 10);

	// HTTP-заголовок ответа не будет возвращён
	curl_setopt($init, CURLOPT_HEADER, 0);

	// добавляем дескриптор потока в массив заданий
	$job[$page] = $init;

	// добавляем дескриптор потока в мультикурл
	curl_multi_add_handle($multi_init, $init);

}

// кол-во активных потоков
$thread = null;

// запускаем исполнение потоков
do {
	$thread_exec = curl_multi_exec($multi_init, $thread);
}
while ($thread_exec == CURLM_CALL_MULTI_PERFORM);

// исполняем, пока есть активные потоки
while ($thread && ($thread_exec == CURLM_OK)) {

	// если поток готов к взаимодествию
	if (curl_multi_select($multi_init) != -1) {

		// ждем, пока что-нибудь изменится
		do {

			$thread_exec = curl_multi_exec($multi_init, $thread);

			// читаем информацию о потоке
			$info = curl_multi_info_read($multi_init);

			// если поток завершился
			if ($info['msg'] == CURLMSG_DONE) {

				$init = $info['handle'];

				// ищем URL страницы по дескриптору потока в массиве заданий
				$page = array_search($init, $job);

				// скачиваем содержимое страницы
				$job[$page] = curl_multi_getcontent($init);
				echo strip_tags($job[$page]).'<br><br><br><br><br><br><br><br><br><br>';
				// удаляем поток из мультикурла
				curl_multi_remove_handle($multi_init, $init);

				// закрываем отдельный поток
				curl_close($init);

			}
		}
		while ($thread_exec == CURLM_CALL_MULTI_PERFORM);
	}
}

// закрываем мультикурл
curl_multi_close($multi_init);
?>