<?php

class unary_filter{
	public $is_set_function = false;
	public $is_math_function = false;
	
	public function getColumnReferences(){
		return($this->value);
	}
		
	public function getFilterColumnReferences(){
		return Array($this);
	}
}

class binary_filter{
	public $is_set_function = false;
	public $is_math_function = false;
	
	public function getColumnReferences(){
		$column_references = Array();
		$temp = $this->op1->getColumnReferences();
		if(isset($temp)){
			if(is_array($temp)){
				$column_references=array_merge($column_references, $temp);
			} else 
				$column_references[] = $temp;
		}
		$temp = $this->op2->getColumnReferences();
		if(isset($temp)){
			if(is_array($temp)){
				$column_references=array_merge($column_references, $temp);
			} else 
				$column_references[] = $temp;
		}
		return($column_references);
	}

	public function getFilterColumnReferences(){
		$column_references = Array();
		$temp1 = $this->op1->getFilterColumnReferences();
		$temp2 = $this->op2->getFilterColumnReferences();
		return array_merge($temp1, $temp2);
	}
}

class filter_EmptyExpression extends unary_filter{
	
	
	public function __construct(){
	}
	
	public function getFilterColumnReferences(){
		return Array();
	}
	
	public function check(){
		return NULL;
	}
}
class filter_ColumnReference extends unary_filter{
	public $name;
	public $table;
	public $alias;
	
	public function __construct($name, $table=NULL){
		$this->name = $name;
		$this->table = $table;
	}
	
	public function check($filtered_values){
		foreach($filtered_values as $cur_value){
			if($cur_value[0]->name==$this->name && $cur_value[0]->table==$this->table)
				return($cur_value[1]);
		}
	}
}

class filter_SetFunction extends unary_filter{
	public $type;
	public $alias;
	public $expression;
	public $is_set_function = true;

	public function __construct($type, $alias=NULL){
		$this->type=$type;
		$this->alias=$alias;
	}
	public function getFilterColumnReferences(){
		return $this->expression->getFilterColumnReferences();
	}
}

class filter_UnaryMathFunction extends unary_filter{
	public $type;
	public $alias;
	public $expression;
	public $is_math_function = true;
	public $return_type="FLOAT";

	public function __construct($type, $alias=NULL){
		$this->type=$type;
		$this->alias=$alias;
	}
	public function check($filtered_values){
		$value = $this->expression->check($filtered_values);
		if($this->type=="abs")
			return(abs($value));
		elseif($this->type=="acos"){
			if($value>1||$value<-1)
				return NULL;
			return(acos($value));
		} elseif($this->type=="asin"){
			if($value>1||$value<-1)
				return NULL;
			return(asin($value));
		} elseif($this->type=="atan")
			return(atan($value));
		elseif($this->type=="ceil")
			return(ceil($value));
		elseif($this->type=="cos")
			return(cos($value));
		elseif($this->type=="cot")
			return(1/tan($value));
		elseif($this->type=="crc32")
			return(sprintf("%u", crc32($value))); // Because crc32 returns a signed int
		elseif($this->type=="degrees")
			return(rad2deg($value));
		elseif($this->type=="exp")
			return(exp($value));
		elseif($this->type=="floor")
			return(floor($value));
		elseif($this->type=="ln"){
			if($value<=0)
				return NULL;
			return(log($value));
		} elseif($this->type=="minus_sign")
			return(-$value);
		elseif($this->type=="pi")
			return(pi());
		elseif($this->type=="radians")
			return(deg2rad($value));
		elseif($this->type=="round")
			return(round($value));
		elseif($this->type=="sign"){
			if($value>0)
				return 1;
			if($value<0)
				return -1;
			if($value==0)
				return 0;
		}
		elseif($this->type=="sin")
			return(sin($value));
		elseif($this->type=="sqrt")
			return(sqrt($value));
		elseif($this->type=="tan")
			return(tan($value));
		return(0);
	}
	public function getFilterColumnReferences(){
		return $this->expression->getFilterColumnReferences();
	}
}
class filter_BinaryMathFunction extends unary_filter{
	public $type;
	public $alias;
	public $expression1;
	public $expression2;
	public $is_math_function = true;
	public $return_type="FLOAT";

