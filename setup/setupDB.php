<?php
	if(isset($_POST['usrFna']) and isset($_POST['usrLna']) and isset($_POST['usrMai']) and isset($_POST['usrPwd'])){

		require '../sql/db.php';

		//POST-variabler
		$fName = $_POST['usrFna'];
		$lName = $_POST['usrLna'];
		$mail = $_POST['usrMai'];
		$pw = $_POST['usrPwd'];


    	//Hash-er passordet
    	require '../login/phpass/PasswordHash.php';
    	$t_hasher = new PasswordHash(8, FALSE);
    	$hash = $t_hasher -> HashPassword($pw);

    	$user = "INSERT INTO $users (email, password, fname, lname, type) VALUES('$mail', '$hash', '$fName', '$lName', 1);";
		add($user);

		$return = array('user' => true);

	}elseif(isset($_POST['updateDB']) or $TestResetDB == true){

		require '../sql/db.php';

		//Spørringer
		$dropAlbum = "DROP TABLE IF EXISTS $old_album;";
		$dropPicture = "DROP TABLE IF EXISTS $old_pictures;";
		$dropUser = "DROP TABLE IF EXISTS $old_users;";
		$dropAlbumAccess = "DROP TABLE IF EXISTS $old_tblAlbumAccess";
		$dropCode = "DROP TABLE IF EXISTS $old_tblCode";

		$b_album = "CREATE TABLE $album (
                id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
                creatorId int NOT NULL,
                name varchar(30),
                year int(4),
                description varchar(400),
                cover int(11)
                );";

		$b_picture = "CREATE TABLE $pictures (
                 id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
                 aid int NOT NULL,
                 imageNum int(11) NOT NULL,
                 name varchar(50),
                 type varchar(14) NOT NULL,
                 path varchar(200) NOT NULL
                 );";

		$b_user = "CREATE TABLE $users (
                  id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
                  email varchar(100) NOT NULL,
                  password varchar(60) NOT NULL,
                  fname varchar(40) NOT NULL,
                  lname varchar(40) NOT NULL,
                  type int DEFAULT NULL,
                  recover varchar(8) DEFAULT NULL,
                  recoverTime datetime DEFAULT NULL
                  );";

		$albumAccess = "CREATE TABLE $tblAlbumAccess (
						id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
						albumId int NOT NULL,
						type varchar(8) NOT NULL,
						otherId int NOT NULL,
						permission int(3)
						);";

		$code = "CREATE TABLE $tblCode (
				 id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
				 code varchar(10) NOT NULL,
				 expire datetime,
				 password varchar(60)
				 );";

		//echo $drop_album;

		//Fjern tidligere
		add($dropAlbum);
		add($dropPicture);
		add($dropUser);
		add($dropAlbumAccess);
		add($dropCode);

		//Legg til tabeller
		add($b_album);
		add($b_picture);
		add($b_user);
		add($albumAccess);
		add($code);

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