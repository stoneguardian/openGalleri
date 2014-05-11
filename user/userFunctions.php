<?php

	//Require -----------------------------//
	// - '../config/dbname.php'
	// - '../sql/db.php'
	//-------------------------------------//

	include 'gravatar.php';

	class user {

		//Protected variables
		private $uid;
		private $mail;
		private $name;
		private $type;

		//Construct
		public function addUserByMail($mailAdr){
			global $tk, $users;
			$id = ''; $firstName = ''; $lastName = ''; $type = '';

			if($stmt = $tk -> prepare("SELECT id, fname, lname, type FROM ". $users . " WHERE email = ?")){
				$stmt -> bind_param('s', $mailAdr);
				$stmt -> execute();
				$stmt -> store_result();
				$stmt -> bind_result($id, $firstName, $lastName, $type);
				$stmt -> fetch();
				$decide = $stmt -> num_rows;
				$stmt -> close();
			}else{
				return false;
			}

			if($decide == 1){
				$this -> setVariables($id, $mailAdr, $firstName, $lastName, $type);
				return true;
			}else{
				return false;
			}
		}

		public function addUserById($userId){
			global $tk, $users;
			$mailAdr = ''; $firstName = ''; $lastName = ''; $type = '';

			if($stmt = $tk -> prepare("SELECT email, fname, lname, type FROM ". $users . " WHERE id = ?")){
				$stmt -> bind_param('i', $userId);
				$stmt -> execute();
				$stmt -> store_result();
				$stmt -> bind_result($mailAdr, $firstName, $lastName, $type);
				$stmt -> fetch();
				$decide = $stmt -> num_rows;
				$stmt -> close();
			}else{
				return false;
			}

			if($decide == 1){
				$this -> setVariables($userId, $mailAdr, $firstName, $lastName, $type);
				return true;
			}else{
				return false;
			}
		}

		private function setVariables($uid, $mail, $firstName, $lastName, $type){
			$this -> uid = $uid;
			$this -> mail = $mail;
			$this -> setName($firstName, $lastName);
			$this -> type = $type;
		}

		private function setName($firstName, $lastName){
			$fullName = $firstName . ' ' . $lastName;
			$this -> name = $fullName;
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
			$userImg = genGravatarURL($this -> mail, 80);
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