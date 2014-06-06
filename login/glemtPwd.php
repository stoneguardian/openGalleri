<?php
	//- Return --------------------------------//
	// 'glemtStatusCode'
	// 'glemtStatusMsg'

	//- Error Codes ---------------------------//
	// 200 OK
	// 500 General Error
	// 501 Missing Variables
	// 502 Email Not Found
	// 503 Database Error

	//-----------------------------------------//

    //funnet: http://stackoverflow.com/a/6101969
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
    //------------------------------------------

    if(isset($_POST['glemtMail'])){
        include "../sql/db_old.php";

        if($stmt = $tk -> prepare("SELECT id, fname, lname FROM ". $users ." WHERE email = ?")){
            $stmt -> bind_param('s', $_POST['glemtMail']);
            $stmt -> execute();
            $stmt -> bind_result($uid, $fname, $lname);
            $stmt -> store_result();
            $stmt -> fetch();
            $decide = $stmt -> num_rows;
            $stmt -> close();
        }

        if($decide == 1){
            $name = $fname . " " . $lname;
            $code = randomPassword();

			//if(isset($_POST['ajax']) and $_POST['ajax'] == true){
				$ajax = true;
			//}

            //må ha med disse variablene
            $toMail = $_POST['glemtMail'];
            $toName = $name;
            $subject = "Glemt passord";
            $message = "Her er koden for å oppdatere ditt passord: <br>
                        <pre>&#9;<strong>$code</strong></pre>
                        Ikke glemt det gamle? Ikke bedt om nytt passord?<br>
                        Du behøver ikke å gjøre noe, ditt gamle passord vil 
                        fortsatt fungere, det er bare å slette denne e-posten.";

            //sender eposten
            include "../mail/sendMail.php";

            //Sett kode og tidspunkt i databasen
            if($stmt = $tk -> prepare("UPDATE ". $users ." SET recover = ?, recoverTime = NOW() WHERE id = ? AND email = ?")){
                $stmt -> bind_param('sis', $code, $uid, $_POST['glemtMail']);
                $stmt -> execute();
                $stmt -> close();

				$glemtStatus = array('glemtStatusCode' => 200, 'glemtStatusMsg' => 'Database oppdatert');
            }else{
				$glemtStatus = array('glemtStatusCode' => 503, 'glemtStatusMsg' => 'Databasefeil');
			}
        }else{
			$glemtStatus = array('glemtStatusCode' => 502, 'glemtStatusMsg' => 'Fant ikke e-post');
			$mailStatus = array('mailStatusCode' => 0, 'mailStatusMsg' => 'Ikke prossessert');
        }
    }else{
		$glemtStatus = array('glemtStatusCode' => 501, 'glemtStatusMsg' => 'Mangler variabler');
		$mailStatus = array('mailStatusCode' => 0, 'mailStatusMsg' => 'Ikke prossessert');
    }

	//Lag ferdig array
	$return = array('glemtStatusCode' => $glemtStatus['glemtStatusCode'],
					'glemtStatusMsg' => $glemtStatus['glemtStatusMsg'],
					'mailStatusCode' => $mailStatus['mailStatusCode'],
					'mailStatusMsg' => $mailStatus['mailStatusMsg']);

	echo json_encode($return);
	header("Content-Type: application/json", true);