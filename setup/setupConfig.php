<?php
	//Filstier
	$dbFile = "../config/dbConf.php";
	$mailFile = "../config/mailConf.php";

	if(isset($_POST['dbPref']) and isset($_POST['dbHost']) and isset($_POST['dbName'])
	and isset($_POST['dbUser']) and isset($_POST['dbPwd'])){

		//dbConf.php content
		$dbConfContent = '
	<?php
		//- Connection properties --------------//
		$dbServer = "'. $_POST['dbHost'] .'";     //IP or hostname to MySQL-server
		$db = "'. $_POST['dbName'] .'";                           //Which database
		$dbUsername = "'. $_POST['dbUser'] .'";                   //Username
		$dbPassword = "'. $_POST['dbPwd'] .'";                  //Password

		//- Table-names ------------------------//

		$prefix = "'. $_POST['dbPref'] .'";

		//Current names
		// to change names, change the value on the right
		$tblName = array("albm" => $prefix."album",
					 	 "pict" => $prefix."pictures",
					 	 "user" => $prefix."users",
					 	 "acce" => $prefix."albumAccess",
					 	 "code" => $prefix."code");

		//Old prefix
	   	$old_prefix = "'. $_POST['dbPref'] .'";

	    //Old names
	    $old_album = $old_prefix . "album";
	    $old_pictures = $old_prefix . "pictures";
	    $old_users = $old_prefix . "users";
	    $old_tblAlbumAccess = $old_prefix . "albumAccess";
		$old_tblCode = $old_prefix . "code";
		';

		//--------------------------------------------//

		//Fjern eksisterende
		if(file_exists($dbFile) == true){
			unlink($dbFile);
		}

		//Skriv database config-fil
		$file = fopen($dbFile, 'w');
		fwrite($file, $dbConfContent);
		fclose($file);

		$return = array("db" => true);

	}elseif(isset($_POST['mailHost']) and isset($_POST['mailPort']) and isset($_POST['mailUser'])
	and isset($_POST['mailPwd']) and isset($_POST['mailFromMail']) and isset($_POST['mailFromName'])){

		//mail.php content							  //
		$mailContent = '
	<?php
    	$host = "'. $_POST['mailHost'] .'";           //Host domain or IP-adress
	    $port = '. $_POST['mailPort'] .';                            //Port used for mail
	    $username = "'. $_POST['mailUser'] .'";    //Username
	    $password = "'. $_POST['mailPwd'] .'";             //Password

    	$fromMail = "'. $_POST['mailFromMail'] .'";    //From (e-mail address)
    	$fromName = "'. $_POST['mailFromName'] .'";    //From (name)

	    //Brukes for øyeblikket kun ved glemt passord, for mer konfigurasjon sjekk filene:
    		//- login/glemtPwd.php
    		//- mail/sendMail.php
	';
		//--------------------------------------------//

		//Fjern eksisterende
		if(file_exists($mailFile) == true){
			unlink($mailFile);
		}

		//Skriv mail config-fil
		//mail.php
		$file = fopen($mailFile, 'w');
		fwrite($file, $mailContent);
		fclose($file);

		$return = array("mail" => true);

	}else{

		$return = array("error" => "Ikke nok parameter");

	}

	//Skriv JSON variabler
	echo json_encode($return);
	header("Content-Type: application/json", true);