	public function __construct($type, $alias=NULL){
		$this->type=$type;
		$this->alias=$alias;
	}
	public function check($filtered_values){
		$value1 = $this->expression1->check($filtered_values);
		$value2 = $this->expression2->check($filtered_values);
		if($this->type=="atan2")
			return(atan2($value1, $value2));
		elseif($this->type=="format")
			return(number_format($value1, $value2));
		elseif($this->type=="log"){
			if($value2<=0)
				return NULL;
			if($value1<=1)
				return NULL;
			return(log($value2, $value1));
		}
		elseif($this->type=="mod"){
			if($value2==0)
				return NULL;
			return(fmod($value1, $value2));
		}
		elseif($this->type=="pow")
			return(pow($value1, $value2));
		elseif($this->type=="round")
			return(round($value1, $value2));
		elseif($this->type=="truncate"){
			$pow = pow(10,$value2);
			$value1 = $value1*($pow);
			$value1 = intval($value1);			
			$value1 = $value1/($pow);
			return($value1);
		}
		return(0);
	}
	public function getFilterColumnReferences(){
		$column_references = Array();
		$temp1 = $this->expression1->getFilterColumnReferences();
		$temp2 = $this->expression2->getFilterColumnReferences();
		return array_merge($temp1, $temp2);
	}
}

class filter_UnaryDateFunction extends unary_filter{
	public $type;
	public $alias;
	public $expression;
	public $is_math_function = true;
	public $return_type="INT";

	public function __construct($type, $alias=NULL){
		$this->type=$type;
		$this->alias=$alias;
		if($this->type=="now"||$this->type=="utc_timestamp")
			$this->return_type="TIMESTAMP";
		elseif($this->type=="utc_date")
			$this->return_type="DATE";
		//||$this->type=="utc_time"
	}
	public function check($filtered_values){
		$value = $this->expression->check($filtered_values);
		if(!(is_object($value)))
			$value = PHPFDB_converters::string2Date($value);
		if($this->type=="now"){
			$temp = new DateTime();
			$temp->setTimestamp(time());
			return $temp;
		}
		if($this->type=="utc_date"){
			$temp = new DateTime();
			$temp->setTimestamp(time());
			$temp->setTimezone(new DateTimezone("UTC"));
			return $temp;		
		}
		if($this->type=="utc_time"){
			$temp = new DateTime();
			$temp->setTimestamp(time());
			$temp->setTimezone(new DateTimezone("UTC"));
			return $temp;
		}
		if($this->type=="utc_timestamp"){
			$temp = new DateTime();
			$temp->setTimestamp(time());
			$temp->setTimezone(new DateTimezone("UTC"));
			return $temp;
		}
		if(!(isset($value)))
			return NULL;
		if($this->type=="day")
			return intval($value->format("d"));
		if($this->type=="dayofweek")
			return intval($value->format("w"))+1; // ODBC Standard
		if($this->type=="dayofyear")
			return intval($value->format("z"))+1; // Days go from 1 to 366 in MySQL
		if($this->type=="hour")
			return intval($value->format("H"));
		if($this->type=="minute")
			return intval($value->format("i"));
		if($this->type=="month")
			return intval($value->format("m"));
		if($this->type=="second")
			return intval($value->format("s"));
		if($this->type=="weekday")
			return intval($value->format("w"))-1; // -1 because Sunday = 0 for PHP, but Monday = 0 for MySQL
		if($this->type=="weekofyear")
			return intval($value->format("W"));
		if($this->type=="year")
			return intval($value->format("Y"));
		return(0);
	}
	public function getFilterColumnReferences(){
		return $this->expression->getFilterColumnReferences();
	}
}
class filter_StaticIntnum extends unary_filter{
	public $value;
	
	public function __construct($value){
		$this->value = $value;
	}
		
	public function check($filtered_values){
		return($this->value);
	}
	public function getColumnReferences(){
		return(NULL);
	}
	public function getFilterColumnReferences(){
		return Array();
	}
}

class filter_StaticString extends unary_filter{
	public $value;
	
	public function __construct($value){
		$this->value = $value;
	}
		
	public function check($filtered_values){
		return($this->value);
	}
	public function getColumnReferences(){
		return(NULL);
	}
	public function getFilterColumnReferences(){
		return Array();
	}
}

class filter_IsNullColumn extends unary_filter{
	public $value;
	
	public function __construct($value){
		$this->value = $value;
	}
	
	public function check($filtered_values){
		$op_value = $this->value->check($filtered_values);
		return(is_null($op_value));
	}
	
	public function getColumnReferences(){		
		$column_references = Array();
		$temp = $this->value->getColumnReferences();
		if(isset($temp)){
			if(is_array($temp)){
				$column_references=array_merge($column_references, $temp);
			} else 
				$column_references[] = $temp;
		}
		return($column_references);
	}

