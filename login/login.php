<?php
    session_start();

	function randomPassword() {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$i = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}

    if($_POST['husk'] == TRUE){
        setcookie('husk', $_POST['brukernavn'], 3600, '/');
    }

    if(isset($_POST['brukernavn']) and isset($_POST['passord'])){
        require '../lib/phpass/PasswordHash.php'; //http://www.openwall.com/phpass/
        require '../class/dbClass.php';

		$db = new db();

		//Prepare parameter-array
		$parameters = array('mail' => $_POST['brukernavn']);

		//Run db-query
		$return = $db -> ask("SELECT password FROM " . $tblName['user'] . " WHERE email = :mail", $parameters);

		//Password from database
		$pass = $return[0]['password'];

        //Sjekker om passordet stemmer
        $t_hasher = new PasswordHash(8, FALSE);
        $check = $t_hasher -> CheckPassword($_POST['passord'], $pass);
        if ($check){
            $_SESSION['username'] = $_POST['brukernavn'];
			$_SESSION['key'] = randomPassword();
            $login = array("login" => "true");
        }else{
            $login = array('login' => 'false');
        }

        //Skriv JSON variabler
        echo json_encode($login);
        header("Content-Type: application/json", true);
    }
