<?php
	//Databaseoppkobling
	require '../class/dbClass.php';
	require '../class/albumClass.php';
	require '../class/userClass.php';

	//Create dependencies
	$db = new db();
	$user = new user($db);
	$value = array('id' => 1);

	//Create album-class
	$album = new album($value, $user, $db);

	//Try setting code
	$code = $album -> setShareCode();

	if($code == false){
		echo "Auda";
	}else{
		echo $code;
		echo "<a href='../index.php?code=".$code."'>Link</a>";
	}

	/*$string = "1BUjWlxnW";
	$array = str_split($string);
	print_r($array);*/