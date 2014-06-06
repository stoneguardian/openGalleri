<?php

	//Require -----------------------------//
	// - '../sql/dbClass.php';
	// - 'userFunctions.php';
	//-------------------------------------//

	include_once '../config/siteConf.php';

	function randomPassword() {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$i = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}

	function decodeAlbumCode($code){
		$array = str_split($code);
		$albumId = $array[0];
		$codeCode = "";
		for($i = 1; $i <= 8; $i++){
			$codeCode = $codeCode . $$array[$i];
		}
		$return = array('albumId' => $albumId, 'code' => $codeCode);
		return $return;
	}

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

		//Connections
		private $sql;
		private $user;

		//Private variables
		//- Album
		private $albumId;
		private $creatorId;
		private $creatorMail;
		private $name;
		private $year;
		private $description;
		private $coverNumber;
		private $folder;

		//- AlbumAccess
		private $authType;
		private $authId;
		private $accessLevel;

		//- Pictures
		private $imageNr;
		private $imagePrefix = "IMG_";
		private $imageType;
		private $imageName;
		private $imagePath;

		//- Status
		private $lastStatusCode;
		private $lastStatusMsg;

		//Construct
		public function __construct($info, user $user, db $db, $type = 'id'){
			global $tblName;

			$this -> sql = $db;
			$this -> user = $user;

			//Valid types = 'id', 'uny', 'new'
			if($type == 'id'){ //Get info based on albumID
				$this -> albumId = $info['id'];
				return $this -> fromId();
			}elseif($type == 'uny'){ //Get info based on userID, name and year
				$query = "SELECT id, description, cover FROM ".$tblName['albm']." WHERE creatorId = :cid AND name = :name AND year = :year";
				$paramaters = array('cid' => $info['cid'], 'name' => $info['name'], 'year' => $info['year']);
				$exist = $this -> sql -> getNumRows($query, $paramaters);

				if($exist == 1){
					$result = $this -> sql -> ask($query, $paramaters);
					$this -> populate($result[0]['id'], $info['cid'], $info['name'],  $info['year'], $result[0]['description'], $result[0]['cover']);
					return true;
				}else{
					return false;
				}
			}elseif($type == 'code'){
				$code = $info['code'];

				//Get albumId
				$array = str_split($code);
				$this -> albumId = $array[0];

				//Check DB for code
				$query = "SELECT permission FROM ".$tblName['acce']." a LEFT JOIN ".$tblName['code']." c ON a.otherId = c.id WHERE a.type = 'code' AND c.code = :code AND a.albumId = :albId";
				$parameters = array('code' => $code, 'aøbId' => $this -> albumId);

				if($this -> sql -> getNumRows($query, $parameters) == 1){
					$result = $this -> sql -> ask($query, $parameters);
					$this -> accessLevel = $result[0]['permission'];
					return $this -> fromId();
				}else{
					return false;
				}
			}else if($type == 'new'){
				$this -> creatorId = $info['cid'];
				$this -> user -> addById($this -> creatorId);
				$this -> creatorMail = $this -> user -> getMail();
				return true;
			}else{
				return false;
			}
		}

		private function fromId(){
			global $tblName;
			$query = "SELECT creatorId, name, year, description, cover FROM ".$tblName['albm']." WHERE id = :id";
			$parameter = array('id' => $this -> albumId);
			$exist = $this -> sql -> getNumRows($query, $parameter);

			if($exist == 1){
				$result = $this -> sql -> ask($query, $parameter);
				$this -> populate($this -> albumId, $result[0]['creatorId'], $result[0]['name'], $result[0]['year'], $result[0]['description'], $result[0]['cover']);
				return true;
			}else{
				return false;
			}
		}

		// Set private variables
		private function populate($id, $creatorId, $name, $year, $description, $cover){
			$this -> albumId = $id;
			$this -> creatorId = $creatorId;
			$this -> name = $name;
			$this -> year = $year;
			$this -> description = $description;
			$this -> coverNumber = $cover;

			$this -> user -> addById($this -> creatorId);
			$this -> creatorMail = $this -> user -> getMail();
		}

		//Get values functions
		public function getName(){
			return $this -> name;
		}

		public function getYear(){
			return $this -> year;
		}

		public function getId(){
			return $this -> albumId;
		}

		//Update fields functions
		public function updateName($name){
			$this -> name = $name;
		}

		public function updateYear($year){
			$this -> year = $year;
		}

		public function updateCover($nr){
			$this -> coverNumber = $nr;
		}

		//Create new album
		public function createAlbum(){
			global $tblName, $albFolder;

			//Generate unique name (based on date and time)
			$this -> name = $this -> creatorId . date('dmYHis');
			$this -> year = date('Y');

			//Create album in db //b_album
			$query = "INSERT INTO ". $tblName['albm'] ." VALUES(NULL, :creatorId, :name, :year, :desc, :cover)";
			$parameters = array('creatorId' => $this -> creatorId, 'name' => $this -> name,
								'year' => $this -> year, 'desc' => $this -> description,
								'cover' => 1);
			$this -> sql -> add($query, $parameters);

			//Get auto incremented albumID
			$this -> albumId = $this -> sql -> getLastID();

			//Set permissions
			$permissionType = 'user';
			$permissionLevel = 5;

			$this -> setPermission($permissionType, $permissionLevel, $this -> creatorId);

			//Create albumfolder
			$this -> setAlbumFolder();
			mkdir($this -> folder , '600', TRUE);

			//Return albumId
			return $this -> albumId;
		}

		private function setPermission($type, $level, $otherId){
			global $tblName;
			$query = "INSERT INTO ". $tblName['acce'] ." VALUES(NULL, :albumId, :type, :otherId, :permission)";
			$parameters = array('albumId' => $this -> albumId, 'type' => $type,
								'otherId' => $otherId, 'permission' => $level);
			return $this -> sql -> add($query, $parameters);
		}

		public function setShareCode(){
			global $tblName;
			$permissionLevel = 1;
			$code = $this -> albumId . randomPassword();
			$query = "INSERT INTO ".$tblName['code']." VALUES(NULL, :code, NULL, NULL)";
			$parameter = array('code' => $code);
			if($this -> sql -> add($query, $parameter) == true){
				$codeId = $this -> sql -> getLastID();

				if($this -> setPermission('code', $permissionLevel, $codeId) == true){
					return $code;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}

		private function setAlbumFolder(){
			global $albFolder;
			$this -> folder = $albFolder . $this -> creatorMail ."/". $this -> year ."-". $this -> name;
		}

		public function moveFolder($oldName, $oldYear){
			global $albFolder;
			$this -> setAlbumFolder();
			rename($albFolder ."/". $this -> creatorMail ."/". $oldYear ."-". $oldName, $this -> folder);
		}


		//Check for albums with that name, year and userId
		private function checkForExisting(){
			global $tblName;

			$query = "SELECT * FROM ". $tblName['albm'] ." WHERE creatorId = :cid AND name = :name AND year = :year";
			$parameters = array('cid' => $this -> creatorId, 'name' => $this -> name, 'year' => $this -> year);
			$exist = $this -> sql -> getNumRows($query, $parameters);

			if($exist == 0){
				return false;
			}else{
				return true;
			}
		}

		//Update DB to be equal to the class-variables
		public function saveToDb(){
			global $tblName;

			if($this -> checkForExisting() == false){
				$query = "UPDATE ". $tblName['albm'] ." SET name = :name, year = :year, description = :desc, cover = :cover WHERE id = :id";
				$parameters = array('name' => $this -> name, 'year' => $this -> year, 'desc' => $this -> description, 'cover' => $this -> coverNumber, 'id' => $this -> albumId);
				$this -> sql -> add($query, $parameters);
				return true;
			}else{
				return false;
			}
		}

		public function setAuth($type = 'user', $id){
			$this -> authType = $type;
			$this -> authId = $id;
		}

		//Add picture
		public function addImage($imgNr, $type){
			global $tk, $tblName;
			$this -> setAlbumFolder();
			$this -> imageNr = $imgNr;
			$this -> getImageType($type);
			$this -> setImageName();
			$this -> setImgPath();

			//Update DB
			$query = "INSERT INTO ". $tblName['pict'] . " VALUES(NULL, :albId, :imgNr, NULL, :path)";
			$parameters = array('albId' => $this -> albumId, 'imgNr' => $this -> imageNr, 'path' => $this -> imageName);
			$this -> sql -> add($query, $parameters);

			return $this -> imagePath;
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

		private function setImageName(){
			$this -> imageName = $this -> imagePrefix . $this -> imageNr . '.' . $this -> imageType;
		}

		private function setImgPath(){
			$this -> imagePath = $this -> folder ."/". $this -> imageName;
		}

		public function getCoverPath(){
			global $tblName;
			$this -> setAlbumFolder();
			$query = "SELECT p.path FROM ".$tblName['albm']." a LEFT JOIN ".$tblName['pict']." p ON a.id = p.aid WHERE a.id = :albId AND a.cover = p.imageNum";
			$parameter = array('albId' => $this -> albumId);
			if($this -> sql -> getNumRows($query, $parameter) > 0){
				$result = $this -> sql -> ask($query, $parameter);
				$this -> imageName = $result[0]['path'];
				$this -> setImgPath();
				return $this -> imagePath;
			}else{
				return false;
			}
		}

		public function getImages(){
			global $tblName;
			$this -> setAlbumFolder();
			$query = "SELECT p.path FROM ".$tblName['albm']." a LEFT JOIN ".$tblName['pict']." p ON a.id = p.aid WHERE a.id = :albId";
			$parameter = array('albId' => $this -> albumId);
			if($this -> sql -> getNumRows($query, $parameter) > 0){
				$result = $this -> sql -> ask($query, $parameter);
				$return = array();
				foreach($result as $row){
					$this -> imageName = $row['path'];
					$this -> setImgPath();
					$return[] = $this -> imagePath;
				}

				return $return;
			}else{
				return false;
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