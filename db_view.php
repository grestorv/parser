<?php
	include "db_init.php";
	$i=0;
	$query="SELECT * FROM data";
	$result=mysqli_query($link,$query) or die(mysqli_error($link));
	for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
	foreach ($data as $key => $value) {
		$i++;
		var_dump($value);
		echo '<br>';
	}
	$query = "TRUNCATE data";
	mysqli_query($link,$query) or die(mysqli_error($link));
?>