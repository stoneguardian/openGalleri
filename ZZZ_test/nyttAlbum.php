<?php
	include '../sql/db.php';
	include '../user/albumFunctions.php';

	echo "<p> Velkommen til test </p>";

	$new = new album();
	$new -> createAlbum(1);
	echo $new -> albumId;
	echo " AlbumId<br>";
	echo $new -> name;
	echo " Navn<br>";
	echo $new -> year;
	echo " År<br>";
	echo $new -> creatorId;
	echo " UserId<br>";

	$new -> setAuth('user', 1);
	$new -> testAuth();
	echo $new -> getAccessLevel();
	echo " Access level<br>";