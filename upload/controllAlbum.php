<?php
	require '../class/albumClass.php';
	require '../class/dbClass.php';
	require '../class/userClass.php';

	$switch = $_POST['switch'];

	function renameDir($oldName, $oldYear, $newName, $newYear, $mail){
		$dir = "../album/$mail/";
		$oldDir = $dir . $oldYear . "-" . $oldName;
		$newDir = $dir . $newYear . "-" . $newName;

		rename($oldDir, $newDir);
	}

	if(isset($_POST['switch'])){
		$db = new db();
		$user = new user($db);
	}

	if($switch == '0' and isset($_POST['uid'])){
		//Generate array for album class
		$value = array('cid' => $_POST['uid']);
		$album = new album($value, $user, $db, 'new');

		//Create album
		$albumId = $album -> createAlbum();

		$return = array('albumID' => $albumId);

	}elseif($switch == '1' and isset($_POST['albumID']) and isset($_POST['albumName']) and isset($_POST['albumYear']) and isset($_POST['count']) and isset($_POST['uid']) and isset($_POST['mail'])){
		//Generate array for album class
		$value = array('id' => $_POST['albumID']);
		$album = new album($value, $user, $db);

		//Get old values
		$oldName = $album -> getName();
		$oldYear = $album -> getYear();

		//Update and save to DB
		$album -> updateName($_POST['albumName']);
		$album -> updateYear($_POST['albumYear']);

		//If successful update folder
		if($album -> saveToDb() == true){
			$album -> moveFolder($oldName, $oldYear);
			$return = array('updateAlbum' => true);
		}else{
			$return = array('updateAlbum' => false, 'errorMsg' => 'Databasefeil');
		}

	}elseif($switch == '2' and isset($_POST['albumID']) and isset($_POST['albumCover'])){//Oppdater Coverbilde
		//Generate array for album class
		$value = array('id' => $_POST['albumID']);
		$album = new album($value, $user, $db);

		//Set new value
		$album -> updateCover($_POST['albumCover']);

		if($album -> saveToDb() == true){
			$return = array('cover' => true);
		}else{
			$return = array('cover' => false);
		}

	}elseif($switch == '3' and isset($_POST['albumID']) and isset($_POST['albumName']) and isset($_POST['albumYear'])){
		//Slett bilder (dersom det er noen)
		/*if($stmt = $tk -> prepare("DELETE FROM ". $pictures ." WHERE aid = ?")){
			$stmt -> bind_param('i', $_POST['albumID']);
			$stmt -> execute();
			$stmt -> close();
		}*/

		//Slett album
		if($stmt = $tk -> prepare("DELETE FROM ". $album ." WHERE id = ?")){
			$stmt -> bind_param('i', $_POST['albumID']);
			$stmt -> execute();
			$stmt -> close();


			$return = array('rm' => true);
		}else{
			$return = array('rm' => false, 'errorMsg' => 'Databasefeil');
		}

	}else{
		$return = array('error' => true, 'errorMsg' => 'For få variabler', 'switch' => $switch);

	}

    echo json_encode($return);
    header("Content-Type: application/json", true);