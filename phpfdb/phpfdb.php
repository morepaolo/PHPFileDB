<?php
class PHPFDB{

	public $db_folder;
	public $tables = Array();
	
	public function __construct($db_folder=NULL){
		//echo "Initializing DB...";
		include ("phpfdb_query.php");
		include ("phpfdb_types.php");
		include ("phpfdb_row.php");
		include ("phpfdb_relation.php");
		include ("phpfdb_resultset.php");
		$this->db_folder = $db_folder;
		$this->tables["cache"] = "cache";
		$this->tables["tables"] = "tables";
		$tables = new PHPFDB_relation($this, "tables", "tables");
		$tables->loadMetadata();
		$tables->bulkLoad();
		foreach($tables->rows as $cur_row){
			$this->tables[$cur_row->values[0]] = $cur_row->values[1];
		}
	}
	
	public function getNewRelationName(){
		$random_name = $this->genRandomString(8);
		return($random_name);
	}
	
	public function addTable($relation_name, $columns){
		$filename = $this->getNewRelationName();
		$temp_table = new PHPFDB_Relation($this, $this->tables["tables"], "tables");
		$temp_table->loadMetadata();
		$values_to_add = Array("name" => $relation_name, "filename" => $filename);
		$temp_table->addRow($values_to_add);
		$fh = fopen($this->db_folder.$filename.".dat","w");
		fclose($fh);
		$fh = fopen($this->db_folder.$filename.".inf","w");
		foreach($columns as $key => $cur_col){
			fwrite($fh, $cur_col->typeToString());
			if($key < count($columns)-1)
				fwrite($fh, "\n");
		}
		$this->tables[$relation_name] = $filename;
		fclose($fh);
	}
	
	public function dropTable($relation_name){
		if(array_key_exists($relation_name, $this->tables)){
			$filename = $this->tables[$relation_name];
			if(file_exists($this->db_folder.$filename.".dat"))
				unlink($this->db_folder.$filename.".dat");
			if(file_exists($this->db_folder.$filename.".inf"))
				unlink($this->db_folder.$filename.".inf");
			if(file_exists($this->db_folder.$filename.".seq"))
				unlink($this->db_folder.$filename.".seq");
			$sql = "delete from tables where name='$relation_name'";
			$result = $this->query($sql);
		}
	}
	
	public function simulate($sql){
		$result = new PHPFDB_resultset($sql);
		$start_planning = microtime(true);
		$parsequery = new PHPFDB_Query($sql);
		$end_planning = microtime(true);
		$end_execution = microtime(true);
		$result->plan = $parsequery->raw_execution_plan;
		$result->planning_duration = $end_planning-$start_planning;
		$result->execution_duration = $end_execution-$end_planning;
		return($result);
	}
	
