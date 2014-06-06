<?php

	require '../config/dbConf.php';

	class db {
		//Variables
		private $dbError;
		private $char = "utf8";
		private $connection;
		private $statement;

		public function __construct(){
			global $dbServer, $db, $dbUsername, $dbPassword;

			try {
				$this -> connection = new PDO("mysql:host=$dbServer;dbname=$db;", $dbUsername, $dbPassword);
				$this -> dbError = "Connection established";
			} catch(PDOException $e){
				$this -> dbError = "Connection error, could not connect to database: " . $e -> getMessage();
			}
		}

		public function getError(){
			return $this -> dbError;
		}

		private function resetError(){
			$this -> dbError = false;
		}

		private function prepare($sql){
			try {
				$this -> statement = $this -> connection -> prepare($sql);
			} catch(PDOException $e) {
				$this -> dbError = $e -> getMessage() ;
				return false;
			}
		}

		private function execute($parameters){
			try {
				if($parameters == ''){
					$this -> statement -> execute();
				}else{
					$this -> statement -> execute($parameters);
				}
			} catch(PDOException $e) {
				$this -> dbError = $e -> getMessage() ;
				return false;
			}
		}

		public function getNumRows($sql, $parameters=''){
			$this -> resetError();
			$this -> prepare($sql);
			$this -> execute($parameters);

			try {
				$numRows = $this -> statement -> rowCount();
				$this -> dbError = "Query run";
				return $numRows;
			} catch(PDOException $e) {
				$this -> dbError = $e -> getMessage() ;
				return false;
			}
		}

		public function ask($sql, $parameters=''){
			$this -> resetError();
			$this -> prepare($sql);
			$this -> execute($parameters);

			try {
				$result = $this -> statement -> fetchAll(PDO::FETCH_ASSOC);
				$this -> dbError = "Query Run";
				return $result;
			} catch(PDOException $e) {
				$this -> dbError = $e -> getMessage() ;
				return false;
			}
		}

		public function add($sql, $parameters=''){
			$this -> resetError();
			$this -> prepare($sql);
			$this -> execute($parameters);
			if($this -> dbError == false){
				return true;
			}else{
				return false;
			}
		}

		public function getLastID(){
			return $this -> connection -> lastInsertId();
		}
	}