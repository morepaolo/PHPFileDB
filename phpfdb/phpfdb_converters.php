<?php
class PHPFDB_converters{
	
	/*
	public static function string2Date($string){
		$temp = explode("-", $string);
		$year = $temp[0];
		$month = $temp[1];
		$day = $temp[2];
		return (new DateTime("$month/$day/$year"));
	}
	*/
	
	//strtotime("2011-01-07")
	
	public static function string2Date($string){
		if(strtotime($string)){
			$temp = new DateTime($string);
			return($temp);
		}
		return NULL;
	}
}
