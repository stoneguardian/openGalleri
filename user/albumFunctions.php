<?php

	//Require -----------------------------//
	// - '../config/dbname.php';
	// - '../sql/db.php';
	// - 'userFunctions.php';
	//-------------------------------------//


	include_once '../config/siteconfig.php';
	include 'userFunctions.php';

	function albumCount($uid){
		global $tk;
		global $album;
		global $users;

		if($stmt = $tk -> prepare("SELECT * FROM ". $album ." a LEFT JOIN ". $users ." u ON u.id = a.uid WHERE u.id = ?")){
			$stmt -> bind_param("i", $uid);
			$stmt -> execute();
			$stmt -> store_result();
			$decide = $stmt -> num_rows;
			$stmt -> close();
			return $decide;
		}else{
			return false;
		}
	}

	function scaleText($string){
		$defSize = 32;
		$factor = 2;
		$length = strlen($string);
		if($length < 17){
			return $defSize;
		}else{
			$divide = round($length / $factor, 0 , PHP_ROUND_HALF_DOWN);
			return $ans = $defSize - $divide;
		}
	}

	// Album permissions
	// 1 Read
	// 2 Partial write (add images only)
	// 3 = 1 + 2 (read + write images)
	// 4 = Full write (name, year, etc.)
	// 5 = 1 + 4 (read + full write)
	//


	class album {

		public $conStatus;

		//Private variables
		//- Album
		private $albumId;
		private $creatorId;
		private $creatorMail;
		private $name;
		private $year;
		private $description;
		private $coverNumber;

		//- AlbumAccess
		private $authType;
		private $authId;
		private $accessLevel;

		//- Pictures
		private $imageNr;
		private $imagePrefix;
		private $imageType;
		private $imageName;
		private $imagePath;

		//- Status
		private $lastStatusCode;
		private $lastStatusMsg;

		//Get album information from albumId
		private function populate(){
			global $tk, $album;

			if($stmt = $tk -> prepare("SELECT creatorId, name, year, description, cover FROM ".$album." WHERE id = ?")){
				$stmt -> bind_param('i', $this -> albumId);
				$stmt -> execute();
				$stmt -> bind_result($this -> creatorId, $this -> name, $this -> year, $this -> description, $this -> coverNumber);
				$stmt -> fetch();
				$stmt -> close();
			}

			$getMail = new user();
			$getMail -> addUserById($this -> creatorId);
			$this -> creatorMail = $getMail -> getMail();
		}

		//Create new album
		public function createAlbum($userId){
			global $tk, $album, $tblAlbumAccess, $albumPath, $serverOS;

			$this -> creatorId = $userId;

			//generate unique name (based on date and time)
			$this -> name = $this -> creatorId . date('dmYHis');
			$this -> year = date('Y');

			//Create album in db
			if($stmt = $tk -> prepare("INSERT INTO ". $album ." VALUES(NULL, ?, ?, ?, NULL, 1)")){
				$stmt -> bind_param('isi', $this -> creatorId, $this -> name, $this -> year);
				$stmt -> execute();
				$stmt -> close();
				$this -> conStatus = true;
			}

			//Get albumId from newly created album
			if($stmt = $tk -> prepare("SELECT id FROM ". $album ." WHERE creatorId = ? AND name = ? and year = ?")){
				$stmt -> bind_param('isi', $this -> creatorId, $this -> name, $this -> year);
				$stmt -> execute();
				$stmt -> bind_result($this -> albumId);
				$stmt -> fetch();
				$stmt -> close();
				$this -> conStatus = true;
			}

			$permissionType = 'user';
			$permissionNr = 5;

			//Set permissions
			if($stmt = $tk -> prepare("INSERT INTO ". $tblAlbumAccess ." VALUES(NULL, ?, ?, ?, ?)")){
				$stmt -> bind_param('isii', $this -> albumId, $permissionType, $this -> creatorId, $permissionNr);
				$stmt -> execute();
				$stmt -> close();
				$this -> conStatus = $permissionNr;
			}
		}

		public function setAlbumId($albumId){
			$this -> albumId = $albumId;
			$this -> populate();
		}

		public function setAuth($type = 'user', $id){
			$this -> authType = $type;
			$this -> authId = $id;
		}

		//Add picture
		public function addImage($imgNr, $type){
			global $tk, $pictures;

			$this -> imageNr = $imgNr;
			$this -> getImageType($type);
			$this -> setImagePrefix();
			$this -> setImageName();
			$this -> setImgPath();

			//Update DB
			if($stmt = $tk -> prepare("INSERT INTO ".$pictures." VALUES(NULL, ?, ?, NULL, ?)")){
				$stmt -> bind_param('iis', $this -> albumId, $this -> imageNr, $this -> imageName);
				$stmt -> execute();
				$stmt -> close();
				return $this -> imagePath;
			}else{
				return false;
			}
		}

		private function getImageType($type){
			if($type == 'image/png'){ //dersom png fil bruk .png som filending
				$this -> imageType = 'png';
				return true;
			}elseif($type == "image/jpeg"){ //dersom jpeg, bruk .jpg som filending
				$this -> imageType = 'jpg';
				return true;
			}else{
				return false;
			}
		}

		private function setImagePrefix(){
			$this -> imagePrefix = $this -> name . '-' . $this -> year . '_';
		}

		private function setImageName(){
			$this -> imageName = $this -> imagePrefix . $this -> imageNr . '.' . $this -> imageType;
		}

		private function setImgPath(){
			global $serverOS;

			if($serverOS == 'Windows'){
				$this -> imagePath = '\\' . $this -> creatorMail . '\\' . $this -> year . '-' . $this -> name . '\\' . $this -> imageName;
			}else if($serverOS == 'Linux'){
				$this -> imagePath = '/' . $this -> creatorMail . '/' . $this -> year . '-' . $this -> name . '/' . $this -> imageName;
			}
		}

		// ----------------------------------------- //

		public function testAuth(){
			$this -> checkAccess();
		}

		//Check album access
		private function checkAccess(){
			if($this -> authType == 'code'){
				$this -> checkCode();
			}else if ($this -> authType == 'user'){
				$this -> checkUser();
			}else{
				$this -> accessLevel = 0;
			}
		}

		private function checkCode(){
			global $tk, $album, $tblAlbumAccess, $tblCode;

			//Check number of rows
			if($stmt = $tk -> prepare("SELECT * FROM ". $tblAlbumAccess . " albAccess LEFT JOIN ". $album ." album ON album.id = albAccess.albumId LEFT JOIN ". $tblCode ." code ON albAccess.otherId = user.id WHERE album.id = ? AND user.id = ? AND albAccess.type = 'code'")){
				$stmt -> bind_param('ii', $this -> albumId, $this -> authId);
				$stmt -> execute();
				$stmt -> store_result();
				$decide = $stmt -> num_rows;
				$stmt -> close();
			}

		}

		private function checkUser(){
			global $tk, $album, $tblAlbumAccess, $users;

			//Check number of rows
			if($stmt = $tk -> prepare("SELECT * FROM ". $tblAlbumAccess . " albAccess LEFT JOIN ". $album ." album ON album.id = albAccess.albumId LEFT JOIN ". $users ." user ON albAccess.otherId = user.id WHERE album.id = ? AND user.id = ? AND albAccess.type = 'user'")){
				$stmt -> bind_param('ii', $this -> albumId, $this -> authId);
				$stmt -> execute();
				$stmt -> store_result();
				$decide = $stmt -> num_rows;
				$stmt -> close();
			}

			if($decide > 0){
				if($stmt = $tk -> prepare("SELECT albAccess.permission FROM ". $tblAlbumAccess . " albAccess LEFT JOIN ". $album ." album ON album.id = albAccess.albumId LEFT JOIN ". $users ." user ON albAccess.otherId = user.id WHERE album.id = ? AND user.id = ? AND albAccess.type = 'user'")){
					$stmt -> bind_param('ii', $this -> albumId, $this -> authId);
					$stmt -> execute();
					$stmt -> bind_result($this -> accessLevel);
					$stmt -> fetch();
					$stmt -> close();
				}else{
					$this -> accessLevel = 9;
				}
			}else{
				$this -> accessLevel = 0;
			}
		}

		public function getAccessLevel(){
			return $this -> accessLevel;
		}
	}