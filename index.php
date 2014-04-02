<?php
    if(isset($_GET['exit'])){
        if(isset($_SESSION['username'])){
            unset($_SESSION['username']);
        }
    }
?>

<!DOCTYPE html>
<!--suppress ALL -->
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <!--<link rel="stylesheet" type="text/css" href="css/reset.css">-->
		<link rel="stylesheet" href="css/styles.css" type="text/css">
        <link rel="stylesheet" href="css/index.css" type="text/css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script src="js/index.js"></script>
    </head>
    <body>
        <div id="backdrop"></div>
        
        <div id="center">
            <div id="bottomBar">
                    <a href="#0" id="btnKode" class="botBox botLeft" onclick="slideRouter('kode');">Jeg har en kode</a>
                    <a href="#0" id="btnLogin" class="botBox botRight" onclick="checkLogin();">Logg inn</a>
                    <a href="#0" id="btnLukk" class="botBox botRight botRightHidden" onclick="slideRouter('login');">Lukk</a>
                    <a href="#0" id="btnTilbake" class="botBox botRight botRightHidden" onclick="slideRouter('login');">Tilbake</a>
                    
                    <div id="loading" class="botBoxHidden">

                    </div><!--loading end-->
            </div><!--bottomBar end-->

            <div id="reqJS">OBS! Denne siden trenger JavaScript for å fungere optimalt</div>

            <div id="login" class="blue">
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
                <span>Brukerkontoens e-postadresse:</span>
                <form method="post" action="login/glemtPwd.php">
                    <input type="text" name="glemtMail" class="oneInput" placeholder="E-post..." required>
                    <input type="submit" name="nyttPwd" class="arrowSub" value="&rarr;">
                </form><!--form end-->
            </div><!--nyPwd end-->

            <div id="kode" class="blue kodeNed">
                <span>Kode:</span>
                <form>
                    <input type="text" name="kode" class="oneInput" placeholder="Kode..." required>
                    <input type="submit" name="chKode" class="arrowSub" value="&rarr;">
                </form><!--form end-->
            </div><!--kode end-->
            
        </div><!--center end-->

        <div id="foot">openGalleri - Versjon 0.1.1 (alpha)</div>
    </body>
</html>
