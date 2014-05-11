<?php

	$TestResetDB = true;

	include "../setup/setupDB.php";

	//Variabler
	$fName = 'Hallvard';
	$lName = 'Vasstveit';
	$mail = 'spretten48@gmail.com';
	$pw = '1234';


	//Hash-er passordet
	require '../login/phpass/PasswordHash.php';
	$t_hasher = new PasswordHash(8, FALSE);
	$hash = $t_hasher -> HashPassword($pw);

	$user = "INSERT INTO $users (email, password, fname, lname, type) VALUES('$mail', '$hash', '$fName', '$lName', 1);";
	add($user);

	echo "Database nullstilt";