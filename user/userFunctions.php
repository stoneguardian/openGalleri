<?php
/**
 * Created by PhpStorm.
 * User: Hallvard
 * Date: 29.03.14
 * Time: 14:45
 */

include_once '../sql/db.php';
include_once '../config/dbname.php';

function getID($mail){
	global $tk;
	global $users;

	if($stmt = $tk -> prepare("SELECT id FROM ". $users . " WHERE email = ?")){
		$stmt -> bind_param('s', $mail);
		$stmt -> execute();
		$stmt -> bind_result($uid);
		$stmt -> fetch();
		$stmt -> close();
		return $uid;
	}else{
		return false;
	}
}

function getName($mail){
	global $tk;
	global $users;

	if($stmt = $tk -> prepare("SELECT fname, lname FROM ". $users ." WHERE email = ?")){
		$stmt -> bind_param("s", $mail);
		$stmt -> execute();
		$stmt -> bind_result($fname, $lname);
		$stmt -> fetch();
		$stmt -> close();
		$tmp = $fname . " " . $lname;
		return $tmp;
	}else{
		return false;
	}
}