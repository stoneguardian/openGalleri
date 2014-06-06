<?php

	include_once '../sql/db.php';

	//Hent variabler
	$year = $_GET['year'];
	$name = $_GET['name'];
	$mail = $_GET['mail'];
	$albumID = $_GET['aid'];

	$destFolder = "../album/$mail/$year-$name/";
	$prefix = "IMG_";

	//Sjekker for ajax
	if(isset($_GET['ajax']) and $_GET['ajax'] == 'on'){
		//henter ekstra ajax-variabler
		$nr = $_GET['nr'];

		if($_GET['type'] == 'image/png'){ //dersom png fil bruk .png som filending
			$ending = ".png";
		}elseif($_GET['type'] == "image/jpeg"){ //dersom jpeg, bruk .jpg som filending
			$ending = ".jpg";
		}

		//Skriver stier
		$finalDest = $destFolder . $prefix . $nr . $ending;
		$dbName = $prefix . $nr . $ending;

		//Lagre filen
		file_put_contents(
			$finalDest,
			file_get_contents('php://input')
		);

		//Registrerer filen i databasen
		if($stmt = $tk -> prepare("INSERT INTO ". $pictures ." VALUES(NULL, ?, ?, NULL, ?)")){
			$stmt -> bind_param("iis", $albumID, $nr, $dbName);
			$stmt -> execute();
			$stmt -> close();
		}

		echo " | fil lagret og i databasen";
	}else{
		echo "Fungerer ikke enda...";
	}

	// Lagrer filen
	/*file_put_contents(
		$name,
		file_get_contents('php://input')
	);*/

	/*function fileType($input){
		if($input == 'image/png'){ //dersom png fil bruk .png som filending
			return ".png";
		}elseif($input == "image/jpeg"){ //dersom jpeg, bruk .jpg som filending
			return ".jpg";
		}
	}*/

	/*
	echo "før db | ";

    require '../sql/db.php';

	echo "etter db | ";

    $user = $_GET['user'];
    $albumName = $_GET['name'];
    $year = $_GET['year'];
    $albumID = $_GET['id'];

    //Variabler
    $location = "../album/" . $user . "/" . $year . " - " . $albumName . "/";
    $prefix = "img_";
    $counter = $location . "teller.txt";

    //function updateCount() {}

    if(!is_dir($location)){ //Sjekker om mappen eksisterer
        mkdir($location, '777', TRUE); //Hvis ikke opprett de(n)
        echo "laget mappe " . $location . " | ";
    }

    if(!file_exists($counter)){ //Sjekker om filen teller eksisterer
        $opprett = fopen($counter, 'w');//Hvis ikke, opprett den og skriv 0
        fwrite($opprett, '0');
        fclose($opprett);
    }

	echo "før åpne fil | ";

    //Åpner teller-filen og leser tallet som står i den
    $count = fopen($counter, 'r');
    $ofset = fgets($count);
    fclose($count);

    $fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);

    if($fn){

        if($fn == 'image/png'){ //dersom png fil bruk .png som filending
        $ending = ".png";
        }elseif($fn == "image/jpeg"){ //dersom jpeg, bruk .jpg som filending
        $ending = ".jpg";
        }

        $ofset = 1; //Oppdaterer filnr, så filer ikke blir overskrevet
        
		echo "ofset: " . $ofset . " | ";

        //Oppdaterer teller-filen med sist brukte tall
        $newCount = fopen($counter, 'w');
        fwrite($newCount, $ofset);
        fclose($newCount);

        $name = $location . $prefix . $ofset . $ending; //Lager navne-variabelen
		//$dbName = "../" . $name;

        // Lagrer filen
	    file_put_contents(
		    $name,
		    file_get_contents('php://input')
	    );

        echo "AlbumID:' $albumID ' | ";

		echo "INSERT INTO ". $pictures ." VALUES (NULL, ?, ?, NULL, ?); |";

        //Oppdaterer databasen
        if($stmt = $tk -> prepare("INSERT INTO ". $pictures ." VALUES (NULL, ?, ?, NULL, ?);")){
            $stmt -> bind_param("iis", $albumID, $ofset, $name);
            $stmt -> execute();
            $stmt -> close();
        }else{
			echo " db feilet |";
		}

	    echo "$navn uploaded | ";
	    //exit();
    }else{
        foreach($_FILES['sti']['error'] as $id => $err){
            if($err == UPLOAD_ERR_OK) {
                //Dersom ingenting blir mottat, avslutt og skriv ut feilmelding
                if(empty($_FILES['sti']['name'])){
                    die("Ingen fil ble valgt");
                }
    
                //Sjekker at filtypen er enten .jpg eller .png stemmer (og oppretter filendinger)
                $type = $_FILES['sti']['type'][$id];
                if($type == 'image/png'){ //dersom png fil bruk .png som filending
                    $ending = ".png";
                }elseif($type == "image/jpeg"){ //dersom jpeg, bruk .jpg som filending
                    $ending = ".jpg";
                }else{ //Hvis det ikke er en av de to, avslutt og skriv ut feilmelding
                    die("Filformatet er ikke støttet!"); 
                }
    
                $ofset += 1; //Oppdaterer filnr, så filer ikke blir overskrevet

                //Oppdaterer teller-filen med sist brukte tall
                $newCount = fopen($counter, 'w');
                fwrite($newCount, $ofset);
                fclose($newCount);
                
                $name = $location . $prefix . $ofset . $ending; //Lager navne-variabelen
                
                $tmp_plassering = $_FILES['sti']['tmp_name'][$id]; //Finner hvor filen er midlertidig lagret
                
                move_uploaded_file($tmp_plassering, $name) or die("Klarte ikke flytte fil"); //Flytter filen til ny plassering
    
                //Oppdaterer databasen
                if($stmt = $tk -> prepare("INSERT INTO b_bilder VALUES (NULL, NULL, ?);")){
                    $stmt -> bind_param("s", $name);
                    $stmt -> execute();
                    $stmt -> close();
                }
        
                if($stmt = $tk -> prepare("SELECT id FROM b_bilder WHERE path = ?")){
                    $stmt -> bind_param("s", $name);
                    $stmt -> execute();
                    $stmt -> bind_result($bildeID);
                    $stmt -> fetch();
                    $stmt -> close();
                }   
                
                if($stmt = $tk -> prepare("INSERT INTO b_biKobAl VALUES(NULL, ?, ?)")){
                    $stmt -> bind_param("ii", $albumID, $bildeID);
                    $stmt -> execute();
                    $stmt -> close();
                }   
                
                echo '<img src="' . $name .'" alt="lastetOpp" width="500px">'; //Viser bildet som er lastet opp
            }   
        }   
    }
    
    

?>