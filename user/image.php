<?php
/**
 * Created by PhpStorm.
 * User: Hallvard
 * Date: 02.05.14
 * Time: 20:22
 */
	session_start();

	include '../config/siteconfig.php';

	//Get variables
	$path = $_GET['path'];
	$challenge = $_GET['key'];
	$truth = $_SESSION['key'];

	//header('Content-type: image/png');
	header('Content-type: image/png');
	if($challenge === $truth and $challenge != '' and $path != ''){
		$truePath = $albumPath . $path;
		readfile($truePath);
	}else{
		readfile('../bilder/noAccess.png');
		//echo $challenge . '<br>';
		//echo $truth;
	}