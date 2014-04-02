<?php
    session_start();
    if(!isset($_SESSION['username'])){
        header('Location:index.php');
    }

	$bruker = $_SESSION['username'];

	//include_once "../config/dbname.php";
	include '../sql/db.php';
	include 'gravatar.php';
	include 'userFunctions.php';

	//Hent gravatar
	$url = genGravatarURL($bruker, 70);
	$uid = getId($bruker);
	$name = getName($bruker);

    //$formAction = "test.php?albumRouter=y&uid=" . $uid;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Nytt Album</title>
		<link rel="stylesheet" href="../css/reset.css" type="text/css">
        <link rel="stylesheet" href="../css/styles.css" type="text/css">
        <link href='http://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet' type='text/css'>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
		<?php //Overfør variabler til Javascript
			//echo "<script>  var mail = $bruker; </script>";
		?>
    </head>
    <body onload="load();">
		<input type="hidden" value="<?php echo $uid;?>" id="uid">
		<input type="hidden" value="<?php echo $bruker;?>" id="mail">

		<div id="sidebar">
			<div id="loginImg">
				<div id="currentIcon" class="userIcon" style="background: url('<?php echo $url; ?>');"></div>
			</div>
			Hei, <?php echo $name; ?>
			<div class="side-cont no-top">
				<a href="../index.php?exit=true" class="side-btn" onclick="exit();">Logg ut</a>
			</div>

			<div class="side-cont">
				<span class="side-cont-title">Navigasjon</span>
				<a href="index.php" class="side-btn" onclick="exit();">Hjem</a>
				<a href="nyttAlbum.php" class="side-btn" onclick="exit();">Nytt album</a>
			</div>
		</div>

        <div id="side2">
            <h1>Nytt Album</h1>
			<div id="omAlbum">
				<h2>Om album</h2>
				<div>
					<form action="../upload/upload.php?ajax=on" method="post" id="basicInfo">
						<input type="text" name="albumNavn" id="albumNavn" placeholder="Albumnavn" required onfocus="removeError('nameError');">
						<label for="year">Hvilket år ble bildene tatt?</label><input type="text" name="year" id="albumYear" maxlength="4" placeholder="YYYY" required onfocus="removeError('yearError');">
						<a href="#0" id="nAsave" onclick="changeAlbum();">Lagre</a>
						<span id="submit"><input type="submit" value="Lagre"></span>
					</form>
					<span id="basicError"></span>
				</div>
				<div id="nAcover">
					<label>Velg coverbilde</label><br>
					<select id="coverBilde">
					</select>
					<a href="#0" id="nAsave" onclick="selectCover();">Lagre</a>
					<span id="currentCover"></span>
				</div>
			</div>
        </div>

		<div id="uplWrapper">
			<form id="upload" action="../upload/save.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="10000000">
				<a href="#0" onclick="$('#sti').click();"><div id="filedrag">Slipp bilder her, eller trykk for å laste opp...</div></a>
				<input type="file" id="sti" name="sti[]" multiple="multiple">
				<div id="submitbutton">
					<input type="submit" value="Last opp...">
				</div>
			</form>
			<div id="uplPad">
				<div id="process">
					<?php //echo $formLink;?>
					<!--Venter...-->

					<!--<select id="coverBilde">
						<option value="0">---Velg coverbilde---</option>
					</select>
					<button onclick="selectCover();">Velg</button><div id="currentCover"></div>-->
				</div>

				<div id="bilder">
					<h2>Bilder</h2>
				</div>

				<div id="result">

				</div>
			</div>
		</div>

        <script src='../js/filedrag.js'></script>
		<script src="../js/nyttAlbum.js"></script>
    </body>
</html>
