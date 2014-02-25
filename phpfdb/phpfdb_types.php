<?php

class PHPFDB_basic_type {
	public $name=NULL;
	public $table=NULL;
	public $alias=NULL;
	public $length=0;
	public $default;
	public $allow_null;
	public $autoinc=0;
	public $is_unique;
	public $string_type;

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
	
	public function typeToString(){
		$string = $this->name."\t";
		$string .= $this->string_type."\t";
		$string .= $this->length."\t";
		if(is_null($this->default))
			$string .= "NULL"."\t";
		else
			$string .= $this->default."\t";
		$string .= $this->allow_null."\t";
		$string .= $this->autoinc."\t";
		$string .= $this->is_unique;
		return($string);
	}
	
	public function toString($value){
		return($value);
	}
}

class PHPFDB_int extends PHPFDB_basic_type{
	
	public $autoinc;
	public $string_type = "INT";
	
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

class PHPFDB_float extends PHPFDB_basic_type{
	
	public $string_type = "FLOAT";
	
	public function __construct($name=NULL, $default="NULL", $allow_null=0, $autoinc=0, $is_unique=0){
		$this->name = $name;
		$this->length=9;
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
			$serialized_value=pack('d', $value);
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
			$unserialized_value = unpack("d", $byte_string);
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

	public $string_type = "CHAR";
	
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
			return(rtrim($byte_string));
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
	
	public $string_type = "VARCHAR";
	
	public function serialize($value){
		if(is_null($value)){
			$isnull=1;
			$serialized_string="";
			$string_length="";
		} else {
			$isnull=0;
			$serialized_string = utf8_decode(substr($value, 0, $this->length));
			$string_length = pack('C', strlen($serialized_string));
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

class PHPFDB_blob extends PHPFDB_basic_type{
	
	public $string_type = "BLOB";
	
	public function serialize($value){
		if(is_null($value)){
			$isnull=1;
			$serialized_string="";
			$string_length="";
		} else {
			$isnull=0;
			$serialized_string = $value;
			$string_length = pack('C', strlen($serialized_string));
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

class PHPFDB_date extends PHPFDB_basic_type{
	
	public $string_type = "DATE";
	
	public function __construct($name=NULL, $default="NULL", $allow_null=0, $is_unique=0){
		$this->name = $name;
		$this->length=5;
		if($default=="NULL")
			$this->default = NULL;
		else
			$this->default = $default;
		$this->allow_null = $allow_null;
		$this->autoinc = 0;
		$this->is_unique = $is_unique;
	}
	
	public function serialize($value){
		if(is_null($value)){
			$isnull=1;
			$serialized_value="";
		} else {
			$isnull=0;
			$temp = PHPFDB_converters::string2Date($value);
			$year = intval($temp->format('Y'));
			$month = intval($temp->format('m'));
			$day = intval($temp->format('d'));
			$value = $year*16*32+$month*32+$day;
			$serialized_value = pack('N', $value);
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
			$unserialized_value =  unpack('N', fread($data_file_handler, 4));
			$unserialized_value = intval($unserialized_value[1]);
			$year = intval($unserialized_value/(16*32));
			$unserialized_value = $unserialized_value-($year*16*32);
			$month = intval($unserialized_value/32);
			$unserialized_value = $unserialized_value-($month*32);
			$day = intval($unserialized_value);
			return(new DateTime("$month/$day/$year"));
		}
	}
	
	public function toString($value){
		if(isset($value))
			return($value->format('Y-m-d'));
		return NULL;
	}
}

class PHPFDB_datetime extends PHPFDB_basic_type{
	
	public $string_type = "DATETIME";
	
	public function __construct($name=NULL, $default="NULL", $allow_null=0, $is_unique=0){
		$this->name = $name;
		$this->length=9;
		if($default=="NULL")
			$this->default = NULL;
		else
			$this->default = $default;
		$this->allow_null = $allow_null;
		$this->autoinc = 0;
		$this->is_unique = $is_unique;
	}
	
	public function serialize($value){
		if(is_null($value)){
			$isnull=1;
			$serialized_value="";
		} else {
			$isnull=0;
			$temp = PHPFDB_converters::string2Date($value);
			$year = intval($temp->format('Y'));
			$month = intval($temp->format('m'));
			$day = intval($temp->format('d'));
			$hour = intval($temp->format('H'));
			$minutes = intval($temp->format('i'));
			$seconds = intval($temp->format('s'));
			$first_word = $year*10000+$month*100+$day;
			$second_word = $hour*10000+$minutes*100+$seconds;
			$serialized_value = pack('N', $first_word).pack('N', $second_word);
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
			$unserialized_first_word =  unpack('N', fread($data_file_handler, 4));
			$unserialized_first_word = intval($unserialized_first_word[1]);
			$year = intval($unserialized_first_word/(10000));
			$unserialized_first_word = $unserialized_first_word-($year*10000);
			$month = intval($unserialized_first_word/100);
			$unserialized_first_word = $unserialized_first_word-($month*100);
			$day = intval($unserialized_first_word);
			
			$unserialized_second_word =  unpack('N', fread($data_file_handler, 4));
			$unserialized_second_word = intval($unserialized_second_word[1]);
			$hour = intval($unserialized_second_word/(10000));
			$unserialized_second_word = $unserialized_second_word-($hour*10000);
			$minutes = intval($unserialized_second_word/100);
			$unserialized_second_word = $unserialized_second_word-($minutes*100);
			$seconds = intval($unserialized_second_word);
			return(new DateTime("$month/$day/$year $hour:$minutes:$seconds"));
		}
	}
	
	public function toString($value){
		if(isset($value))
			return($value->format('Y-m-d H:i:s'));
		return NULL;
	}
}
