<?php
class PHPFDB_relation{

	public $filename;
	public $tablename;
	public $alias;
	private $db;
	public $cols = Array();
	public $rows = Array();

	public function __construct($db, $filename, $tablename=NULL){
		$this->db = $db;
		$this->filename = $filename;
		$this->tablename = $tablename;
	}
	
	public function loadMetadata(){
		/*
			$temp[0] = COLUMN NAME
			$temp[1] = COLUMN TYPE
			$temp[2] = COLUMN LENGTH
			$temp[3] = DEFAULT VALUE
			$temp[4] = ALLOW NULL
			$temp[5] = AUTOINC
			$temp[6] = UNIQUE
		*/
		if(file_exists($this->db->db_folder.$this->filename.".inf")){
			$fh = fopen($this->db->db_folder.$this->filename.".inf","r");
			$temp_cols = Array();
			while (($line = fgets($fh)) !== false){
				$temp = explode ("\t", $line);
				switch(trim($temp[1])){
					case "INT":
						$column = new PHPFDB_int(trim($temp[0]), intval(trim($temp[3])), intval(trim($temp[4])), intval(trim($temp[5])), intval(trim($temp[6])));
						$column->table = $this->tablename;
						$temp_cols[] = $column;
						break;
					case "FLOAT":
						$column = new PHPFDB_float(trim($temp[0]), intval(trim($temp[3])), intval(trim($temp[4])), intval(trim($temp[5])), intval(trim($temp[6])));
						$column->table = $this->tablename;
						$temp_cols[] = $column; 
						break;
					case "CHAR":
						$column = new PHPFDB_char(trim($temp[0]), intval(trim($temp[2])), trim($temp[3]), intval(trim($temp[4])), intval(trim($temp[6])));
						$column->table = $this->tablename;
						$temp_cols[] = $column; 
						break;
					case "VARCHAR":
						$column = new PHPFDB_varchar(trim($temp[0]), intval(trim($temp[2])), trim($temp[3]), intval(trim($temp[4])), intval(trim($temp[6])));
						$column->table = $this->tablename;
						$temp_cols[] = $column; 
						break;
					case "BLOB":
						$column = new PHPFDB_blob(trim($temp[0]), intval(trim($temp[2])), trim($temp[3]), intval(trim($temp[4])), intval(trim($temp[6])));
						$column->table = $this->tablename;
						$temp_cols[] = $column; 
						break;
					case "DATE":
						$column = new PHPFDB_date(trim($temp[0]), intval(trim($temp[3])), trim($temp[4]), intval(trim($temp[6])));
						$column->table = $this->tablename;
						$temp_cols[] = $column; 
						break;
					case "TIME":
						$column = new PHPFDB_time(trim($temp[0]), intval(trim($temp[3])), trim($temp[4]), intval(trim($temp[6])));
						$column->table = $this->tablename;
						$temp_cols[] = $column; 
						break;
					case "DATETIME":
						$column = new PHPFDB_datetime(trim($temp[0]), trim($temp[3]), trim($temp[4]), intval(trim($temp[6])));
						$column->table = $this->tablename;
						$temp_cols[] = $column; 
						break;
					case "TIMESTAMP":
						$column = new PHPFDB_timestamp(trim($temp[0]), trim($temp[3]), trim($temp[4]), intval(trim($temp[6])));
						$column->table = $this->tablename;
						$temp_cols[] = $column; 
						break;
				}
			}
			$this->cols = $temp_cols;
			fclose($fh);
		}else
			throw new PHPFDB_InvalidTableName_Exception();
	}
	
