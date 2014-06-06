<?php

	session_start();

	require '../class/dbClass.php';
	require '../class/userClass.php';
	require '../class/albumClass.php';

	//Create dependencies
	$db = new db();
	$user = new user($db);

	//Create album-class
	$values = array('id' => 20);
	$album = new album($values, $user, $db);

	echo $album -> addImage(1, 'image/png');

	echo $tblName['pict'];


	/*$user = new user();
	$user -> addUserById(1);*/

	//$image = new album();
	//$image -> setAlbumId(1);
	//$dest = $image -> addImage('1', 'image/png');

	//if($dest == false){
		//echo 'false';
	//}else{
		/*file_put_contents(
			$dest,
			file_get_contents('php://input')
		);*/

		$key = $_SESSION['key'];

		//$test = fopen($dest . '.txt', 'w');
		//fwrite($test, 'TestingTesting');
		//fclose($test);
		//echo "<img src='../user/image.php?path=$dest&key=$key' width='400px'>";

	//}

echo "<br>Heu";