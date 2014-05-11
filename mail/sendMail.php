<?php
    //- Krever variablene ---------------------//
    // $toMail
    // $toName
    // $subject
    // $message

	//- Return --------------------------------//
	// 'mailStatusCode'
	// 'mailStatusMsg'

	//- Error Codes ---------------------------//
	// 0 Not Run
	// 200 OK
	// 500 General Error
	// 501 Missing Variables
	// 502 Sending Error

	//-----------------------------------------//
    
    if(isset($toMail) and isset($toName) and isset($subject) and isset($message)){

        //eksempel funnet: http://stackoverflow.com/a/8629554
        //Last ned PHPMailer her: https://github.com/Synchro/PHPMailer
        require_once "../config/mail.php";
        require_once('class.phpmailer.php');
        //include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

        $mail = new PHPMailer();

        $mail->IsSMTP();                        // telling the class to use SMTP
        $mail->Host       = "ssl://" . $host;   // SMTP server
        $mail->SMTPDebug  = false;                  // enables SMTP debug information (for testing), 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth   = true;               // enable SMTP authentication
        $mail->SMTPSecure = "ssl";              // sets the prefix to the servier
        $mail->Host       = $host;              // the SMTP server
        $mail->Port       = $port;              // the SMTP port
        $mail->Username   = $username;          // username
        $mail->Password   = $password;          // password
    
        $mail->SetFrom($fromMail, $fromName);
        $mail->AddAddress($toMail, $toName);
        $mail->Subject = $subject;
        $mail->MsgHTML($message);

        if(!$mail->Send()) {
            if($ajax == true){
				$mailStatus = array('mailStatusCode' => 502, 'mailStatusMsg' => 'Klarte ikke sende e-post');
            }else{
                echo "Mailer Error: " . $mail->ErrorInfo;
            }
        } else {
            if($ajax == true){
				$mailStatus = array('mailStatusCode' => 200, 'mailStatusMsg' => 'E-post sendt');
            }else{
                echo "Message sent!";
            }
			//$json = true;
        }
    }else{
        if($ajax == true){
			$mailStatus = array('mailStatusCode' => 501, 'mailStatusMsg' => 'Ikke nok variabler');
        }else{
            echo "<br>Mail ERROR: Ikke nok variabler!<br>";
            echo "toMail: $toMail <br>";
            echo "toName: $toName <br>";
            echo "subject: $subject <br>";
            echo "message: $message <br>";
        }
    }