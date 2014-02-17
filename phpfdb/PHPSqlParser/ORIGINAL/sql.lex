<?php
include 'jlex.php';

%%

%%

<YYINITIAL>ALL			{ return $this->createToken($this->yytext()); }
<YYINITIAL>AND			{ return $this->createToken($this->yytext()); }
<YYINITIAL>AVG			{ return $this->createToken("AMMSC"); }
<YYINITIAL>MIN			{ return $this->createToken("AMMSC"); }
<YYINITIAL>MAX			{ return $this->createToken("AMMSC"); }
<YYINITIAL>SUM			{ return $this->createToken("AMMSC"); }
<YYINITIAL>COUNT		{ return $this->createToken("AMMSC"); }
<YYINITIAL>ANY			{ return $this->createToken($this->yytext()); }
<YYINITIAL>AS			{ return $this->createToken($this->yytext()); }
<YYINITIAL>ASC			{ return $this->createToken($this->yytext()); }
<YYINITIAL>AUTHORIZATION	{ return $this->createToken($this->yytext()); }
<YYINITIAL>BETWEEN		{ return $this->createToken($this->yytext()); }
<YYINITIAL>BY			{ return $this->createToken($this->yytext()); }
<YYINITIAL>CHAR(ACTER)?		{ return $this->createToken("CHARACTER"); }
<YYINITIAL>CHECK		{ return $this->createToken($this->yytext()); }
<YYINITIAL>CLOSE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>COMMIT		{ return $this->createToken($this->yytext()); }
<YYINITIAL>CONTINUE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>CREATE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>CURRENT		{ return $this->createToken($this->yytext()); }
<YYINITIAL>CURSOR		{ return $this->createToken($this->yytext()); }
<YYINITIAL>DECIMAL		{ return $this->createToken($this->yytext()); }
<YYINITIAL>DECLARE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>DEFAULT		{ return $this->createToken($this->yytext()); }
<YYINITIAL>DELETE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>DESC			{ return $this->createToken($this->yytext()); }
<YYINITIAL>DISTINCT		{ return $this->createToken($this->yytext()); }
<YYINITIAL>DOUBLE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>ESCAPE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>EXISTS		{ return $this->createToken($this->yytext()); }
<YYINITIAL>FETCH		{ return $this->createToken($this->yytext()); }
<YYINITIAL>FLOAT		{ return $this->createToken($this->yytext()); }
<YYINITIAL>FOR			{ return $this->createToken($this->yytext()); }
<YYINITIAL>FOREIGN		{ return $this->createToken($this->yytext()); }
<YYINITIAL>FOUND		{ return $this->createToken($this->yytext()); }
<YYINITIAL>FROM			{ return $this->createToken($this->yytext()); }
<YYINITIAL>GO[ \t]*TO		{ return $this->createToken("GOTO"); }
<YYINITIAL>GRANT		{ return $this->createToken($this->yytext()); }
<YYINITIAL>GROUP		{ return $this->createToken($this->yytext()); }
<YYINITIAL>HAVING		{ return $this->createToken($this->yytext()); }
<YYINITIAL>IN			{ return $this->createToken($this->yytext()); }
<YYINITIAL>INDICATOR		{ return $this->createToken($this->yytext()); }
<YYINITIAL>INSERT		{ return $this->createToken($this->yytext()); }
<YYINITIAL>INT(EGER)?		{ return $this->createToken("INTEGER"); }
<YYINITIAL>INTO			{ return $this->createToken($this->yytext()); }
<YYINITIAL>IS			{ return $this->createToken($this->yytext()); }
<YYINITIAL>KEY			{ return $this->createToken($this->yytext()); }
<YYINITIAL>LANGUAGE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>LIKE			{ return $this->createToken($this->yytext()); }
<YYINITIAL>NOT			{ return $this->createToken($this->yytext()); }
<YYINITIAL>"NULL"		{ return $this->createToken("NULLX"); }
<YYINITIAL>NUMERIC		{ return $this->createToken($this->yytext()); }
<YYINITIAL>OF			{ return $this->createToken($this->yytext()); }
<YYINITIAL>ON			{ return $this->createToken($this->yytext()); }
<YYINITIAL>OPEN			{ return $this->createToken($this->yytext()); }
<YYINITIAL>OPTION		{ return $this->createToken($this->yytext()); }
<YYINITIAL>OR			{ return $this->createToken($this->yytext()); }
<YYINITIAL>ORDER		{ return $this->createToken($this->yytext()); }
<YYINITIAL>PRECISION		{ return $this->createToken($this->yytext()); }
<YYINITIAL>PRIMARY		{ return $this->createToken($this->yytext()); }
<YYINITIAL>PRIVILEGES		{ return $this->createToken($this->yytext()); }
<YYINITIAL>PROCEDURE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>PUBLIC		{ return $this->createToken($this->yytext()); }
<YYINITIAL>REAL			{ return $this->createToken($this->yytext()); }
<YYINITIAL>REFERENCES		{ return $this->createToken($this->yytext()); }
<YYINITIAL>ROLLBACK		{ return $this->createToken($this->yytext()); }
<YYINITIAL>SCHEMA		{ return $this->createToken($this->yytext()); }
<YYINITIAL>SELECT		{ return $this->createToken($this->yytext()); }
<YYINITIAL>SET			{ return $this->createToken($this->yytext()); }
<YYINITIAL>SMALLINT		{ return $this->createToken($this->yytext()); }
<YYINITIAL>SOME			{ return $this->createToken($this->yytext()); }
<YYINITIAL>YYINITIALCODE	{ return $this->createToken($this->yytext()); }
<YYINITIAL>TABLE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>TO			{ return $this->createToken($this->yytext()); }
<YYINITIAL>UNION		{ return $this->createToken($this->yytext()); }
<YYINITIAL>UNIQUE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>UPDATE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>USER			{ return $this->createToken($this->yytext()); }
<YYINITIAL>VALUES		{ return $this->createToken($this->yytext()); }
<YYINITIAL>VIEW			{ return $this->createToken($this->yytext()); }
<YYINITIAL>WHENEVER		{ return $this->createToken($this->yytext()); }
<YYINITIAL>WHERE		{ return $this->createToken($this->yytext()); }
<YYINITIAL>WITH			{ return $this->createToken($this->yytext()); }
<YYINITIAL>WORK			{ return $this->createToken($this->yytext()); }


<YYINITIAL>[-+*/(),.;]		{ return $this->createToken($this->yytext());}
<YYINITIAL>"="|"<>"|"<"|">"|"<="|">="	{ return $this->createToken("COMPARISON");}

<YYINITIAL>[A-Za-z][A-Za-z0-9_]*	{ return $this->createToken("NAME");}

<YYINITIAL>[0-9]+|[0-9]+"."[0-9]*|"."[0-9]*		{ return $this->createToken("INTNUM");}

<YYINITIAL> [ \t\v\n\f] { }
