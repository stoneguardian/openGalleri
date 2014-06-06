<?php ?>

<div id="progress" class="configBoxContainer">
	<div id="progressNumber">Framdrift</div>
	<div id="progressBar"></div>
	<div id="progressUnder"></div>
</div>

<div id="setupForms">
	<div id="database" class="configBox">
		<span id="dbTitle" class="title">Database<span id="dbSubTitle" class="subTitle"> - Tilkoblingsinstillingene til databasen</span></span>
		<form method="post">
			<table>
				<tr>
					<td><label for="dbHost">Host</label></td>
					<td><input type="text" id="dbHost" name="dbHost" value="<?php echo $dbServer ?>"></td>
					<td>Hostname eller IP-adresse til MySQL-serveren som skal brukes</td>
				</tr>
				<tr>
					<td><label for="dbName">Database</label></td>
					<td><input type="text" id="dbName" name="dbName" value="<?php echo $db ?>"></td>
					<td>Navnet på MySQL-databasen som bildeGalleri skal bruke</td>
				</tr>
				<tr>
					<td><label for="dbUser">Brukernavn</label></td>
					<td><input type="text" id="dbUser" name="dbUser" value="<?php echo $dbUsername ?>"></td>
					<td>MySQL brukernavnet...</td>
				</tr>
				<tr>
					<td><label for="dbPwd">Passord</label></td>
					<td><input type="password" id="dbPwd" name="dbPwd"></td>
					<td>... og passordet</td>
				</tr>
				<tr>
					<td><label for="dbPref">Tabell prefix</label></td>
					<td><input type="text" id="dbPref" name="dbPref" value="<?php echo $prefix ?>"></td>
					<td>For å kjøre flere bildeGalleri i samme database (hvis ikke er default ok)</td>
				</tr>
			</table>
		</form>
		<a href="#0" class="next" onclick="triggerNext('mail');">Neste</a>
	</div>

	<div id="mail" class="configBox futureStep">
		<span id="mailTitle" class="title"">E-post<span id="mailSubTitle" class="subTitle"> - Instillinger for å sende e-post</span></span>
		<form method="post">
			<table>
				<tr>
					<td><label for="mailFromName">Fra navn</label></td>
					<td><input type="text" id="mailFromName" name="mailFromName" value="<?php echo $fromName ?>"></td>
					<td>Navnet som skal vises i 'fra'-feltet hos mottaker</td>
				</tr>
				<tr>
					<td><label for="mailFromMail">Fra e-post</label></td>
					<td><input type="text" id="mailFromMail" name="mailFromMail" value="<?php echo $fromMail ?>"></td>
					<td>E-postadressen bildeGalleri skal sende fra</td>
				</tr>
				<tr>
					<td><label for="mailHost">Host</label></td>
					<td><input type="text" id="mailHost" name="mailHost" value="<?php echo $host ?>"></td>
					<td>Hostname eller IP-adressen til e-posttjeneren (f.eks mail.example.com)</td>
				</tr>
				<tr>
					<td><label for="mailPort">Portnr.</label></td>
					<td><input type="text" id="mailPort" name="mailPort" value="<?php echo $port ?>"></td>
					<td>Portnummmeret e-posttjeneren bruker </td>
				</tr>
				<tr>
					<td><label for="mailUser">Brukernavn</label></td>
					<td><input type="text" id="mailUser" name="mailUser" value="<?php echo $username ?>"></td>
					<td>Brukernavnet til e-posttjeneren</td>
				</tr>
				<tr>
					<td><label for="mailPwd">Passord</label></td>
					<td><input type="password" id="mailPwd" name="mailPwd"></td>
					<td>Passordet til e-posttjeneren</td>
				</tr>
			</table>
		</form>
		<a href="#0" class="next" onclick="triggerNext('user');">Neste</a>
	</div>

	<div id="user" class="configBox futureStep">
		<span id="userTitle" class="title">Bruker<span id="userSubTitle" class="subTitle"> - Opprett administrator</span></span>
		<form method="post">
			<p>Vi trenger litt informasjon for å opprette din bruker (dette vil også være administratorbrukeren for nettsiden):</p>
			<table>
				<tr>
					<td><label for="usrFna">Fornavn</label></td>
					<td><input type="text" id="usrFna" name="usrFna"></td>
				</tr>
				<tr>
					<td><label for="usrLna">Etternavn</label></td>
					<td><input type="text" id="usrLna" name="usrLna"></td>
				</tr>
				<tr>
					<td><label for="usrMai">E-post</label></td>
					<td><input type="text" id="usrMai" name="usrMai"></td>
				</tr>
				<tr>
					<td><label for="usrPwd">Passord</label></td>
					<td><input type="password" id="usrPwd" name="usrPwd"></td>
				</tr>
			</table>
		</form>
		<a href="#0" class="next" onclick="triggerNext('progress');">Fullfør</a>
	</div>
</div>
<!--<div id="status">
	<!--<div id="done" class=""></div>-->
	<!--<div id="brukerOK" class=""></div>
	<div id="mailOK" class=""></div>
	<div id="tablesOK" class=""></div>
	<div id="dbnameOK" class=""></div>
	<div id="dbConfOK" class=""></div>
	<!--<div id="temp" class="progress">- Framdrift -</div>-->
<!--</div>-->
<!--</div>-->

<!--	<p id="info">Dersom du vil endre på mail og databaseinstillinger senere finner du config-filene i mappen 'config'</p>
	<p> Config-filen 'dbname.php' frarådes det å endre på</p>-->