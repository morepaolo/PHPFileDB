<?php
class PHPFDB_resultset{

	public $db;
	public $data;
	public $cursor = 0;
	public $query;
	public $EOF = false;
	
	public $plan;
	public $from_cache=false;
	public $planning_duration;
	public $execution_duration;
	
	public function __construct($db, $query){
		$this->db = $db;
		$this->query = $query;
	}
	
	/*
	public function MoveNext(){
		if($this->cursor==count($this->rows)){
			$this->EOF = true;
		} else {
			$this->cursor = $this->cursor+1;
		}
	}
	
	public function RecordCount(){
		return(5);
	}
	*/
	
	public function HTMLDump(){
		$html = "<table cellpadding='4' cellspacing='0' style='border-collapse:collapse;margin-bottom:20px;'>";
		if(isset($this->data->cols))
			$colspan=count($this->data->cols)+1;
		else
			$colspan=2;
		$html .= "<tr><td style='background-color:99CCFF;' colspan='$colspan'>".$this->query."</td></tr>";
		if(isset($this->data->cols)){
			$html .= "<tr>";
			$html .= "<th>ADDRESS</th>";
			foreach($this->data->cols as $cur_column){
				$html .= "<th style='border:1px solid black;'>".$cur_column->name."</th>";
			}
			$html .= "</tr>";
		}
		if(isset($this->data->cols)){
			foreach($this->data->rows as $cur_row){
				$html .= "<tr>";
				$html .= "<td style='border:1px solid black;'>".$cur_row->address."</td>";
				for($i=0;$i<count($this->data->cols);$i++)
					$html .= "<td style='border:1px solid black;'>".$this->data->cols[$i]->toString($cur_row->values[$i])."</td>";
				$html .= "</tr>";
			}
			$col_1_span = floor((count($this->data->cols)+1)/2);
			$col_2_span = (count($this->data->cols)+1) - $col_1_span;
		} else {
			$col_1_span = 1;
			$col_2_span = 1;	
		}
		$html .= "<tr>";
		$html .= "<td colspan='".$col_1_span."' style='background-color:#FFBBFF;border-right:1px solid black;font-weight:bold;'>plan: ".(round($this->planning_duration,3)*1000)." msec";
		if($this->from_cache) $html .= " <sub>(cache)</sub>";
		$html .= "</td>";
		$html .= "<td colspan='".$col_2_span."' style='background-color:#FFBBFF;text-align:right;font-weight:bold;'>exec: ".(round($this->execution_duration,3)*1000)." msec</td>";
		$html .= "</tr>";
		$html .= "</table>";
		return($html);		
	}
	
	public function storePlan(){
		$can_store_plan=true;
		$serialized_plan="";
		foreach($this->plan as $instruction){
			$action_code = $instruction->getActionCode();
			if($action_code==0){
				$can_store_plan = false;
				break;
			}
			$serialized_plan.=$instruction->serialize();
		}
		if($can_store_plan){
			$cache_table = new PHPFDB_Relation($this->db, $this->db->tables["cache"], "cache");
			$cache_table->loadMetadata();
			$cache_table->bulkLoad();
			$values_to_add = Array();
			$values_to_add['hash'] = md5($this->query);
			$values_to_add['original_query'] = $this->query;
			$values_to_add['plan'] = $serialized_plan;
			$cache_table->addRow($values_to_add);
		}
	}
	
	public function retrievePlan(){
		$cache_table = new PHPFDB_Relation($this->db, $this->db->tables["cache"], "cache");
		$cache_table->loadMetadata();
		$cache_table->bulkLoad();
		$col_filter = new filter_ColumnReference("hash");
		$val_filter = new filter_StaticString(md5($this->query));
		$eq_filter = new filter_COMP("=", $col_filter, $val_filter);
		$cache_table->filter($eq_filter);
		if(count($cache_table->rows)==0)
			return NULL;
		$serialized_plan = $cache_table->rows[0]->values[2];
		$cached_plan = Array();
		while(strlen($serialized_plan)>0){
			$action_code = substr($serialized_plan, 0, 2);
			$action_code = unpack("n", $action_code);
			$action_code = $action_code[1];
			$instruction_length = substr($serialized_plan, 2, 4);
			$instruction_length = unpack("N", $instruction_length);
			$instruction_length = $instruction_length[1];
			$serialized_insruction = substr($serialized_plan, 6, $instruction_length);
			$serialized_plan=substr($serialized_plan, 6+$instruction_length);
			$instruction = qpAction::byActionCode($action_code);
			$instruction->deserialize($serialized_insruction);
			$cached_plan[] = $instruction;
		}
		return $cached_plan;
	}
}
