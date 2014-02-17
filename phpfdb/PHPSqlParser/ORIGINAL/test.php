<?php
include "sql.lex.php";
include "sql-92.php";



$P = new ParseParser();
$S = new Yylex(fopen("query.sql", "r")); // you can get one of these using the JLexPHP package
$P->ParseTrace(fopen("trace", "w"), "");
/*
while ($t = $S->nextToken()) {
	print_r($t);
}
*/
while ($t = $S->yylex()) {
	print_r($t);
	$P->Parse(constant('ParseParser::'. $t->type), $t);
}
$P->Parse(0);
print_r($P);
