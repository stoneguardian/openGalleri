<?php
	//Logincheck --------------------------//
    session_start();
    if(!isset($_SESSION['username'])){
        header("Location: ../index.php?n=TRUE");
    }
	//-------------------------------------//

	//Includes ----------------------------//
	include '../class/dbClass.php';
	include '../class/userClass.php';
	include '../class/albumClass.php';
	//-------------------------------------//

    $mail = $_SESSION['username'];
	$key = $_SESSION['key'];

	//Create user and get information
	$db = new db();
	$user = new user($db);
	$user -> addByMail($mail);

	$uid = $user -> getUid();

	//Check how many albums the user have
	//$decide = albumCount($uid);
	$query = "SELECT albumId FROM ".$tblName['acce']." WHERE type = 'user' AND otherId = :uid";
	$parameter = array('uid' => $uid);
	$decide = $db -> getNumRows($query, $parameter);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include '../head.php';?>
    </head>
    <body>
        <div id="sidebar">
			<?php $user -> getSidebar(); ?>
        </div>

        <div id="beside">
            <h1>Mine album</h1>
            <?php
				if($decide > 0){
					$result = $db -> ask($query, $parameter);
					foreach($result as $row){
						$value = array('id' => $row['albumId']);
						$album = new album($value, $user, $db);
						$tmpPath = $album -> getCoverPath();
						$absolutePath = "image.php?path=".$tmpPath."&key=".$key;
						$aName = $album -> getName();
						$aYear = $album -> getYear();
						$aId = $album -> getId();

						//echo $tmpPath;

						echo '
                                  <div class="album-box" style="background:url(' . "'$absolutePath'" . '); background-repeat: no-repeat; background-position: center center; background-size: cover;">
                                  <div class="box-pad"></div>
                                  <a href="album.php?id='.$aId.'" class="box-title" style="font-size: ' . $fontSize . 'px;">' . $aName . '</a>
                                  <a class="box-year">' . $aYear . '</a>
                                  </div>';
					}

				    /*if($stmt = $tk -> prepare("SELECT a.name, a.year, p.path FROM ". $album ." a LEFT JOIN ". $users ." u ON u.id = a.uid LEFT JOIN ". $pictures ." p ON a.id = p.aid WHERE u.id = ? AND a.cover = p.imageNum")){
                        $stmt -> bind_param("s", $uid);
                        $stmt -> execute();
                        $stmt -> bind_result($aName, $aYear, $aPath);
                        while($stmt -> fetch()){
                            $fontSize = scaleText($aName);
							$absolutePath = "../album/" . $user -> getMail() . "/" . $aYear . "-" . $aName . "/" . $aPath;

                            echo '
                                  <div class="album-box" style="background:url(' . "'$absolutePath'" . '); background-repeat: no-repeat; background-position: center center; background-size: cover;">
                                  <div class="box-pad"></div>
                                  <a href="album.php?aName='. $aName . '&aYear=' . $aYear . '" class="box-title" style="font-size: ' . $fontSize . 'px;">' . $aName . '</a>
                                  <a class="box-year">' . $aYear . '</a>
                                  </div>';
							//echo $aName;
							$album = new album($tk, $uid, $aName, $aYear);
							echo $album -> conStatus;
							//$album -> getCard();
							//echo $album -> getName();


                        }
                        $stmt -> close();
                    }*/
					/*$album = new album(1, 'Italia', 2013);
					$album -> getName();
					echo "123";*/
                }else{
                    echo "her var det tomt gitt";
                }
            ?>
        </div>
    </body>
</html>
