<?php
    session_start();
    if(!isset($_SESSION['username'])){
        if(!isset($_GET['code'])){
            header('Location: ../index.php');
        }
    }

    //Databaseoppkobling
    require "../sql/db.php";

    //GET-variabler
    $albumName = $_GET['aName'];
    $albumYear = $_GET['aYear'];

    //Sjekk tilgangen på albumet
    if($stmt = $tk -> prepare("SELECT a.id FROM ". $users ." u LEFT JOIN ". $album ." a ON u.id = a.uid WHERE u.email = ? AND a.name = ? AND a.year = ?")){
        $stmt -> bind_param("ssi", $_SESSION['username'], $albumName, $albumYear);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($albumID);
        $stmt -> fetch();
        $decide = $stmt -> num_rows;
        $stmt -> close();
    }
    
    //Om du ikke har tilgang, kast ut
    if($decide == 0){
        header('Location: test.php?noAccess=true');
    }
    
    //Hent cover-bildet
    if($stmt = $tk -> prepare("SELECT p.path FROM ". $album ." a LEFT JOIN ". $pictures ." p ON a.id = p.aid WHERE a.id = ? AND a.cover = p.imageNum")){
        $stmt -> bind_param("i", $albumID);
        $stmt -> execute();
        $stmt -> bind_result($coverName);
        $stmt -> fetch();
        $stmt -> close();
    }

    //Hent deltakere
    $i = 0;
    if($stmt = $tk -> prepare("SELECT u.email FROM ". $album ." a LEFT JOIN ". $users ." u ON a.uid = u.id WHERE a.id = ?")){
        $stmt -> bind_param("i", $albumID);
        $stmt -> execute();
        $stmt -> bind_result($tmp_users);
        while($stmt->fetch()){
            $inAlbum[$i] = $tmp_users;
            $i += 1;
        }
        $stmt -> close();
    }

	//Stier
	$path = "../album/" . $_SESSION['username'] . "/" . $albumYear . "-" . $albumName . "/";
	$cover = $path . $coverName;

    include_once 'gravatar.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo $albumName; ?></title>
		<link rel="stylesheet" href="../css/reset.css" type="text/css">
        <link rel="stylesheet" href="../css/styles.css" type="text/css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.1/jquery.min.js"></script>
        <script>
            function load()
            {
                repos();
            }


            function repos()
            {
                var ySize = window.innerHeight;
                var pagePos = ySize - 30;
                $('#page').css({ 'top': pagePos + 'px' });
            }

            $(window).scroll(function ()
            {
                var top = $(window).scrollTop();
                $('#title').css({ 'transform': 'translate3d(0px, -' + (top * 0.90) + 'px, 0px)' });
                $('#cover').css({ 'background-position': 'center -' + (top * 0.15) + 'px' });
            });

            function toggleExtras()
            {
                var toggle = document.getElementById('showHide');
                if (toggle.value == 0)
                {
                    $('#extras').addClass('showBar');
                    $('#toggle').addClass('on');
                    toggle.value = 1;
                } else
                {
                    $('#extras').removeClass('showBar');
                    $('#toggle').removeClass('on');
                    toggle.value = 0;
                }
            }
        </script>
    </head>
    <body onload="load();" onresize="repos();">
        <div id="cover" style="background: url('<?php echo $cover;?>'); background-repeat: no-repeat; background-position: center 0; background-size: cover;"></div>
        <div id="title">
            
            <span id="name"><?php echo $albumName; ?></span><span class="rot">(<?php echo $albumYear; ?>)</span>
            <div id="inAlbum">
                <?php 
                    //foreach($inAlbum as $user){
                    //    echo '<div class="user" style="background: url(' . "'" . genGravatarURL($user, '40') . "'" .');"></div>';
                    //}

                    echo '<div class="user" style="background: url(' . "'" . genGravatarURL($inAlbum[0], '35') . "'" .');"></div>';
                ?>
            </div>
        </div>
        <div id="page">
            <div id="extras">
                <input type="hidden" id="showHide" value="0">
                <a id="toggle" href="#0;" onclick="toggleExtras();">&#9776;</a>
            </div>
            <section id="photos">
            <?php 

            if($stmt = $tk -> prepare("SELECT p.path FROM ". $pictures ." p LEFT JOIN ". $album ." a ON a.id = p.aid WHERE a.id = ?")){
                $stmt -> bind_param('i', $albumID);
                $stmt -> execute();
                $stmt -> bind_result($imageName);
                while($stmt -> fetch()){
					$image = $path . $imageName;
                    echo "<img src='$image'>";
                }
                $stmt -> close();
            }

            ?>
            </section>
        </div>
    </body>
</html>
