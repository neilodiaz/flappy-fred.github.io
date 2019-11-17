<?php
	
	class dB {
	
		// initialize variables
		private $db_host = "localhost";
		private $db_user = "root";
		private $db_pass = "!!ITseo2011";
		private $db_name = "flappy-fred";
		protected static $con;
		
		public function __construct() {
			try {
				self::$con = new PDO('mysql:host='.$this->db_host.';dbname='.$this->db_name,$this->db_user,$this->db_pass);
				self::$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			} catch(PDOException $e) {
				return 'ERROR: '.$e->getMessage();
			}
		}
		
		public function dbName() {
			return $this->db_name;
		}
		
	}
	
	class query extends dB {

		// initialize variable
		private $sql_query;
		private $sql_error;
		
		public function __construct($query,$array=array()) {
			try {
				if (empty(parent::$con)) { parent::__construct(); }
				$this->sql_query = parent::$con->prepare($query);
				$this->sql_query->execute((array)$array);
				return true;
			} catch(PDOException $e) {
				$this->setErrorCode($e->getCode());
			}
		}
		
		public function fetch( $style = 'PDO::FETCH_BOTH' ) {
			switch ( $style ) {
				case 'PDO::FETCH_BOTH':
					return $this->sql_query->fetch(PDO::FETCH_BOTH);
					break;
				case 'PDO::FETCH_ASSOC':
					return $this->sql_query->fetch(PDO::FETCH_ASSOC);
					break;
			}
		}
		
		public function getFirstField(){
			$result = $this->sql_query->fetch();
			return $result[0];
		}
		
		public function countRows(){
			return $this->sql_query->rowCount();
		}
		
		public function affectedRows(){
			return $this->sql_query->rowCount();
		}
		
		public function getLastInsertedID(){
			return parent::$con->lastInsertId();
		}
		
		public function setErrorCode($err) {
			$this->sql_error = $err;
		}
		
		public function getErrorCode() {
			return $this->sql_error;
		}
		
		public function __desctruct() {
			parent::$con = null;
		}
		
	}

	$db = new dB();
	$db_name = $db->dbName();
	
	if ( isset($_POST['playername']) && !empty($_POST['playername']) ) {
		$data = array(
			'player' => $_POST['playername'],
			'score' => $_POST['score']
		);
		
		$check = new query("SELECT * from scores WHERE player_name = :player", array('player'=>$_POST['playername']));
		
		if ( $check->countRows() > 0 ) {
			$update = new query("UPDATE scores SET player_score= :score WHERE player_name=:player", $data);
		} else {
			$save = new query("INSERT INTO scores (player_name,player_score) values (:player,:score)", $data);
		}
	}