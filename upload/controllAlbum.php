<?php
    include_once "../sql/db.php";

	$switch = $_POST['switch'];

	function renameDir($oldName, $oldYear, $newName, $newYear, $mail){
		$dir = "../album/$mail/";
		$oldDir = $dir . $oldYear . "-" . $oldName;
		$newDir = $dir . $newYear . "-" . $newName;

		rename($oldDir, $newDir);
	}

	if($switch == '0' and isset($_POST['uid'])){
			//Genererer ett årstall og et unikt navn
			$name = $_POST['uid'] . date('dmYHis');
			$year = date('Y');

			//Oppretter album
			if($stmt = $tk -> prepare("INSERT INTO ". $album ." VALUES(NULL, ?, ?, ?, 1)")){
				$stmt -> bind_param("isi", $_POST['uid'], $name, $year);
				$stmt -> execute();
				$stmt -> close();
			}

			//Henter albumets album-id
			if($stmt = $tk -> prepare("SELECT id FROM ". $album ." WHERE uid = ? AND name = ? and year = ?")){
				$stmt -> bind_param("isi", $_POST['uid'], $name, $year);
				$stmt -> execute();
				$stmt -> bind_result($albumID);
				$stmt -> fetch();
				$stmt -> close();
				$return = array('albumID' => $albumID, 'alName' => $name, 'alYear' => $year);
			}else{
				$return = array('albumID' => false);
			}

		mkdir("../album/spretten48@gmail.com/$year-$name", '660', TRUE);

	}elseif($switch == '1' and isset($_POST['albumID']) and isset($_POST['albumName']) and isset($_POST['albumYear']) and isset($_POST['count']) and isset($_POST['uid']) and isset($_POST['mail'])){
			//Sjekker om det finnes album med samme navn
			if($stmt = $tk -> prepare("SELECT * FROM ". $album ." WHERE uid = ? AND name = ? and year = ?")){
				$stmt -> bind_param('isi', $_POST['uid'], $_POST['albumName'], $_POST['albumYear']);
				$stmt -> execute();
				$stmt -> store_result();
				$decide = $stmt -> num_rows;
				$stmt -> close();
			}else{
				$decide = 0;
			}

			if($decide > 0){
				$return = array('updateAlbum' => false, 'errorMsg' => 'Det finnes allerede et slikt album');
			}else{ //Dersom unik,
				//Hent gammelt navn og årstall
				if($stmt = $tk -> prepare("SELECT name, year FROM ". $album ." WHERE id = ?")){
					$stmt -> bind_param("i", $_POST['albumID']);
					$stmt -> execute();
					$stmt -> bind_result($alName, $alYear);
					$stmt -> fetch();
					$stmt -> close();
				}

				//Endre navn og årstall
				if($stmt = $tk -> prepare("UPDATE ". $album ." SET name = ?, year = ? WHERE id = ?")){
					$stmt -> bind_param("sii", $_POST['albumName'], $_POST['albumYear'], $_POST['albumID']);
					$stmt -> execute();
					$stmt -> close();

					renameDir($alName, $alYear, $_POST['albumName'], $_POST['albumYear'], $_POST['mail']);

					$return = array('updateAlbum' => true, 'oldN' => $alName, 'oldY' => $alYear);
				}else{
					$return = array('updateAlbum' => false, 'errorMsg' => 'Databasefeil');
				}
			}
	}elseif($switch == '2' and isset($_POST['albumID']) and isset($_POST['albumCover'])){//Oppdater Coverbilde
			if($stmt = $tk -> prepare("UPDATE ". $album . " SET cover = ? WHERE id = ?")){
				$stmt -> bind_param("ii", $_POST['albumCover'], $_POST['albumID']);
				$stmt -> execute();
				$stmt -> close();
				$return = array('cover' => true);
			}else{
				$return = array('cover' => false);
			}
	}elseif($switch == '3' and isset($_POST['albumID']) and isset($_POST['albumName']) and isset($_POST['albumYear'])){
		//Slett bilder (dersom det er noen)
		/*if($stmt = $tk -> prepare("DELETE FROM ". $pictures ." WHERE aid = ?")){
			$stmt -> bind_param('i', $_POST['albumID']);
			$stmt -> execute();
			$stmt -> close();
		}*/

		//Slett album
		if($stmt = $tk -> prepare("DELETE FROM ". $album ." WHERE id = ?")){
			$stmt -> bind_param('i', $_POST['albumID']);
			$stmt -> execute();
			$stmt -> close();


			$return = array('rm' => true);
		}else{
			$return = array('rm' => false, 'errorMsg' => 'Databasefeil');
		}
	}else{
		$return = array('error' => true, 'errorMsg' => 'For få variabler', 'switch' => $switch);
	}

    echo json_encode($return);
    header("Content-Type: application/json", true);