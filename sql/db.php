<?php
	//echo "db.php her! <br>";

	include_once "../config/dbConnect.php";
	include_once "config/dbConnect.php";

	include_once "../config/dbname.php";
	include_once "config/dbname.php";

	//Kobler til databasen
	global $dbServer, $dbUsername, $dbPassword, $db;
	$tk = new mysqli($dbServer, $dbUsername, $dbPassword, $db);

	/*if ($tk->connect_error) {
		trigger_error('Tilkobling misslyktes: ' . mysqli_connect_error(), E_USER_ERROR);
	}*/

	$tk->set_charset("utf8");
	//-------------------

	//Funksjonene ask, add og antRad brukes kun ved statiske SQL-spørringer hvor det ikke er
	//bruker-input. Ved bruker-input brukes prepared statements. Da inkluderes denne filen kun
	//for oppkoblingskoden.

	function ask($sql) { //Spørre-funksjon, brukes for å hente ut data til en array

		global $tk;

		$tk->set_charset("utf8"); //For "Æ-Ø-Å"
		$query = $tk->query($sql); //Utfører spørringen

		if ($query === false) {
			trigger_error('Feil SQL: ' . $sql . 'Error: ' . $tk->error, E_USER_ERROR);
			return false;
		} else {
			$query->data_seek(0);
			$array = $query->fetch_all(MYSQLI_ASSOC);
			while ($row = $query->fetch_assoc()) { //Legger resultatet i en array
				$array[] = $row;
			}

			return $array;
		}
	}

	function add($sql) { //Legge-til funksjon, brukes for å sette data inn i databasen
		global $tk;

		$tk->set_charset("utf8"); //For "Æ-Ø-Å"
		$query = $tk->query($sql); //Utfører spørringen

		if ($query === false) {
			trigger_error('Feil SQL: ' . $sql . 'Error: ' . $tk->error, E_USER_ERROR);
			return false;
		} else {
			return true;
		}
	}

	function antRad($sql) { //Funksjonen sjekker hvor mange rader en SQL-setning gir
		global $tk;

		$query = $tk->query($sql);

		if ($query === false) {
			trigger_error('Feil SQL: ' . $sql . 'Error: ' . $tk->error, E_USER_ERROR);
			return "0";
		} else {
			$antRader = $query->num_rows . "<br>";
			return $antRader;
		}
	}