	public function bulkLoad(){
		$fh = fopen($this->db->db_folder.$this->filename.".dat","rb");
		$address_counter=-1; // address is 0-BASED
		$row_counter=0;
		$updated_rows_lookup = Array();
		while(true){ 
			$status_byte = fread($fh, 1);
			$address_counter+=1;
			if(feof($fh)){
				break;
			} else {
				$unserialized_status_byte = unpack("C", $status_byte);
				$status = $unserialized_status_byte[1];
				$rowlength_byte = fread($fh, 4);
				$unserialized_rowlength_byte = unpack("N", $rowlength_byte);
				$row_length = $unserialized_rowlength_byte[1];
				// ROW STATUSES:
				// 0: normal row
				// 1: deleted row
				// 2: updated row with row pointer
				// 3: row pointed
				if($status===0){
					$row_counter+=1;
					$row = new PHPFDB_row();
					$row->status = $status;
					$row->address = $address_counter;
					$row->real_address = $address_counter;
					$row->allocated_space = $row_length;
					foreach($this->cols as $cur_column){
						$cur_value=$cur_column->unserialize($fh);
						$row->values[] = $cur_value;
					}
					$this->rows[] = $row;
				} elseif($status===2){
					$row_counter+=1;
					//echo "LA RIGA E' STATA AGGIORNATA E DA QUALCHE PARTE DEVO SALVARE UN PUNTATORE ALLA POSIZIONE IN CUI LA RIGA EFFETTIVA E' SALVATA<BR />";
					//echo "PUNTATORE: ??? $row_length ??? (NON POSSO USARE LO SPAZIO DI row_length...)<br />";
					$real_address_bytes = fread($fh, 4);
					$unserialized_real_address_bytes = unpack("N", $real_address_bytes);
					$real_address = $unserialized_real_address_bytes[1];
					//echo "PUNTATORE: ??? $real_address ???<br />";
					$this->rows[] = new PHPFDB_row();
					$updated_rows_lookup[$real_address] = Array(
																	"row_index" => $row_counter-1,
																	"base_address" => $address_counter
																);
				} elseif($status===3){
					if(isset($updated_rows_lookup[$address_counter])){
						$row_counter+=1;
						$row = new PHPFDB_row();
						$row->status = $status;
						$row->address = $updated_rows_lookup[$address_counter]["base_address"];
						$row->real_address = $address_counter;
						$row->allocated_space = $row_length;
						foreach($this->cols as $cur_column){
							$cur_value=$cur_column->unserialize($fh);
							$row->values[] = $cur_value;
						}
						$this->rows[$updated_rows_lookup[$address_counter]["row_index"]] = $row;
					}
				}
				$address_counter += 4;
				$address_counter+=$row_length;
				fseek($fh, $address_counter+1);
			}
		}
		fclose($fh);
	}
		
	public function bulkDelete(){
		$fh = fopen($this->db->db_folder.$this->filename.".dat","w");
		fclose($fh);
	}
	
	public function addressedDelete(){
		$fh = fopen($this->db->db_folder.$this->filename.".dat","c+b");
		foreach($this->rows as $cur_row){
			fseek($fh, $cur_row->address);
			fwrite($fh, pack("C", 1));
		}
		fclose($fh);
	} 
	
	public function filter($filter){
		$new_rows = Array();
		$filtered_columns = Array();
		$temp = $filter->getFilterColumnReferences();
		foreach($temp as $filtered_column){
			$position=NULL;
			$found_index=$this->getColumnIndex($this->cols, $filtered_column);
			if($found_index>=0){
				$position=$found_index;
			}
			$filtered_columns[] = Array($filtered_column, $position);
		}
		foreach($this->rows as $cur_row){
			$filtered_values = Array();
			foreach($filtered_columns as $column){
				$filtered_values[] = Array($column[0], $cur_row->values[$column[1]]);
			}
			if($filter->check($filtered_values)){
				$new_rows[] = $cur_row;
			}
		}
		$this->rows = $new_rows;
	}
		
	public function filterDistinct(){
		$new_rows = Array();
		foreach($this->rows as $cur_row){
			$row_found=false;
			foreach($new_rows as $new_row){
				if($this->equalRows($cur_row, $new_row))
					$row_found=true;
			}
			if(!$row_found)
				$new_rows[] = $cur_row;
		}
		$this->rows = $new_rows;
	}
	
