<?php
class PHPFDB_row{

	public $status;
	public $address;
	public $real_address;
	public $allocated_space;
	public $length;
	public $values = Array();
	
	public function __construct(){
	
	}
	
	public function	merge($relation, $arr_values){
		$must_update_autoinc=false;
		$check_uniqueness=false;
		foreach($relation->cols as $key => $cur_col){
			if(isset($arr_values)&&array_key_exists($cur_col->name, $arr_values)){
				$value_to_write = $arr_values[$cur_col->name];
				$check_uniqueness = true;
			}else {
				if($cur_col->autoinc){
					if(isset($this->address)){
						$value_to_write = $this->values[$key];
					} else {
						$value_to_write = $relation->getNextValueAutoinc();
						$must_update_autoinc=true;
						$check_uniqueness = true;
					}
				} elseif(!(isset($this->address))){ // If address is set, we are updating a row (on INSERT, address is null)
					$value_to_write = $cur_col->getDefaultValue();
					$check_uniqueness = true;
				} else 
					$value_to_write = $this->values[$key];
			}
			if($cur_col->is_unique && $check_uniqueness){ //CHECKING FOR UNIQUENESS
				$uniqueness = $relation->check_uniqueness($cur_col->name, $value_to_write);
				if($uniqueness)
					$this->values[$key] = $value_to_write;
				else
					throw new PHPFDB_UniqueConstraintViolated_Exception();
			} else
				$this->values[$key] = $value_to_write;
		}
		$relation->must_update_autoinc=$must_update_autoinc;
	}
	
	public function serialize($relation){
		$buffer="";
		foreach($relation->cols as $key => $cur_col)
			$buffer.=$cur_col->serialize($this->values[$key]);
		$this->length = strlen($buffer);
		return($buffer);
	}
	
}
