<?php

    require '../sql/db_old.php';

	if(isset($_POST['code']) and isset($_POST['mail'])){

		//- Return --------------------------------//
		// 'codeStatusCode'
		// 'codeStatusMsg'

		//- Error Codes ---------------------------//
		// 0 Not Run
		// 200 OK
		// 500 General Error
		// 501 Missing Variables
		// 502 Code Expired
		// 503 Database Error
		// 504 Code Not Found (With Supplied Mail Address)

		//-----------------------------------------//

		if($stmt = $tk -> prepare("SELECT recover, recoverTime FROM ". $users ." WHERE recover = ? AND email = ?")){
			$stmt -> bind_param('ss', $_POST['code'], $_POST['mail']);
			$stmt -> execute();
			$stmt -> bind_result($recover, $recoverTime);
			$stmt -> store_result();
			$stmt -> fetch();
			$decide = $stmt -> num_rows;
			$stmt -> close();

			if($decide == 1){
				$date = new DateTime();
				$date -> format('Y-m-d H:i:s');

				$timeout = new DateTime($recoverTime);
				$timeout -> format('Y-m-d H:i:s');
				$timeout -> modify('+60 minute');
				$timeReached = $timeout >= $date;

				if($timeReached == false){
					$codeStatus = array('codeStatusCode' => 502, 'codeStatusMsg' => 'Kode utløpt');
				}else{
					$codeStatus = array('codeStatusCode' => 200, 'codeStatusMsg' => 'Kode akseptert');
				}
			}else{
				$codeStatus = array('codeStatusCode' => 504, 'codeStatusMsg' => 'Fant ikke kode med gitt mailadresse');
			}
		}else{
			$codeStatus = array('codeStatusCode' => 503, 'codeStatusMsg' => 'Databasefeil');
		}
		$pwdStatus = array('pwdStatusCode' => 0, 'pwdStatusMsg' => 'Not run');
	}else if(isset($_POST['nPwd']) and isset($_POST['mail'])){

		//- Return --------------------------------//
		// 'pwdStatusCode'
		// 'pwdStatusMsg'

		//- Error Codes ---------------------------//
		// 0 Not Run
		// 200 OK
		// 500 General Error
		// 501 Missing Variables
		// 503 Database Error

		//-----------------------------------------//

		require '../login/phpass/PasswordHash.php';
		$t_hasher = new PasswordHash(8, FALSE);
		$hash = $t_hasher -> HashPassword($_POST['nPwd']);

		if($stmt = $tk -> prepare("UPDATE ". $users ." SET password = ? WHERE email = ?")){
			$stmt -> bind_param('ss', $hash, $_POST['mail']);
			$stmt -> execute();
			$stmt -> close();
			$pwdStatus = array('pwdStatusCode' => 200, 'pwdStatusMsg' => 'Nytt passord satt');
		}else{
			$pwdStatus = array('pwdStatusCode' => 503, 'pwdStatusMsg' => 'Databasefeil');
		}
		$codeStatus = array('codeStatusCode' => 0, 'codeStatusMsg' => 'Not run');
	}else{
		$codeStatus = array('codeStatusCode' => 501, 'codeStatusMsg' => 'Mangler variabler');
		$pwdStatus = array('pwdStatusCode' => 501, 'pwdStatusMsg' => 'Mangler variabler');
	}

	//Gjør klar tilbakesending
	$return = array('codeStatusCode' => $codeStatus['codeStatusCode'],
					'codeStatusMsg' => $codeStatus['codeStatusMsg'],
					'pwdStatusCode' => $pwdStatus['pwdStatusCode'],
					'pwdStatusMsg' => $pwdStatus['pwdStatusMsg']);

	echo json_encode($return);
	header("Content-Type: application/json", true);