<?php
class PHPFDB_query{
	public $raw_execution_plan;
	public function __construct($sql){
		$P = new ParseParser();
		$fh = fopen("temp/test_query.sql", "w");
		fwrite($fh, $sql);
		fclose($fh);
		$S = new Yylex(fopen("temp/test_query.sql", "r")); // you can get one of these using the JLexPHP package
		$P->ParseTrace(fopen("temp/trace", "w"), "");
		
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