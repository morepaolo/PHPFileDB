<?php
class PHPFDB_resultset{

	public $db;
	public $data;
	public $cursor = 0;
	public $query;
	public $EOF = false;
	
	public $plan;
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
					$html .= "<td style='border:1px solid black;'>".$cur_row->values[$i]."</td>";
				$html .= "</tr>";
			}
			$col_1_span = floor((count($this->data->cols)+1)/2);
			$col_2_span = (count($this->data->cols)+1) - $col_1_span;
		} else {
			$col_1_span = 1;
			$col_2_span = 1;	
		}
		$html .= "<tr>";
		$html .= "<td colspan='".$col_1_span."' style='background-color:#FFBBFF;border-right:1px solid black;font-weight:bold;'>plan: ".(round($this->planning_duration,3)*1000)." msec</td>";
		$html .= "<td colspan='".$col_2_span."' style='background-color:#FFBBFF;text-align:right;font-weight:bold;'>exec: ".(round($this->execution_duration,3)*1000)." msec</td>";
		$html .= "</tr>";
		$html .= "</table>";
		return($html);		
	}
	
	public function storePlan(){
		$cache_table = new PHPFDB_Relation($this->db, $this->db->tables["cache"], "cache");
		$cache_table->loadMetadata();
		$cache_table->bulkLoad();
		
		$values_to_add = Array();
		$values_to_add['hash'] = md5($this->query);
		$values_to_add['original_query'] = $this->query;
		$values_to_add['plan'] = "OKTEST";
		$cache_table->addRow($values_to_add);
		
	}
	
}