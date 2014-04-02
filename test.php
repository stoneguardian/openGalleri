<?php
    session_start();

    if(isset($_GET['clear'])){
        if(isset($_SESSION['username'])){
            unset($_SESSION['username']);
            echo "session fjernet <br>";
        }
        echo "Du kjører 'ut'";
        header('location:index.php');
    }

    if(isset($_SESSION['username'])){
        echo "All good to go (sessionvise)<br>";
    }else{
        echo "Auda.. session ikke satt";
    }

    echo "<br><br>";

    if(isset($_GET['error'])){
        if($_GET['error'] == 1){
            echo "Yay!";
        }else{
            echo "Nay!<br>";
            echo $_GET['error'];
        }
    }

    if(isset($_GET['albumRouter'])){
        include_once "sql/db.php";
        if($stmt = $tk -> prepare("INSERT INTO b_album VALUES(NULL, ?, ?, ?, NULL)")){
            $stmt -> bind_param("isi", $_GET['uid'], $_POST['albumNavn'], $_POST['year']);
            $stmt -> execute();
            $stmt -> close();
        }

        if($stmt = $tk -> prepare("SELECT id FROM b_album WHERE uid = ? AND navn = ? and year = ?")){
            $stmt -> bind_param("isi", $_GET['uid'], $_POST['albumNavn'], $_POST['year']);
            $stmt -> execute();
            $stmt -> bind_result($albumID);
            $stmt -> fetch();
            $stmt -> close();
        }

        echo $albumID;
        header('Location:upload/upload.php?id=' . $albumID . '&year=' . $_POST['year'] . '&name=' . $_POST['albumNavn']);
    }

    if(isset($_GET['noAccess'])){
        echo "Feil, du har ikke tilgang til å se dette albumet";
    }

    $date = date('Y-m-d H:i:s');
    $tretti = new DateTime($date);
    $tretti -> modify('+60 minute');

    echo $date . "<br>";
    
    echo "+ 30 min : " . date_format($tretti, 'Y-m-d H:i:s');

    echo "<br><br>";

	$dirOrig = 'album/spretten48@gmail.com/2013 - Italia';
	$newDir = 'album/spretten48@gmail.com/2000 - moveTest';

	/*if(is_dir($dirOrig)){
		$dir = opendir($dirOrig);

		while($file = readdir($dir)){
			if($file != "." and $file != ".."){
				echo $file;
				echo "<br>";
			}
		}
	}else{
		echo "ikke en mappe!<br>";
	}*/

	rename($dirOrig, $newDir);

    echo "<br>Velkommen til test.php";
?>