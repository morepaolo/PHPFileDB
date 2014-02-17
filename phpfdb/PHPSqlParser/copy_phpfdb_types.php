<?php

class PHPFDB_basic_type {
	public $name;
	public $length;
	public $default;
	public $allow_null;
	public $autoinc=0;
	public $is_unique;

	public function __construct($name=NULL, $length=0, $default="NULL", $allow_null=0, $is_unique=0){
		$this->name = $name;
		$this->length = $length;
		if($default=="NULL")
			$this->default = NULL;
		else
			$this->default = $default;
		$this->allow_null = $allow_null;
		$this->is_unique = $is_unique;
	}
	
	public function getLength($value){
		return($this->length);
	}
	
}

class PHPFDB_int extends PHPFDB_basic_type{
	
	public $autoinc;
	
	public function __construct($name=NULL, $default="NULL", $allow_null=0, $autoinc=0, $is_unique=0){
		$this->name = $name;
		$this->length=5;
		if($default=="NULL")
			$this->default = NULL;
		else
			$this->default = $default;
		$this->allow_null = $allow_null;
		$this->autoinc = $autoinc;
		$this->is_unique = $is_unique;
	}

	public function serialize($value){
		if(is_null($value)){
			$isnull=1;
			$serialized_value="";
		} else {
			$isnull=0;
			$serialized_value=pack('i', $value);
		}
		$serialized_isnull=	pack('C', $isnull);
		return($serialized_isnull.$serialized_value);
	}
	
	public function unserialize($data_file_handler){
		$temp = unpack('C', fread($data_file_handler, 1));
		$unserialized_isnull=$temp[1];
		if($unserialized_isnull==1){
			return(NULL);
		}else {
			$byte_string = fread($data_file_handler, $this->length-1);
			$unserialized_value = unpack("i", $byte_string);
			return($unserialized_value[1]);
		}
	}
	
	public function getLength($value){
		if(is_null($value))
			return(1);
		else
			return($this->length);
	}
		
}

class PHPFDB_char extends PHPFDB_basic_type{

	public function serialize($value){		
		if(is_null($value)){
			$isnull=1;
			$serialized_string="";
		} else {
			$isnull=0;
			$serialized_string=substr($value, 0, $this->length);
		}
		$serialized_isnull=	pack('C', $isnull);		
		if(!($isnull)&&strlen($serialized_string)<$this->length)
			$serialized_string = str_pad($serialized_string, $this->length, " ");
		return($serialized_isnull.$serialized_string);
	}
	
	public function unserialize($data_file_handler){
		$temp = unpack('C', fread($data_file_handler, 1));
		$unserialized_isnull=$temp[1];
		if($unserialized_isnull==1){
			return(NULL);
		}else {
			$byte_string = fread($data_file_handler, $this->length);
			return($byte_string);
		}
	}
	
	public function getLength($value){
		if(is_null($value))
			return(1);
		else
			return($this->length+1);
	}
}

class PHPFDB_varchar extends PHPFDB_basic_type{
	
	public function serialize($value){
		if(is_null($value)){
			$isnull=1;
			$serialized_string="";
			$string_length="";
		} else {
			$isnull=0;
			$serialized_string = utf8_decode(substr($value, 0, $this->length));
			$string_length = pack('C', strlen($value));
		}
		$serialized_isnull=	pack('C', $isnull);
		return($serialized_isnull.$string_length.$serialized_string);
	}
	
	public function unserialize($data_file_handler){	
		$temp = unpack('C', fread($data_file_handler, 1));
		$unserialized_isnull=$temp[1];
		if($unserialized_isnull==1){
			return(NULL);
		}else {
			$temp =  unpack('C', fread($data_file_handler, 1));
			$real_string_length=$temp[1];
			$byte_string = fread($data_file_handler, $real_string_length);
			return($byte_string);
		}
	}
	
	public function getLength($value){
		if(is_null($value))
			return(1);
		else
			return(strlen($value)+2);
	}
}
