<?php
    session_start();

    if($_POST['husk'] == TRUE){
        setcookie('husk', $_POST['brukernavn'], 3600, '/');
    }

    if(isset($_POST['brukernavn']) and isset($_POST['passord'])){
        //require "../config/dbname.php";
        require 'phpass/PasswordHash.php'; //http://www.openwall.com/phpass/
        require '../sql/db.php';

        //Henter passordet fra databasen
        if($stmt = $tk -> prepare("SELECT password FROM " . $users . " WHERE email = ?")){
            $stmt -> bind_param("s", $_POST['brukernavn']);
            $stmt -> execute();
            $stmt -> bind_result($pass);
            $stmt -> fetch();
            $stmt -> close();
        }

        //Sjekker om passordet stemmer
        $t_hasher = new PasswordHash(8, FALSE);
        $check = $t_hasher -> CheckPassword($_POST['passord'], $pass);
        if ($check){
            $_SESSION['username'] = $_POST['brukernavn'];
            $login = array("login" => "true");
        }else{
            $login = array('login' => 'false');
        }

        //Skriv JSON variabler
        echo json_encode($login);
        header("Content-Type: application/json", true);
    }

    if(isset($_GET['ut'])){
        if(isset($_SESSION['username'])){
            unset($_SESSION['username']);
            echo "session fjernet <br>";
        }
        echo "Du kjører 'ut'";
    }
?>