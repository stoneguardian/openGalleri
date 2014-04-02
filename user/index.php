<?php
    session_start();
    if(!isset($_SESSION['username'])){
        header("Location: ../index.php?n=TRUE");
    }

    $bruker = $_SESSION['username'];

    //include_once "../config/dbname.php";
    include '../sql/db.php';
    include 'gravatar.php';
	include_once 'userFunctions.php';

    //Hent gravatar
    $url = genGravatarURL($bruker, 70);

    //Hent navn
	$name = getName($bruker);
    
    function scaleText($string){
        $defSize = 32;
        $factor = 2;
        $length = strlen($string);
        if($length < 17){
            return $defSize;
        }else{
            $divide = round($length / $factor, 0 , PHP_ROUND_HALF_DOWN);
            return $ans = $defSize - $divide;
        }
    }

    if($stmt = $tk -> prepare("SELECT * FROM ". $album ." a LEFT JOIN ". $users ." u ON u.id = a.uid WHERE u.email = ?")){
        $stmt -> bind_param("s", $_SESSION['username']);
        $stmt -> execute();
        $stmt -> store_result();
        $decide = $stmt -> num_rows;
        $stmt -> close();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="../css/reset.css" type="text/css">
        <link rel="stylesheet" href="../css/styles.css" type="text/css">
        <link href='http://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet' type='text/css'>
        <title>Hoved</title>
    </head>
    <body>
        <div id="sidebar">
            <div id="loginImg">
                <div id="currentIcon" class="userIcon" style="background: url('<?php echo $url; ?>');"></div>
            </div>
            Hei, <?php echo $name; ?>
            <div class="side-cont no-top">
                <a href="../index.php?exit=true" class="side-btn">Logg ut</a>
            </div>
            
            <div class="side-cont">
                <span class="side-cont-title">Navigasjon</span>
                <a href="index.php" class="side-btn">Hjem</a>
                <a href="nyttAlbum.php" class="side-btn">Nytt album</a>
            </div>
        </div>

        <div id="beside">
            <h1>Mine album</h1>
            <?php
				echo $decide . "<br>";

                if($decide > 0){
					echo $decide;
                    if($stmt = $tk -> prepare("SELECT a.name, a.year, p.path FROM ". $album ." a LEFT JOIN ". $users ." u ON u.id = a.uid LEFT JOIN ". $pictures ." p ON a.id = p.aid WHERE u.email = ? AND a.cover = p.imageNum")){
                        $stmt -> bind_param("s", $bruker);
                        $stmt -> execute();
                        $stmt -> bind_result($aName, $aYear, $aPath);
                        while($stmt -> fetch()){
                            $fontSize = scaleText($aName);
							$absolutePath = "../album/" . $bruker . "/" . $aYear . "-" . $aName . "/" . $aPath;

                            echo '
                                  <div class="album-box" style="background:url(' . "'$absolutePath'" . '); background-repeat: no-repeat; background-position: center center; background-size: cover;">
                                  <div class="box-pad"></div>
                                  <a href="album.php?aName='. $aName . '&aYear=' . $aYear . '" class="box-title" style="font-size: ' . $fontSize . 'px;">' . $aName . '</a>
                                  <a class="box-year">' . $aYear . '</a>
                                  </div>';
                        }
                        $stmt -> close();
                    }
                }else{
                    echo "her var det tomt gitt";
                }
            ?>
        </div>
    </body>
</html>
