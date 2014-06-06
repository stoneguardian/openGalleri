<?php
	session_start();

	include '../config/siteConf.php';


	//echo "HEI";
	//Get variables
	$path = $_GET['path'];
	$challenge = $_GET['key'];
	$truth = $_SESSION['key'];

	if($challenge === $truth and $challenge != '' and $path != ''){
		$truePath = $path;

		if(file_exists($truePath)){
			$type = pathinfo($truePath); //Set header
			if($type['extension'] == 'jpg'){
				header('Content-type: image/jpeg');
			}elseif($type['extension'] == 'png'){
				header('Content-type: image/png');
			}
			readfile($truePath);
		}
	}else{
		header('Content-type: image/png');
		readfile('../bilder/noAccess.png');
		//echo $challenge . '<br>';
		//echo $truth;
	}