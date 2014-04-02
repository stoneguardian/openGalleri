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

	}elseif(isset($_POST['updateDB'])){

		require '../sql/db.php';

		//Spørringer
		$drop_album = "DROP TABLE IF EXISTS $old_album;";
		$drop_bilder = "DROP TABLE IF EXISTS $old_pictures;";
		$drop_brukere = "DROP TABLE IF EXISTS $old_users;";

		$b_album = "CREATE TABLE $album (
                id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
                uid int NOT NULL,
                name varchar(30),
                year int(4),
                cover int(11)
                );";

		$b_bilder = "CREATE TABLE $pictures (
                 id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
                 aid int NOT NULL,
                 imageNum int(11) NOT NULL,
                 name varchar(50),
                 path varchar(200)
                 );";

		$b_brukere = "CREATE TABLE $users (
                  id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
                  email varchar(100) NOT NULL,
                  password varchar(60) NOT NULL,
                  fname varchar(40) NOT NULL,
                  lname varchar(40) NOT NULL,
                  type int DEFAULT NULL,
                  recover varchar(8) DEFAULT NULL,
                  recoverTime datetime DEFAULT NULL
                  );";

		//echo $drop_album;

		//Fjern tidligere
		add($drop_album);
		add($drop_bilder);
		add($drop_brukere);

		//Legg til tabeller
		add($b_album);
		add($b_bilder);
		add($b_brukere);

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