	public function filterColumns($filtered_columns){
		$new_cols = Array();
		$new_rows = Array();
		$keep_indexes = Array();
		foreach($filtered_columns as $cname){
			if($cname->is_set_function){
				$new_cols[]=new PHPFDB_int($cname->alias, 0, 0, 0, 0);
				$keep_indexes[]=-1;
			/*
			$temp[0] = COLUMN NAME
			$temp[1] = COLUMN TYPE
			$temp[2] = COLUMN LENGTH
			$temp[3] = DEFAULT VALUE
			$temp[4] = ALLOW NULL
			$temp[5] = AUTOINC
			$temp[6] = UNIQUE
						$temp_cols[] = new PHPFDB_int(trim($temp[0]), intval(trim($temp[3])), intval(trim($temp[4])), intval(trim($temp[5])), intval(trim($temp[6])));
						*/
			} elseif($cname->is_math_function){
				if($cname->return_type=="INT")
					$new_cols[]=new PHPFDB_int($cname->alias, 0, 0, 0, 0);
				elseif($cname->return_type=="FLOAT")
					$new_cols[]=new PHPFDB_float($cname->alias, 0, 0, 0, 0);
				elseif($cname->return_type=="TIMESTAMP")
					$new_cols[]=new PHPFDB_timestamp($cname->alias, 0, 0, 0, 0);
				elseif($cname->return_type=="DATE")
					$new_cols[]=new PHPFDB_date($cname->alias, 0, 0, 0, 0);
				$keep_indexes[]=-1;
			} else {
				foreach($this->cols as $key => $cur_col){
					if($cur_col->name==$cname->name){
						$keep_indexes[]=$key;
						// APPLY ALIAS TO COLUMN
						if(isset($cname->alias))
							$cur_col->name = $cname->alias;
						$new_cols[]=$cur_col;					
						break;
					}
				}
			}
		}
		foreach($this->rows as $cur_row){
			$new_row = new PHPFDB_row();
			$new_row->status = $cur_row->status;
			$new_row->address = $cur_row->address;
			foreach($keep_indexes as $key => $kindex){
				if($kindex>=0)
					$new_row->values[] = $cur_row->values[$kindex];
				else {
					if($filtered_columns[$key]->is_math_function)
						$new_row->values[] = $this->evaluateExpression($cur_row, $filtered_columns[$key]);
					else
						$new_row->values[] = 0;
				}
			}
			$new_rows[] = $new_row;
		}
		$this->cols = $new_cols;
		$this->rows = $new_rows;
	}
	
	public function filterColumnsByIndexes($column_indexes){
		$new_cols = Array();
		$new_rows = Array();
		
		foreach($column_indexes as $cur_index){
			$new_cols[]=$this->cols[$cur_index];
		}
		foreach($this->rows as $cur_row){
			$new_row = new PHPFDB_row();
			$new_row->status = $cur_row->status;
			$new_row->address = $cur_row->address;
			foreach($column_indexes as $kindex){
				if($kindex>=0)
					$new_row->values[] = $cur_row->values[$kindex];
				else
					$new_row->values[] = 0;
			}
			$new_rows[] = $new_row;
		}
		$this->cols = $new_cols;
		$this->rows = $new_rows;
	}
	
	private $autoinc_lock;
	private $autoinc_cur_value;
	public $must_update_autoinc=false;
	
	public function lockAutoinc(){
		$this->autoinc_lock = fopen($this->db->db_folder.$this->filename.".seq","c+b");
		flock($this->autoinc_lock, LOCK_EX);
		$bytes_read = fread($this->autoinc_lock, 4);
		if(feof($this->autoinc_lock)){
			$this->autoinc_cur_value=0;
		}else{
			$temp = unpack("i", $bytes_read);
			$this->autoinc_cur_value=$temp[1];
		}
	}
	
	public function getNextValueAutoinc(){
		$this->autoinc_cur_value+=1;
		return($this->autoinc_cur_value);
	}
	
	public function releaseAndUpdateAutoinc(){
		if($this->must_update_autoinc){
			fseek($this->autoinc_lock, 0);
			fwrite($this->autoinc_lock, pack("i", $this->autoinc_cur_value));
		}
		$this->releaseAutoinc();
	}
	
	public function releaseAutoinc(){
		flock($this->autoinc_lock, LOCK_UN);
		fclose($this->autoinc_lock);
	}
	
	public function addRow($arr_values, $overwrite_deleted_rows=false){
		$row = new PHPFDB_row();
		$this->lockAutoinc();
		try {
			$row->merge($this, $arr_values);
			$serialized_row="";
			$serialized_row.=pack("C", 0);
			$buffer = $row->serialize($this);
			if(isset($row->allocated_space))
				$length = $row->allocated_space;
			else {
				if($row->length<4)
					$length = 4;
				else
					$length = $row->length;
			}
			$serialized_row = $serialized_row.pack("N", $length).$buffer;
			$fh = fopen($this->db->db_folder.$this->filename.".dat","a");
			// QUA CI VA IL CODICE PER SCRIVERE IN MODALITA' "overwrite_deleted_rows"
			//echo "Serialized row length: ".strlen($serialized_row)."<br />";
			//echo "CUR BUFFER LENGTH: ".strlen($buffer)."<br />";
			//echo "CUR ROW LENGTH: ".strlen($serialized_row)."<br />";
			fwrite($fh, $serialized_row);
			fclose($fh);
			$this->releaseAndUpdateAutoinc();
		} catch(Exception $e){
			$this->releaseAutoinc();
		}
	}
	
