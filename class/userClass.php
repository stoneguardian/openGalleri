<?php

	//Require -----------------------------//
	// - '../class/dbClass.php'
	//-------------------------------------//

	function genGravatarURL($mail, $size = '80', $default = 'mm'){
		$md5 = md5( strtolower( trim( $mail ) ) );
		return "http://www.gravatar.com/avatar/$md5?s=$size&d=$default";
	}

	class user {

		//Private variables
		private $uid;
		private $mail;
		private $name;
		private $type;

		//DB
		private $sql;

		//Construct
		public function __construct(db $connection){
			//Create database-object
			$this -> sql = $connection;
		}

		public function addByMail($mail){
			global $tblName;

			//Check if user is in database
			$query = "SELECT id, fname, lname, type FROM ". $tblName['user'] . " WHERE email = :mail";
			$parameter = array('mail' => $mail);
			$exist = $this -> sql -> getNumRows($query, $parameter);

			if($exist == 1){ //If user exist, get information from DB
				$return = $this -> sql -> ask($query, $parameter);
				$this -> setVariables($return[0]['id'],$mail,$return[0]['fname'],$return[0]['lname'],$return[0]['type']);
				return true;
			}else{
				return false;
			}
		}

		public function addById($userId){
			global $tblName;

			//Check if user is in database
			$query = "SELECT email, fname, lname, type FROM ". $tblName['user'] . " WHERE id = :id";
			$parameter = array('id' => $userId);
			$exist = $this -> sql -> getNumRows($query, $parameter);

			if($exist == 1){
				$return = $this -> sql -> ask($query, $parameter);
				$this -> setVariables($userId,$return[0]['email'],$return[0]['fname'],$return[0]['lname'],$return[0]['type']);
				return true;
			}else{
				return false;
			}
		}

		private function setVariables($uid, $mail, $firstName, $lastName, $type){
			$this -> uid = $uid;
			$this -> mail = $mail;
			$this -> name = $firstName . ' ' . $lastName;
			$this -> type = $type;
		}

		//Read-functions
		public function getUid(){
			return $this -> uid;
		}

		public function getMail(){
			return $this -> mail;
		}

		public function getName(){
			return $this -> name;
		}

		public function getType(){
			return $this -> type;
		}

		//Sidebar
		public function getSidebar(){
			$this -> sideUser();
			$this -> sideNav();
		}

		//Sidebar sections
		private function sideUser(){
			//Hent gravatar
			$userImg = genGravatarURL($this -> mail);
			//
			echo '<div id="userAvatarEnc">';
               	echo '<div id="userAvatar" class="userIcon" style="background: url('."'".$userImg."'".');"></div>';
            echo '</div>';

			echo "Hei, " . $this -> name;

			echo '<div class="side-cont no-top">';
				echo '<a href="../index.php?exit=true" class="side-btn">Logg ut</a>';
			echo '</div>';
		}

		private function sideNav(){
			echo '<div class="side-cont">
                	<span class="side-cont-title">Navigasjon</span>

                	<a href="index.php" class="side-btn">Hjem</a>
                	<a href="nyttAlbum.php" class="side-btn">Nytt album</a>
            	  </div>';
		}
	}