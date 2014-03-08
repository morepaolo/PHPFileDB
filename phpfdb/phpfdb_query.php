<?php
class PHPFDB_query{
	public $raw_execution_plan;
	public function __construct($sql){
		$P = new ParseParser();
		$S = new Yylex($sql);
		//$P->ParseTrace(fopen("temp/trace", "w"), "");
		
		while ($t = $S->yylex()) {
			$P->Parse(constant('ParseParser::'. $t->type), $t);
		}
		$P->Parse(0);
		/*
		echo "<pre>";
		print_r($P);
		echo "</pre>";
		*/
		$this->raw_execution_plan = $P->yystack[1]->minor->actions;
	}
}
