<?php
	if(isset($_POST['usrFna']) and isset($_POST['usrLna']) and isset($_POST['usrMai']) and isset($_POST['usrPwd'])){

		require '../class/dbClass.php';

		//POST-variabler
		$fName = $_POST['usrFna'];
		$lName = $_POST['usrLna'];
		$mail = $_POST['usrMai'];
		$pw = $_POST['usrPwd'];


    	//Hash-er passordet
    	require '../lib/phpass/PasswordHash.php';
    	$t_hasher = new PasswordHash(8, FALSE);
    	$hash = $t_hasher -> HashPassword($pw);

		$user = "INSERT INTO ".$tblName['user']." VALUES(NULL, :mail, :pwd, :fname, :lname, :type, NULL, NULL)";
		$parameters = array('mail' => $mail, 'pwd' => $hash, 'fname' => $fName, 'lname' => $lName, 'type' => 1);

		$db = new db();
		$db -> add($user, $parameters);

		$return = array('user' => true);

	}elseif(isset($_POST['updateDB']) or $TestResetDB == true){

		require '../class/dbClass.php';

		//Spørringer
		$dropAlbum = "DROP TABLE IF EXISTS $old_album;";
		$dropPicture = "DROP TABLE IF EXISTS $old_pictures;";
		$dropUser = "DROP TABLE IF EXISTS $old_users;";
		$dropAlbumAccess = "DROP TABLE IF EXISTS $old_tblAlbumAccess";
		$dropCode = "DROP TABLE IF EXISTS $old_tblCode";

		$b_album = "CREATE TABLE ".$tblName['albm']." (
                id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
                creatorId int NOT NULL,
                name varchar(30),
                year int(4),
                description varchar(400),
                cover int(11)
                );";

		$b_picture = "CREATE TABLE ".$tblName['pict']." (
                 id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
                 aid int NOT NULL,
                 imageNum int(11) NOT NULL,
                 name varchar(50),
                 path varchar(200) NOT NULL
                 );";

		$b_user = "CREATE TABLE ".$tblName['user']." (
                  id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
                  email varchar(100) NOT NULL,
                  password varchar(60) NOT NULL,
                  fname varchar(40) NOT NULL,
                  lname varchar(40) NOT NULL,
                  type int DEFAULT NULL,
                  recover varchar(8) DEFAULT NULL,
                  recoverTime datetime DEFAULT NULL
                  );";

		$albumAccess = "CREATE TABLE ".$tblName['acce']." (
						id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
						albumId int NOT NULL,
						type varchar(8) NOT NULL,
						otherId int NOT NULL,
						permission int(3)
						);";

		$code = "CREATE TABLE ".$tblName['code']." (
				 id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
				 code varchar(10) NOT NULL,
				 expire datetime,
				 password varchar(60)
				 );";

		$db = new db();

		//Fjern tidligere
		$db -> add($dropAlbum);
		$db -> add($dropPicture);
		$db -> add($dropUser);
		$db -> add($dropAlbumAccess);
		$db -> add($dropCode);

		//Legg til tabeller
		$db -> add($b_album);
		$db -> add($b_picture);
		$db -> add($b_user);
		$db -> add($albumAccess);
		$db -> add($code);

		$return = array('db' => true);

	}else{
		$return = array('error' => 'ikke nok parameter');
	}

	echo json_encode($return);
	header("Content-Type: application/json", true);

    //--------Fjernet--------//
    /*
    $drop_biKobAl = "DROP TABLE IF EXISTS b_biKobAl;";

    $b_biKobAl = "CREATE TABLE b_biKobAl (
                  id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
                  albId int NOT NULL,
                  bilId int NOT NULL
                  );";

    add($drop_biKobAl);
    add($b_biKobAl);
    */
    //----------------------//