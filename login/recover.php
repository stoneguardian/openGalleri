<?php
    require '../sql/db.php';

    if(!isset($_GET['step'])){
        $stage = 0;
    }elseif($_GET['step'] == 1){
        $stage = 10;

        if($stmt = $tk -> prepare("SELECT id, recover, recoverTime FROM ". $users ." WHERE recover = ?")){
            $stmt -> bind_param('s', $_POST['code']);
            $stmt -> execute();
            $stmt -> bind_result($uid, $recover, $recoverTime);
            $stmt -> store_result();
            $stmt -> fetch();
            $decide = $stmt -> num_rows;
            $stmt -> close();
        }

        if($decide == 0){
            $stage = 5;
        }else{
            $stage = 1;
            $date = new DateTime();
            $date -> format('Y-m-d H:i:s');

            $timeout = new DateTime($recoverTime);
            $timeout -> format('Y-m-d H:i:s');
            $timeout -> modify('+60 minute');
            $timeReached = $timeout >= $date;

            if($timeReached == FALSE){
            $stage = 6;
            }
        }
        
        
    }elseif($_GET['step'] == 2){
        $stage = 10;

        require '../login/phpass/PasswordHash.php';
        $t_hasher = new PasswordHash(8, FALSE);
        $hash = $t_hasher -> HashPassword($_POST['pwd']);

        if($stmt = $tk -> prepare("UPDATE ". $users ." SET password = ? WHERE id = ?")){
            $stmt -> bind_param('si', $hash, $_POST['uid']);
            $stmt -> execute();
            $stmt -> close();
        }

        $stage = 2;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <script>
            function chkPwd()
            {
                var orig = document.getElementById('pwd').value;
                var rep = document.getElementById('pwdRep').value;

                if (orig == rep)
                {
                    document.forms['chngPwd'].submit();
                } else
                {
                    document.getElementById('errors').innerHTML = 'Passordene stemmer ikke med hverandre';
                }
            }
        </script>
    </head>
    <body>
        <?php
            if($stage == 0){
                echo "Vennligst Skriv inn koden du har fått på e-post:";
                echo '<form method="post" action="recover.php?step=1">
                      <input type="text" name="code" required>
                      <input type="submit" value="Neste">
                      </form>';
            }elseif($stage == 1){
                echo '<form method="post" action="recover.php?step=2" id="chngPwd"">
                        <div id="errors"></div>
                        <input type="hidden" name="uid" value="'. $uid .'">
                        <input type="password" name="pwd" id="pwd" placeholder="Nytt passord" required><br>
                        <input type="password" name="pwdRep" id="pwdRep" placeholder="Gjenta nytt passord" required><br>
                        <input type="button" value="Endre passord" onclick="chkPwd();">
                        <!--<input type="submit" value="Endre passord">-->
                      </form>';
            }elseif($stage == 2){
                echo 'Passord oppdatert';
                echo '<br><br>';
                echo '<a href="../index.php">Tilbake til innlogging</a>';
            }elseif($stage == 5){
                echo 'Beklager, fant ingen bruker med koden: ' . $_POST['code'];
                echo '<br><a href="recover.php">Prøv igjen</a>';
            }elseif($stage == 6){
                echo 'Beklager, tiden for å bytte passord er utløpt. ';
                echo '<a href="../index.php">Be om en ny e-post</a><br>';
            }elseif($stage == 7){
                echo 'Passordene var ikke like';
            }else{
                echo 'Unknown error';
            }
        ?>
        
    </body>
</html>
