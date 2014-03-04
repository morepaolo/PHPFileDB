<?php

class PHPFDB_InvalidColumnName_Exception extends Exception{
}
class PHPFDB_InvalidTableName_Exception extends Exception{
}
class PHPFDB_DuplicateTableName_Exception extends Exception{
}
class PHPFDB_UniqueConstraintViolated_Exception extends Exception{

}

class PHPFDB_Exception extends Exception {
/*
var $dbms;
var $fn;
var $sql = '';
var $params = '';
var $host = '';
var $database = '';
	*/
	function __construct($dbms, $fn, $errno, $errmsg, $p1, $p2, $thisConnection){
		/*
		switch($fn) {
		case 'EXECUTE':
			$this->sql = $p1;
			$this->params = $p2;
			$s = "$dbms error: [$errno: $errmsg] in $fn(\"$p1\")\n";
			break;
	
		case 'PCONNECT':
		case 'CONNECT':
			$user = $thisConnection->user;
			$s = "$dbms error: [$errno: $errmsg] in $fn($p1, '$user', '****', $p2)\n";
			break;
		default:
			$s = "$dbms error: [$errno: $errmsg] in $fn($p1, $p2)\n";
			break;
		}
	
		$this->dbms = $dbms;
		if ($thisConnection) {
			$this->host = $thisConnection->host;
			$this->database = $thisConnection->database;
		}
		$this->fn = $fn;
		$this->msg = $errmsg;
		*/		
		if (!is_numeric($errno)) $errno = -1;
		parent::__construct($s,$errno);
	}
}
