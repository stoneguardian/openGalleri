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
	//-------------------------------------//

	$mail = $_SESSION['username'];
	$db = new db();

	//Create user and get information
	$user = new user($db);
	$user -> addByMail($mail);

	$uid = $user -> getUid();
	$name = $user -> getName();

	//Get gravatar-url
	//$url = genGravatarURL($mail);

    //$formAction = "ZZZ_test.php?albumRouter=y&uid=" . $uid;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!--<meta charset="utf-8" />
        <title>Nytt Album</title>
		<link rel="stylesheet" href="../css/reset.css" type="text/css">
        <link rel="stylesheet" href="../css/styles.css" type="text/css">
        <link href='http://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet' type='text/css'>-->
		<?php include '../head.php';?>
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    </head>
    <body onload="load();">
		<input type="hidden" value="<?php echo $uid;?>" id="uid">
		<input type="hidden" value="<?php echo $mail;?>" id="mail">

		<div id="sidebar">
			<?php $user -> getSidebar(); ?>
		</div>
		<div id="beside">
			<h1 id="nAlbTitle">Nytt Album</h1>

			<div class="naStep" id="naAbout">
				<a href="#0" onclick="naAbout(false);" class="naStepTitle" id="naAboutTitle">+ Om Album <span class="hStatus" id="omStatus"></span></a>
				<div class="naContent">
					<div id="naAlbNameContainer">
						<input type="text" name="albumNavn" id="albumNavn" class="naAlbumName" maxlength="30" placeholder="Albumnavn" required onfocus="removeError('nameError');" onkeyup="naCheckLenght();">
						<div id="naAlbNameCount">0/30 tegn</div><div id="naAlbNameStatus"></div>
					</div>

					<div id="naAlbYearContainer">
						<label for="year">Hvilket år ble bildene tatt?</label>
						<input type="text" name="year" id="albumYear" maxlength="4" placeholder="YYYY" required onfocus="removeError('yearError');" onkeyup="naCheckYear();">
						<div id="naAlbYearStatus"></div>
					</div>


					<button class="btn-wanted" type="button" onclick="changeNameYear();">Lagre</button>
					<span id="basicError"></span>
				</div>
			</div>

			<div class="naStep">
				<span class="naStepTitle">- Bilder<span class="hStatus" id="picStatus"></span></span>
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
						<!--Venter...-->

						<!--<select id="coverBilde">
							<option value="0">---Velg coverbilde---</option>
						</select>
						<button onclick="selectCover();">Velg</button><div id="currentCover"></div>-->
					</div>

					<div id="bilder">
						<!--<h2>Bilder</h2>-->
					</div>

					<div id="result">
					</div>

				</div>
			</div>

			<div class="naStep">
				<span class="naStepTitle">- Rettigheter<span class="hStatus" id="rightStatus"></span></span>
			</div>

				<!--<div id="side2">
					<h1 id="nAlbTitle">Nytt Album</h1>


					<div id="omAlbum">
						<h2>Om album</h2>
						<div>
							<form action="../upload/upload.php?ajax=on" method="post" id="basicInfo">
								<input type="text" name="albumNavn" id="albumNavn" maxlength="30" placeholder="Albumnavn" required onfocus="removeError('nameError');">
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
							<!--Venter...-->

					<!--<select id="coverBilde">
						<option value="0">---Velg coverbilde---</option>
					</select>
					<button onclick="selectCover();">Velg</button><div id="currentCover"></div>-->
				<!--</div>

				<div id="bilder">
					<h2>Bilder</h2>
				</div>

				<div id="result">

				</div>
			</div>
		</div>-->

        <script src='../js/filedrag.js'></script>
		<script src="../js/nyttAlbum.js"></script>
		<script src="../js/globalFunctions.js"></script>
		<script>genAlbum();</script>
    </body>
</html>
