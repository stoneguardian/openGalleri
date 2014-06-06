<?php
    session_start();
    if(!isset($_SESSION['username'])){
        if(!isset($_GET['code'])){
            header('Location: ../index.php');
        }
    }

    //Databaseoppkobling
    require '../class/dbClass.php';
	require '../class/albumClass.php';
	require '../class/userClass.php';

	$key = $_SESSION['key'];

	//Create dependencies
	$db = new db();
	$user = new user($db);

	if(isset($_GET['id'])){
		$albumId = $_GET['id'];

		//Create array
		$value = array('id' => $albumId);

		//Create album-class
		$album = new album($value, $user, $db);
	}elseif(isset($_GET['code'])){
		$code = $_GET['code'];

		//Create array
		$value = array('code' => $_GET['code']);

		//Create album-class
		$album = new album($value, $user, $db, 'code');

		if($album == false){
			header('Location:../index.php?code=false');
		}
	}
	//Authenticate
	/*
	 * To be added
	 */

	//Get information
	$name = $album -> getName();
	$year = $album -> getYear();
	$cover = $album -> getCoverPath();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo $name; ?></title>
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
        <div id="cover" style="background: url('<?php echo "image.php?path=".$cover."&key=".$key;?>'); background-repeat: no-repeat; background-position: center 0; background-size: cover;"></div>
        <div id="title">
            
            <span id="name"><?php echo $name; ?></span><span class="rot">(<?php echo $year; ?>)</span>
            <!--<div id="inAlbum">
                <?php 
                    //foreach($inAlbum as $user){
                    //    echo '<div class="user" style="background: url(' . "'" . genGravatarURL($user, '40') . "'" .');"></div>';
                    //}

                    echo '<div class="user" style="background: url(' . "'" . genGravatarURL($inAlbum[0], '35') . "'" .');"></div>';
                ?>
            </div>-->
        </div>
        <div id="page">
            <div id="extras">
                <input type="hidden" id="showHide" value="0">
                <a id="toggle" href="#0" onclick="toggleExtras();">&#9776;</a>
            </div>
            <section id="photos">
				<!--<div id="albDescription">
					<h2>Om albumet</h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce ornare tortor ipsum. Nulla ornare nibh id dui cursus, eget pretium arcu vestibulum. Fusce mattis, felis a placerat fermentum, dolor dolor suscipit turpis, sed condimentum nisl dui vitae sapien. In aliquam velit sit amet urna egestas, id lobortis tortor tempor. Phasellus eu arcu sollicitudin, eleifend mauris in, pretium metus. Nullam vel ante ut tellus malesuada ullamcorper. Nulla at varius leo. Morbi pharetra tortor cursus, pellentesque elit in, laoreet arcu. Ut nisi sem, tristique faucibus nisi ut, facilisis semper libero. In sagittis orci eu pulvinar aliquam. Nullam ultrices tortor magna, at varius leo laoreet quis. Pellentesque dignissim, leo in convallis aliquam, odio nunc adipiscing leo, in elementum est nunc quis metus.</p>
				</div>-->
				<?php
					$pictures = $album -> getImages();

					if($pictures == false){
						echo "Albumet har ingen bilder";
					}else{
						foreach($pictures as $path){
							echo '<img src="image.php?path='.$path.'&key='.$key.'">';
						}
					}
				?>
            </section>
        </div>
    </body>
</html>
