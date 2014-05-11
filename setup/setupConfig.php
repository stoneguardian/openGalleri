<?php
	//Filstier
	$dbnameFile = "../config/dbname.php";
	$dbConnectFile = "../config/dbConnect.php";
	$mailFile = "../config/mail.php";

	if(isset($_POST['dbPref']) and isset($_POST['dbHost']) and isset($_POST['dbName'])
	and isset($_POST['dbUser']) and isset($_POST['dbPwd'])){

		//dbname.php content						//
		$dbnameContent = '
	<?php
		//Change what prefix the tablenames should have
    	$prefix = "'. $_POST['dbPref'] .'";

		//Change tablenames
		$album = $prefix . "album";
		$pictures = $prefix . "pictures";
		$users = $prefix . "users";
		$tblAlbumAccess = $prefix . "albumAccess";
		$tblCode = $prefix . "code";

		//Old prefix
	   	$old_prefix = "'. $_POST['dbPref'] .'";

	    //Old tablenames
	    $old_album = $old_prefix . "album";
	    $old_pictures = $old_prefix . "pictures";
	    $old_users = $old_prefix . "users";
	    $old_tblAlbumAccess = $old_prefix . "albumAccess";
		$old_tblCode = $old_prefix . "code";

		//echo "dbname conf here";
	';
		//--------------------------------------------//

		//dbConnect.php content						  //
		$dbConnectContent = '
	<?php
		$dbServer = "'. $_POST['dbHost'] .'";     //IP or hostname to MySQL-server
		$db = "'. $_POST['dbName'] .'";                           //Which database
		$dbUsername = "'. $_POST['dbUser'] .'";                   //Username
		$dbPassword = "'. $_POST['dbPwd'] .'";                  //Password
	';
		//--------------------------------------------//

		//Fjern eksisterende
		if(file_exists($dbConnectFile) == true){
			unlink($dbConnectFile);
		}
		if(file_exists($dbnameFile) == true){
			unlink($dbnameFile);
		}

		//Skriv database config-filer
		//dbConnect.php
		$file = fopen($dbConnectFile, 'w');
		fwrite($file, $dbConnectContent);
		fclose($file);

		//dbname.php
		$file = fopen($dbnameFile, 'w');
		fwrite($file, $dbnameContent);
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