	public function updateRows($new_values){
		foreach($this->rows as $row){
			$new_values_array = Array();
			foreach($new_values as $column_to_update){
				$new_values_array[$column_to_update->column->name]=$column_to_update->new_value->value;
			}
			$row->merge($this, $new_values_array);
			$buffer = $row->serialize($this);
			$buffer = $row->serialize($this);
			if($row->length<4)
				$length = 4;
			else
				$length = $row->length;
			if($length>$row->allocated_space){
				$serialized_row="";
				$serialized_row.=pack("C", 3);
				$serialized_row = $serialized_row.pack("N", $length).$buffer;
				// DEVO CANCELLARE LA RIGA ATTUALE E RICREARLA GESTENDO I PUNTATORI PER NON AVERE SIDE EFFECT DOVUTI ALL'ORDINAMENTO
				$fh = fopen($this->db->db_folder.$this->filename.".dat","c+b");
				fseek($fh, 0, SEEK_END);
				$new_address = ftell($fh);
				fwrite($fh, $serialized_row);
				fseek($fh, $row->address);
				fwrite($fh, pack("C", 2));
				fseek($fh, 4, SEEK_CUR);
				fwrite($fh, pack("N", $new_address));
				fclose($fh);
			} else {
				$serialized_row="";
				$serialized_row.=pack("C", $row->status);
				$serialized_row = $serialized_row.pack("N", $row->allocated_space).$buffer;
				$fh = fopen($this->db->db_folder.$this->filename.".dat","c+b");
				fseek($fh, $row->real_address);
				fwrite($fh, $serialized_row);
				fclose($fh);
			}
		}
	}
	
	public function equalRows($row_1, $row_2){
		for($i=0;$i<count($this->cols);$i++)
			if($row_1->values[$i]!=$row_2->values[$i])
				return(false);
		return(true);
	}
	
	public function getColIndex($col_name){
		for($i=count($this->cols)-1;$i>=0;$i--)
			if($this->cols[$i]->name==$col_name)
				return($i);
		return(-1);
	}
	
	public function sameGroupRows($row_1, $row_2, $grouping_attribute_indexes){
		$same_group = true;
		foreach($grouping_attribute_indexes as $index){
			if($row_1->values[$index]!==$row_2->values[$index]){
				$same_group=false;
				break;
			}				
		}
		return($same_group);
	}
	
	public function join($relation_1, $relation_2, $join_condition){
		$this->cols = array_merge($relation_1->cols, $relation_2->cols);
		$empty_row_2 = Array();
		foreach($relation_2->cols as $cur_row)
			$empty_row_2[] = NULL;
		foreach($relation_1->rows as $cur_row_1){
			$temp = $cur_row_1;
			foreach($relation_2->rows as $cur_row_2){
				if($this->evaluateJoinCondition($join_condition, $relation_1->cols, $temp->values, $relation_2->cols, $cur_row_2->values)){
					$row = new PHPFDB_row();
					$row->values=array_merge($temp->values, $cur_row_2->values);
					$this->rows[] = $row;
				}
			}
		}
	}
	
	public function leftJoin($relation_1, $relation_2, $join_condition){
		$this->cols = array_merge($relation_1->cols, $relation_2->cols);
		$empty_row_2 = Array();
		foreach($relation_2->cols as $cur_row)
			$empty_row_2[] = NULL;
		foreach($relation_1->rows as $cur_row_1){
			$temp = $cur_row_1;
			$added_rows = false;
			foreach($relation_2->rows as $cur_row_2){
				if($this->evaluateJoinCondition($join_condition, $relation_1->cols, $temp->values, $relation_2->cols, $cur_row_2->values)){
					$row = new PHPFDB_row();
					$row->values=array_merge($temp->values, $cur_row_2->values);
					$this->rows[] = $row;
					$added_rows=true;
				}
			}
			if(!($added_rows)){
				$row = new PHPFDB_row();
				$row->values=array_merge($temp->values, $empty_row_2);
				$this->rows[] = $row;
			}
		}
	}
	
