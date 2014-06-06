<?php
	require '../class/albumClass.php';
	require '../class/dbClass.php';
	require '../class/userClass.php';

	$db = new db();
	$user = new user($db);

	//Generate array for album class
	$value = array('cid' => 1);
	$album = new album($value, $user, $db, 'new');

	//Create album
	$albumId = $album -> createAlbum();

	echo $albumId;
	echo "<br>";
	echo $album -> creatorMail;
	echo "<br>";

	$user -> addById(1);
	echo $user -> getMail();

	echo "<br>-end-";

	/*$creatorId = 1;
	$name = $creatorId . date('dmYHis');
	$year = date('Y');
	$description = "";

	//echo $tblName['albm'];

	echo $query = "INSERT INTO ". $tblName['albm'] ." VALUES(NULL, :creatorId, :name, :year, :desc, :cover)";
	echo "<br>";
	echo $parameters = array('creatorId' => 1, 'name' => $name, 'year' => $year, 'desc' => "", 'cover' => 1);
	echo "<br>";
	echo $db -> add($query, $parameters);
	echo "<br>";
	echo $db -> getLastID();*/

	/*$query = "INSERT INTO b_album VALUES(NULL, :creatorId, :name, :year, :desc, :cover)";
	$params = array('creatorId' => 1, 'name' => 'Test', 'year' => 2000, 'desc' => '', 'cover' => 1);
	$db -> add($query, $params);
	echo $db -> getLastID();
	echo "<br>";
	echo $db -> getError();*/