	public function getFilterColumnReferences(){
		return Array($this->value);
	}
}

class filter_IsNotNullColumn extends unary_filter{
	public $value;
	
	public function __construct($value){
		$this->value = $value;
	}
	
	public function check($filtered_values){
		$op_value = $this->value->check($filtered_values);
		return(!(is_null($op_value)));
	}
	
	public function getColumnReferences(){		
		$column_references = Array();
		$temp = $this->value->getColumnReferences();
		if(isset($temp)){
			if(is_array($temp)){
				$column_references=array_merge($column_references, $temp);
			} else 
				$column_references[] = $temp;
		}
		return($column_references);
	}

	public function getFilterColumnReferences(){
		return Array($this->value);
	}
}

class filter_COMP extends binary_filter{
	public $op1;
	public $op2;
	public $comp_operator;
	
	public function __construct($comp_operator, $in_op1=NULL, $in_op2=NULL){
		$this->comp_operator = $comp_operator;
		$this->op1 = $in_op1;
		$this->op2 = $in_op2;
		//echo get_class($in_op1);
		//echo get_class($in_op2);
	}
	
	private function like ($string, $pattern, $escape= "\\"){
		$original = $pattern;
		$pattern = str_split($pattern);
		$cur_state=0;
		$final_pattern="";
		for($i=0;$i<count($pattern);$i++){
			if($cur_state==0){
				if($pattern[$i]==$escape)
					$cur_state=1;
				elseif($pattern[$i]=="%")
					$final_pattern.=".*";
				elseif($pattern[$i]=="_")
					$final_pattern.=".";
				else
					$final_pattern.=preg_quote($pattern[$i]);
			} elseif($cur_state==1){
				if($pattern[$i]=="%"||$pattern[$i]=="_"){
					$final_pattern.=$pattern[$i];
					$cur_state=0;
				} elseif($pattern[$i]==$escape)
					$final_pattern.=$escape;
				else {
					$final_pattern.=preg_quote($escape.$pattern[$i]);
					$cur_state=0;
				}
			}
		}
		if(preg_match("/".$final_pattern."/i", $string))
			return true;
		return false;
	}
	
	public function check($filtered_values){
		$op1_value = $this->op1->check($filtered_values);
		$op2_value = $this->op2->check($filtered_values);
		
		if((is_object($op1_value)&&get_class($op1_value)=="DateTime")||(is_object($op2_value)&&get_class($op2_value)=="DateTime")){
			if(!(is_object($op1_value)&&get_class($op1_value)=="DateTime"))
				$op1_value = PHPFDB_converters::string2Date($op1_value);
			if(!(is_object($op2_value)&&get_class($op2_value)=="DateTime"))
				$op2_value = PHPFDB_converters::string2Date($op2_value);
			$op1_value = $op1_value->format('YmdHis');
			$op2_value = $op2_value->format('YmdHis');
		}
		if($this->comp_operator=="="){
			return($op1_value==$op2_value);
		}elseif($this->comp_operator==">"){
			return($op1_value>$op2_value);
		}elseif($this->comp_operator=="<"){
			return($op1_value<$op2_value);
		}elseif($this->comp_operator==">="){
			return($op1_value>=$op2_value);
		}elseif($this->comp_operator=="<="){
			return($op1_value<=$op2_value);
		}elseif($this->comp_operator=="<>"){
			return($op1_value<>$op2_value);
		}elseif($this->comp_operator=="LIKE"){
			//$regexp = str_replace("%", ".*", $op2_value);
			//return(preg_match("/".$regexp."/i", $op1_value));
			return $this->like($op1_value, $op2_value);
		}
	}
}

class filter_OR extends binary_filter{
	public $op1;
	public $op2;
	
	public function __construct($in_op1=NULL, $in_op2=NULL){
		$this->op1 = $in_op1;
		$this->op2 = $in_op2;
	}

	public function check($filtered_values){
		$op1_value = $this->op1->check($filtered_values);
		$op2_value = $this->op2->check($filtered_values);
		return($op1_value||$op2_value);
	}
}

class filter_AND extends binary_filter{
	public $op1;
	public $op2;
	
	public function __construct($in_op1=NULL, $in_op2=NULL){
		$this->op1 = $in_op1;
		$this->op2 = $in_op2;
	}

	public function check($filtered_values){
		$op1_value = $this->op1->check($filtered_values);
		$op2_value = $this->op2->check($filtered_values);
		return($op1_value&&$op2_value);
	}
}
