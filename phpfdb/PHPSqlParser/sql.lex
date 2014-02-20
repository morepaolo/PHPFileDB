<?php
include 'jlex.php';

%%

%ignorecase
%full
%%

<YYINITIAL>ALL			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>AND			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>AVG			{ return $this->createToken("TK_AMMSC"); }
<YYINITIAL>MIN			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>MAX			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>SUM			{ return $this->createToken("TK_AMMSC"); }
<YYINITIAL>COUNT		{ return $this->createToken("TK_COUNT"); }
<YYINITIAL>ANY			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>AS			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>ASC			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>AUTHORIZATION	{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>AUTO_INCREMENT	{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>BETWEEN		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>BLOB			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>BY			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>CHAR(ACTER)?		{ return $this->createToken("TK_CHARACTER"); }
<YYINITIAL>CHECK		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>CLOSE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>COMMIT		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>CONTINUE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>CREATE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>CURRENT		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>CURSOR		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>DECIMAL		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>DECLARE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>DEFAULT		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>DELETE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>DESC			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>DISTINCT		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>DOUBLE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>DROP			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>ESCAPE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>EXISTS		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>FETCH		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>FLOAT		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>FOR			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>FOREIGN		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>FOUND		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>FROM			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>GO[ \t]*TO		{ return $this->createToken("TK_GOTO"); }
<YYINITIAL>GRANT		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>GROUP		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>HAVING		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>IN			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>INDICATOR		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>INNER		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>INSERT		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>INT(EGER)?		{ return $this->createToken("TK_INTEGER"); }
<YYINITIAL>INTO			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>IS			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>JOIN			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>KEY			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>LANGUAGE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>LEFT			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>LIKE			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>LIMIT		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>NOT			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>"NULL"		{ return $this->createToken("TK_NULLX"); }
<YYINITIAL>NUMERIC		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>OF			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>OFFSET		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>ON			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>OPEN			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>OPTION		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>OR			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>ORDER		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>OUTER		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>PRECISION		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>PRIMARY		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>PRIVILEGES		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>PROCEDURE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>PUBLIC		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>REAL			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>REFERENCES		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>RIGHT		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>ROLLBACK		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>SCHEMA		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>SELECT		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>SET			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>SMALLINT		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>SOME			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>YYINITIALCODE	{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>TABLE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>TO			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>UNION		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>UNIQUE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>UPDATE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>USER			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>VALUES		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>VARCHAR		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>VIEW			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>WHENEVER		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>WHERE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>WITH			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>WORK			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>DEADTOKEN		{ return $this->createToken("TK_".strtoupper($this->yytext())); }


<YYINITIAL>ABS			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>ACOS			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>ASIN			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>ATAN			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>ATAN2		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>CEIL(ING)?		{ return $this->createToken("TK_CEIL"); }
<YYINITIAL>COS			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>COT			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>CRC32		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>DEGREES		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>EXP			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>FLOOR		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>FORMAT		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>LN			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>LOG			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>LOG2			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>LOG10		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>MOD			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>PI			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>POW(ER)?		{ return $this->createToken("TK_POW"); }
<YYINITIAL>RADIANS		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>ROUND		{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>SIGN			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>SIN			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>SQRT			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>TAN			{ return $this->createToken("TK_".strtoupper($this->yytext())); }
<YYINITIAL>TRUNCATE		{ return $this->createToken("TK_".strtoupper($this->yytext())); }


<YYINITIAL>[*]			{ return $this->createToken("TK_ASTERISK");}
<YYINITIAL>[/]			{ return $this->createToken("TK_SOLIDUS");}
<YYINITIAL>"."			{ return $this->createToken("TK_PERIOD");}
<YYINITIAL>[,]			{ return $this->createToken("TK_COMMA");}
<YYINITIAL>[(]			{ return $this->createToken("TK_LPAR");}
<YYINITIAL>[)]			{ return $this->createToken("TK_RPAR");}
<YYINITIAL>"+"			{ return $this->createToken("TK_PLUS_SIGN");}
<YYINITIAL>"-"			{ return $this->createToken("TK_MINUS_SIGN");}
<YYINITIAL>[;]			{ return $this->createToken("TK_SEMI");}
<YYINITIAL>"="			{ return $this->createToken("TK_OP_EQ");}
<YYINITIAL>"<>"			{ return $this->createToken("TK_OP_GTLT");}
<YYINITIAL>"<"			{ return $this->createToken("TK_OP_LT");}
<YYINITIAL>">"			{ return $this->createToken("TK_OP_GT");}
<YYINITIAL>"<="			{ return $this->createToken("TK_OP_LTEQ");}
<YYINITIAL>">="			{ return $this->createToken("TK_OP_GTEQ");}

<YYINITIAL>[A-Za-z][A-Za-z0-9_]*	{ return $this->createToken("TK_NAME");}

<YYINITIAL>[0-9]+|[0-9]+"."[0-9]*|"."[0-9]*		{ return $this->createToken("TK_INTNUM");}

<YYINITIAL>[0-9]+[eE][+-]?[0-9]+	{ return $this->createToken("TK_APPROXNUM");}
<YYINITIAL>[0-9]+"."[0-9]*[eE][+-]?[0-9]+	{ return $this->createToken("TK_APPROXNUM");}
<YYINITIAL>"."[0-9]*[eE][+-]?[0-9]+	{ return $this->createToken("TK_APPROXNUM");}

<YYINITIAL>\'(\\.|[^\\\'])*\' {return $this->createToken("TK_STRING");}

<YYINITIAL> [ \t\v\n\f] { }
