<?php
	$global_relation_id_counter=0;
	class qpAction {
		public $action_name;
		public $action_codes = Array(
					"LOAD_TABLE" => 1,
					"RETURN_RELATION" => 2
		);
		public function getNewRelationId(){
			global $global_relation_id_counter;
			$global_relation_id_counter+=1;
			return($global_relation_id_counter);
		}
		public function getActionCode($binary = false){
			if(isset($this->action_codes[$this->action_name])){
				if($binary)
					return pack('n', $this->action_codes[$this->action_name]);
				else
					return $this->action_codes[$this->action_name];
			} else
				return 0;
		}
		public static function byActionCode($action_code){
			$temp = new qpAction();
			foreach($temp->action_codes as $action_name => $code){
				if($code==$action_code){
					if($action_name=="LOAD_TABLE")
						return new qpAction_loadTable();
					elseif($action_name=="RETURN_RELATION")
						return new qpAction_returnRelation();
				}
			}			
		}
	}
	
	class qpAction_loadTable extends qpAction{
		public $relation_name;
		public $relation_id;
		public $alias;
		public $data;
		public function __construct($table_name=NULL, $alias=NULL){
			$this->action_name = "LOAD_TABLE";
			$this->relation_id=$this->getNewRelationId();
			$this->alias = $alias;
			$this->relation_name = $table_name;
		}
		public function serialize(){			
			$temp = "";
			$serialized_instruction=$this->getActionCode(true);
			$temp.=pack("N", $this->relation_id);
			$temp.=pack("N", strlen($this->relation_name));
			$temp.=$this->relation_name;
			$temp.=pack("N", strlen($this->alias));
			$temp.=$this->alias;
			$instruction_length=pack("N", strlen($temp));
			$serialized_instruction .= $instruction_length.$temp;
			return($serialized_instruction);
		}
		public function deserialize($serialized_instruction){
			$relation_id = substr($serialized_instruction, 0, 4);
			$relation_id = unpack("N", $relation_id);
			$this->relation_id = $relation_id[1];
			$relation_name_length = substr($serialized_instruction, 4, 4);
			$relation_name_length = unpack("N", $relation_name_length);
			$this->relation_name = substr($serialized_instruction, 8, $relation_name_length[1]);
			$alias_length = substr($serialized_instruction, 8+$relation_name_length[1], 4);
			$alias_length = unpack("N", $alias_length);
			$this->alias = substr($serialized_instruction, 12+$relation_name_length[1], $alias_length[1]);
		}
	}

	class qpAction_returnRelation extends qpAction{
		public $target_relation_id;
		public function __construct($relation_id=NULL){
			$this->action_name = "RETURN_RELATION";
			$this->target_relation_id = $relation_id;
		}
		public function serialize(){
			$temp = "";
			$serialized_instruction=$this->getActionCode(true);
			$temp.=pack("N", $this->target_relation_id);
			$instruction_length=pack("N", strlen($temp));
			$serialized_instruction .= $instruction_length.$temp;
			return($serialized_instruction);
		}
		public function deserialize($serialized_instruction){
			$target_relation_id = substr($serialized_instruction, 0, 4);
			$target_relation_id = unpack("N", $target_relation_id);
			$this->target_relation_id = $target_relation_id[1];
		}
	}

	class qpAction_groupTable extends qpAction{
		public $relation_id;
		public $target_relation_id;
		public $data;
		public $columns;
		public $projection;
		public function __construct($relation_id, $columns, $projection=NULL){
			$this->action_name = "GROUP_TABLE";
			$this->target_relation_id=$relation_id;
			$this->relation_id=$this->getNewRelationId();
			$this->columns=$columns;
			$this->projection=$projection;
		}
	}

	class qpAction_orderRelation extends qpAction{
		public $target_relation_id;
		public $columns;
		public function __construct($relation_id, $columns){
			$this->action_name = "ORDER_RELATION";
			$this->target_relation_id=$relation_id;
			$this->columns=$columns;
		}
	}

	class qpAction_joinRelationsLeft extends qpAction{
		public $in_relation_1;
		public $in_relation_2;
		public $join_condition;
		public $relation_id;
		public $data;
		public function __construct($in_relation_1, $in_relation_2, $join_condition){
			$this->action_name = "LEFT_JOIN";
			$this->relation_id=$this->getNewRelationId();
			$this->in_relation_1 = $in_relation_1;
			$this->in_relation_2 = $in_relation_2;
			$this->join_condition = $join_condition;
		}
	}
	class qpAction_distinctValues extends qpAction{
		public $target_relation_id;
		public function __construct($relation_id){
			$this->action_name = "DISTINCT_VALUES";
			$this->target_relation_id = $relation_id;
		}
	}

	class qpAction_projectColumns extends qpAction{
		public static function mergeColumnsNoDuplicates($columns_set_1, $columns_set_2){
			foreach($columns_set_2 as $column_b){
				$column_found=false;
				foreach($columns_set_1 as $column_a){
					if(get_class($column_a)==get_class($column_b)){
						if($column_a->name==$column_b->name){
							if($column_a->table==$column_b->table){
								if($column_a->alias==$column_b->alias){
									if($column_a->is_set_function&&$column_b->is_set_function)
										$column_found=true;
									elseif(!$column_a->is_set_function&&!$column_b->is_set_function)
										$column_found=true;
								}
							}
						}
					}
				}
				if(!$column_found)
					$columns_set_1[] = $column_b;
			}
			return($columns_set_1);
		}
		
		public $columns;
		public $target_relation_id;
		public function __construct($relation_id, $columns){
			$this->action_name = "PROJECT_COLUMNS";
			$this->target_relation_id = $relation_id;
			$this->columns = $columns;
		}
	}

	class qpAction_selectColumnsByIndexes extends qpAction{
		public $columns;
		public $target_relation_id;
		public function __construct($relation_id, $columns){
			$this->action_name = "SELECT_COLUMNS_BY_ID";
			$this->target_relation_id = $relation_id;
			$this->columns = $columns;
		}
	}

	class qpAction_filterResults extends qpAction{
		public $filter;
		public $target_relation_id;
		public function __construct($relation_id, $filter){
			$this->action_name = "FILTER_RESULTS";
			$this->target_relation_id = $relation_id;
			$this->filter = $filter;
		}
	}

	class qpAction_limitRows extends qpAction{
		public $rows;
		public $offset;
		public function __construct($relation_id, $rows, $offset){
			$this->action_name = "LIMIT_ROWS";
			$this->target_relation_id = $relation_id;
			$this->rows = $rows;
			$this->offset = $offset;
		}
	}

	class qpAction_bulkDelete extends qpAction{
		public $relation_id;
		public function __construct($table_name=NULL){
			$this->action_name = "BULK_DELETE";
			$this->relation_id=$this->getNewRelationId();
			$this->relation_name = $table_name;
		}
	}

	class qpAction_addressedDelete extends qpAction{
		public function __construct($relation_id){
			$this->action_name = "ADDRESSED_DELETE";
			$this->target_relation_id=$relation_id;
		}
	}

	class qpAction_createTable extends qpAction{
		public $relation_id;
		public $columns = Array();
		public function __construct($table_name=NULL, $columns){
			$this->action_name = "CREATE_TABLE";
			$this->relation_id=$this->getNewRelationId();
			$this->relation_name = $table_name;
			$this->columns = $columns;
		}
	}

	class qpAction_dropTable extends qpAction{
		public function __construct($table_name=NULL){
			$this->action_name = "DROP_TABLE";
			$this->relation_name = $table_name;
		}
	}

	class qpAction_insertRow extends qpAction{
		public $values;
		public function __construct($table_name, $values){
			$this->action_name = "INSERT_ROW";
			$this->relation_id=$this->getNewRelationId();
			$this->relation_name = $table_name;
			$this->values = $values;
		}
	}

	class qpAction_updateValues extends qpAction{
		public $new_values_list;
		public function __construct($relation_id, $new_values_list){
			$this->action_name = "UPDATE_ROW";
			$this->target_relation_id=$relation_id;
			$this->new_values_list = $new_values_list;
		}
	}
