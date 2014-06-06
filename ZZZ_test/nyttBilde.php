<?php

	session_start();

	include '../sql/db.php';
	include '../user/albumFunctions.php';
	include '../user/userFunctions.php';

	/*$user = new user();
	$user -> addUserById(1);*/

	$image = new album();
	$image -> setAlbumId(1);
	$dest = $image -> addImage('1', 'image/png');

	if($dest == false){
		echo 'false';
	}else{
		/*file_put_contents(
			$dest,
			file_get_contents('php://input')
		);*/

		$key = $_SESSION['key'];

		$test = fopen($dest . '.txt', 'w');
		fwrite($test, 'TestingTesting');
		fclose($test);
		echo "<img src='../user/image.php?path=$dest&key=$key' width='400px'>";

	}

echo "<br>Heu";