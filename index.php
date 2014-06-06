<?php
    if(isset($_GET['exit'])){
        if(isset($_SESSION['username'])){
            /*unset($_SESSION['username']);
			unset($_SESSION['key']);*/
			session_unset();
			session_destroy();
			setcookie('PHPSESSID', '', time() - 3600);
        }
    }

	$version = '0.2.0';
?>

<!DOCTYPE html>
<!--suppress ALL -->
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <link rel="stylesheet" type="text/css" href="css/reset.css">
		<link rel="stylesheet" href="css/styles.css" type="text/css">
        <link rel="stylesheet" href="css/index.css" type="text/css">
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script src="js/index.js"></script>
    </head>
    <body>
        <div id="backdrop"></div>
        
        <div id="center">
            <div id="bottomBar">
                    <!--<a href="#0" id="btnKode" class="botBox botLeft" onclick="slideRouter('kode');">Jeg har en kode</a>-->
                    <a href="#0" id="btnLogin" class="botBox botRight botRightHidden" onclick="checkLogin();">Logg inn</a>
                    <a href="#0" id="btnLukk" class="botBox botRight botRightHidden" onclick="slideRouter('login');">Lukk</a>
                    <a href="#0" id="btnTilbake" class="botBox botRight botRightHidden" onclick="slideRouter('login');">Tilbake</a>
                    
                    <div id="loading" class="botBoxHidden">

                    </div><!--loading end-->
            </div><!--bottomBar end-->

            <div id="reqJS"><h2>OBS!</h2> <p>Denne siden trenger JavaScript for å fungere</p></div>

            <div id="login" class="blue loginNed">
                <form action="login/login.php" method="post">
                    
                    <div id="loginImg">
						<div id="currentIcon" class="userIcon"></div>
                    </div>
            
                    <div id="textInput">
                        <input type="text" name="brukernavn" id="bnavn" class="login" placeholder="E-post..." onfocus="removeRed('#bnavn');" onblur="uImg();">
                        <input type="password" name="passord" id="pwd" class="login" placeholder="Passord..." onfocus="removeRed('#pwd');">
                    </div><!--textInput end-->
            
                    <div class="box center"><input type="checkbox" name="husk" id="husk"><label for="husk">Husk meg</label></div>
                    <div class="box center"><a href="#0" class="link" id="glemt" onclick="slideRouter('nyttPwd');">Glemt passord?</a></div>
                    <div id="sub">
                        <input type="submit" id="submit" class="botBox botRight" value="Logg inn">
                    </div><!--sub end-->
                
                </form><!--form end-->
            </div><!--login end-->

            <div id="nyPwd" class="blue nyPwdNed">
				<label for="npInput" id="npLabel">Brukerkontoens e-postadresse:</label>
				<form method="post" action="login/glemtPwd.php">
					<div id="nyPwdInput">
						<div id="npFlex" class="npFlex">
							<input type="text" name="npMail" id="npMail" class="en oneInput" onfocus="npRmRedBorder();">
							<span class="npStatus">Sender e-post med resetkode...</span>
							<span id="npMailStatus" class="npStatus"><!-- Mail status --></span>
							<input type="text" name="npCode" id="npCode" class="oneInput" onfocus="npRmRedBorder();">
							<span class="npStatus">Sjekker kode</span>
							<span id="npCodeStatus" class="npStatus"><!-- Kode status --></span>
							<input type="password" name="npPwd" id="npPwd" class="oneInput" onfocus="npRmRedBorder();">
							<input type="password" name="npPwdRep" id="npPwdRep" class="oneInput" onfocus="npRmRedBorder();">
							<span id="npPwdStatus" class="npStatus"><!-- Passord status --></span>
						</div>
						<a href="#0" class="arrowSub" onclick="npRouter();"><i class="fa fa-angle-right"></i></a>
					</div>

					<div class="step"><span id="nyPwdStep">1</span>/4</div>
				</form><!--form end-->
            </div><!--nyPwd end-->

            <!--<div id="kode" class="blue kodeNed">
                <label for="kode">Kode:</label>
                <form>
                    <input type="text" name="kode" class="oneInput en" placeholder="Kode..." required>
                    <input type="submit" name="chKode" class="arrowSub" value="&rarr;">
                </form><!--form end--><!--
            </div><!--kode end-->
            
        </div><!--center end-->

        <div id="foot">openGalleri - Versjon <?php echo $version ?> (alpha)</div>
    </body>
</html>