	public function query($sql){
		$result = new PHPFDB_resultset($this, $sql);
		$start_planning = microtime(true);
		$parsequery = new PHPFDB_Query($sql);
		$end_planning = microtime(true);
		foreach($parsequery->raw_execution_plan as $instruction){
			switch($instruction->action_name){
				case "LOAD_TABLE":
					if(array_key_exists($instruction->relation_name, $this->tables)){
						$instruction->data = new PHPFDB_Relation($this, $this->tables[$instruction->relation_name], $instruction->relation_name);
						$instruction->data->loadMetadata();
						$instruction->data->bulkLoad();
						break;
					} else {
						throw new PHPFDB_InvalidTableName_Exception();
					}
				case "DISTINCT_VALUES":
					foreach($parsequery->raw_execution_plan as $inner_instruction){
						if(isset($inner_instruction->relation_id)&&$inner_instruction->relation_id==$instruction->target_relation_id){
							$inner_instruction->data->filterDistinct();
						}
					}
					break;
				case "PROJECT_COLUMNS":
					foreach($parsequery->raw_execution_plan as $inner_instruction){
						if(isset($inner_instruction->relation_id)&&$inner_instruction->relation_id==$instruction->target_relation_id){
							$inner_instruction->data->filterColumns($instruction->columns);
						}
					}
					break;
				case "SELECT_COLUMNS_BY_ID":
					foreach($parsequery->raw_execution_plan as $inner_instruction){
						if(isset($inner_instruction->relation_id)&&$inner_instruction->relation_id==$instruction->target_relation_id){
							$inner_instruction->data->filterColumnsByIndexes($instruction->columns);
						}
					}
					break;
				case "LIMIT_ROWS":
					foreach($parsequery->raw_execution_plan as $inner_instruction){
						if(isset($inner_instruction->relation_id)&&$inner_instruction->relation_id==$instruction->target_relation_id){
							$inner_instruction->data->limitRows($instruction->rows, $instruction->offset);
						}
					}
					break;
				case "ORDER_RELATION":
					foreach($parsequery->raw_execution_plan as $inner_instruction){
						if(isset($inner_instruction->relation_id)&&$inner_instruction->relation_id==$instruction->target_relation_id){
							$inner_instruction->data->sort($instruction->columns);
						}
					}
					break;
				case "RETURN_RELATION":
					foreach($parsequery->raw_execution_plan as $inner_instruction){
						if(isset($inner_instruction->relation_id)&&$inner_instruction->relation_id==$instruction->target_relation_id){
							$result->data=$inner_instruction->data;
						}
					}
					break;
				case "FILTER_RESULTS":
					foreach($parsequery->raw_execution_plan as $inner_instruction){
						if(isset($inner_instruction->relation_id)&&$inner_instruction->relation_id==$instruction->target_relation_id){
							$inner_instruction->data->filter($instruction->filter);
						}
					}
					break;
				case "CREATE_TABLE":
					$this->addTable($instruction->relation_name, $instruction->columns);
					break;
				case "DROP_TABLE":
					$this->dropTable($instruction->relation_name);
					break;
				case "BULK_DELETE":
					$rel = new PHPFDB_Relation($this, $this->tables[$instruction->relation_name], $instruction->relation_name);
					$rel->bulkDelete();
					break;
				case "ADDRESSED_DELETE":
					foreach($parsequery->raw_execution_plan as $inner_instruction){
						if(isset($inner_instruction->relation_id)&&$inner_instruction->relation_id==$instruction->target_relation_id){
							$inner_instruction->data->addressedDelete();
						}
					}
					break;
				case "UPDATE_ROW":
					foreach($parsequery->raw_execution_plan as $inner_instruction){
						if(isset($inner_instruction->relation_id)&&$inner_instruction->relation_id==$instruction->target_relation_id){
							$inner_instruction->data->updateRows($instruction->new_values_list);
						}
					}
					break;
				case "LEFT_JOIN":
					foreach($parsequery->raw_execution_plan as $inner_instruction){
						if(isset($inner_instruction->relation_id)&&$inner_instruction->relation_id==$instruction->in_relation_1){
							$relation_1 = $inner_instruction->data;
						}
					}
					foreach($parsequery->raw_execution_plan as $inner_instruction){
						if(isset($inner_instruction->relation_id)&&$inner_instruction->relation_id==$instruction->in_relation_2){
							$relation_2 = $inner_instruction->data;
						}
					}
					$temp = new PHPFDB_Relation($this, "", "");
					$temp->leftJoin($relation_1, $relation_2, $instruction->join_condition);
					$instruction->data = $temp;
					break;
				case "INSERT_ROW":
					$temp_table = new PHPFDB_Relation($this, $this->tables[$instruction->relation_name], $instruction->relation_name);
					$temp_table->loadMetadata();
					$values_to_add = Array();
					foreach($instruction->values as $key => $cell){
						$values_to_add[$key] = $cell->value;
					}
					$temp_table->addRow($values_to_add);
					break;
					
				case "GROUP_TABLE":
					foreach($parsequery->raw_execution_plan as $inner_instruction){
						if(isset($inner_instruction->relation_id)&&$inner_instruction->relation_id==$instruction->target_relation_id){
							$instruction->data = new PHPFDB_Relation($this, "", "");
							$instruction->data->cols=$inner_instruction->data->cols;
							$new_rows = Array();
							$grouping_attribute_indexes = Array();
							foreach($instruction->columns as $grouping_column){
								$grouping_attribute_indexes[] = $instruction->data->getColIndex($grouping_column->name);
								//echo $grouping_column->value." - ".$instruction->data->getColIndex($grouping_column->value)."<br />";
							}
							foreach($inner_instruction->data->rows as $cur_row){
								$same_group=false;
								foreach($instruction->data->rows as $row_index => $new_row){
									if($instruction->data->sameGroupRows($new_row, $cur_row, $grouping_attribute_indexes)){
										$same_group=true;
										break;
									}
								}
								if($same_group){ // VALUTO LA SET FUNCTION
									foreach($instruction->projection as $key => $column){
										if($column->is_set_function){
											if($column->type=="count_asterisk"){
												$new_row->values[$key] = $new_row->values[$key]+1;
											} elseif($column->type=="max"){
												$value = $instruction->data->evaluateExpression($cur_row, $column->expression);
												if($new_row->values[$key]<$value)
													$new_row->values[$key] = $value;
											} elseif($column->type=="min"){
												$value = $instruction->data->evaluateExpression($cur_row, $column->expression);
												if($new_row->values[$key]>$value)
													$new_row->values[$key] = $value;
											}
										}
									}
								} else { // AGGIUNGO LA ROW
									foreach($instruction->projection as $key => $column){
										if($column->is_set_function){
											if($column->type=="count_asterisk")
												$cur_row->values[$key]=1;
											elseif($column->type=="max")
												$cur_row->values[$key]=$instruction->data->evaluateExpression($cur_row, $column->expression);
											elseif($column->type=="min")
												$cur_row->values[$key]=$instruction->data->evaluateExpression($cur_row, $column->expression);
										}
									}
									$instruction->data->rows[] = $cur_row;
								}
							}
							break;
						}
					}
			}
		}
		$end_execution = microtime(true);
		$result->plan = $parsequery->raw_execution_plan;
		$result->planning_duration = $end_planning-$start_planning;
		$result->execution_duration = $end_execution-$end_planning;
		return($result);
	}
	
	public function genRandomString($length) {
		$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
		$string = "";    
		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters)-1)];
		}
		return $string;
	}
}
