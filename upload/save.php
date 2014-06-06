<?php

	require '../class/dbClass.php';
	require '../class/userClass.php';
	require '../class/albumClass.php';

	//POST variables
	$albumID = $_POST['aid'];
	$userId = $_POST['uid'];
	$nr = $_POST['nr'];

	//File variables
	$tmpPath = $_FILES['image']['tmp_name'];
	$type = $_FILES['image']['type'];
	$name = $_FILES['image']['name'];
	//echo var_dump($_FILES['image']);
	echo $_FILES['image']['error'];

	//Create dependencies
	$db = new db();
	$user = new user($db);

	//Create album-class
	$values = array('id' => $albumID);
	$album = new album($values, $user, $db);

	//Add to DB and get imagepath
	$path = $album -> addImage($nr, $type);

	//Save file to disk
	move_uploaded_file($tmpPath, $path);

	echo "image saved to: " . $path;