	public function evaluateJoinCondition($join_condition, $row_1_cols, $row_1_values, $row_2_cols, $row_2_values){
		$filtered_values = Array();
		$temp = $join_condition->getFilterColumnReferences();
		foreach($temp as $filtered_column){
			$found_index=$this->getColumnIndex($row_1_cols, $filtered_column);
			if($found_index>=0){
				$filtered_values[] = Array($filtered_column, $row_1_values[$found_index]);
			} else {
				$found_index=$this->getColumnIndex($row_2_cols, $filtered_column);
				if($found_index>=0){
					$filtered_values[] = Array($filtered_column, $row_2_values[$found_index]);
				} else {
					if(isset($filtered_column->table))
						throw new PHPFDB_InvalidColumnName_Exception("Exception - Invalid Column name: ".$filtered_column->table.".".$filtered_column->name);
					else
						throw new PHPFDB_InvalidColumnName_Exception("Exception - Invalid Column name: ".$filtered_column->name);
				}
			}
		}
		return($join_condition->check($filtered_values));
	}
	
	public function getColumnIndex($cols, $column){
		foreach($cols as $key => $current_column){
			if($current_column->name==$column->name && ($column->table=="" || ($column->table!="" && $current_column->table==$column->table))){
				return($key);
			}
		}
		return(-1);
	}
	
	/* OLD
	public function evaluateJoinCondition($join_condition, $row_1_cols, $row_1_values, $row_2_cols, $row_2_values){
		$filtered_values = Array();
		$temp = $join_condition->getColumnReferences();
		foreach($temp as $filtered_column){
			foreach($row_1_cols as $key => $cur_col){
				if($cur_col->name==$filtered_column){
					$filtered_values[] = Array($filtered_column, $row_1_values[$key]);
					break;
				}
			}
			foreach($row_2_cols as $key => $cur_col){
				if($cur_col->name==$filtered_column){
					$filtered_values[] = Array($filtered_column, $row_2_values[$key]);
					break;
				}
			}
		}
		return($join_condition->check($filtered_values));
	}
	*/
	
	public function check_uniqueness($col_name, $value){
		$temp_table = new self($this->db, $this->filename);
		$temp_table->loadMetadata();
		$temp_table->bulkLoad();
		foreach($temp_table->cols as $key => $cur_col)
			if($cur_col->name==$col_name)
				$col_index=$key;
		foreach($temp_table->rows as $cur_row)
			if($cur_row->values[$col_index]==$value)
				return(false);
		return(true);
	}
	
	public function evaluateExpression($row, $expression){
		$filtered_columns = Array();
		$temp = $expression->getFilterColumnReferences();
		foreach($temp as $filtered_column){
			$position=NULL;
			$found_index=$this->getColumnIndex($this->cols, $filtered_column);
			if($found_index>=0){
				$position=$found_index;
			}
			$filtered_columns[] = Array($filtered_column, $position);
		}
		$filtered_values = Array();
		foreach($filtered_columns as $column){
			$filtered_values[] = Array($column[0], $row->values[$column[1]]);
		}
		return($expression->check($filtered_values));
	}
	
	public function limitRows($rows, $offset){
		$new_rows = Array();
		for($i=$offset;$i<$offset+$rows;$i++)
			$new_rows[] = $this->rows[$i];
		$this->rows = $new_rows;
	}
	
	public function sort($order_expressions){
		usort($this->rows, $this->createCompareFunction($order_expressions));
	}
	
	public function createCompareFunction($order_expressions){
		$obj=$this;
		return function ($a, $b) use ($obj, $order_expressions) {
			$result = 0;
			foreach($order_expressions as $exp){
				$val_a = $obj->evaluateExpression($a, $exp->expression);
				$val_b = $obj->evaluateExpression($b, $exp->expression);
				if($val_a==$val_b){/* GO ON */ }
				else {
					if($exp->order=="asc")
						$result = ($val_a<$val_b) ? -1 : 1;
					elseif($exp->order=="desc")
						$result = ($val_a>$val_b) ? -1 : 1;
					break;
				}
			}
			return $result;
		};
	}
}
