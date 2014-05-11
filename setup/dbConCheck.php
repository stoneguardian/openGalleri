<?php
	if(isset($_POST['host']) and isset($_POST['un']) and isset($_POST['pw']) and isset($_POST['db'])){
		$tk = mysqli_connect($_POST['host'], $_POST['un'], $_POST['pw'], $_POST['db']);

		if($tk){
			$dbConCode = 200;
			$dbConMsg = 'Oppkobling suksessfull';
		}else{
			$dbConCode = 500;
			$dbConMsg = 'Oppkobling mislyktes';
		}
	}

	$return = array('dbConCode' => $dbConCode, 'dbConMsg' => $dbConMsg);
	echo json_encode($return);
	header("Content-Type: application/json", true);