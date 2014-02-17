<?php
include "sql.lex.php";
include "sql.php";
include "query-planner.php";
include "filter-framework.php";

$S = new Yylex(fopen("test_lexer.sql", "r")); // you can get one of these using the JLexPHP package


while ($t = $S->yylex()) {
	print_r($t);
}

