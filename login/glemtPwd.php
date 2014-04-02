<?php
        
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
        include "../sql/db.php";

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

            echo $name = $fname . " " . $lname;
            $code = randomPassword();

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

            echo "<br>Før Database, etter mail";

            //Sett kode og tidspunkt i databasen
            if($stmt = $tk -> prepare("UPDATE ". $users ." SET recover = ?, recoverTime = NOW() WHERE id = ? AND email = ?")){
                $stmt -> bind_param('sis', $code, $uid, $_POST['glemtMail']);
                $stmt -> execute();
                $stmt -> close();
            }
            echo "<br>Database oppdatert";
            //header('Location:recover.php');
        }else{
            echo "ikke riktig mail";
            //header('Location:../login.php?mail=false');
        }
    }else{
        echo "Now you don't!";
    }
?>