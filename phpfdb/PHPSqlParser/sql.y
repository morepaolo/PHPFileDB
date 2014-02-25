%token_prefix TK_
%parse_accept {
      //echo "PARSING COMPLETE!!";
   }

statement(A) ::= query_list(B). {A=B;}

query_list(A) ::= query(B) SEMI query_list(C). {A=B;A->actions=array_merge(A->actions, C->actions);}
query_list(A) ::= query(B) optional_semi.  {A=B;}
optional_semi ::= .
optional_semi ::= SEMI.

/* QUESTO L'HO AGGIUNTO IO, TESTARE... */
query(A) ::= query_specification(B). {
		B->actions[] = new qpAction_returnRelation(B->last_relation_id);		
		A=B;
	}
query(A) ::= sql_data_statement(B). {A=B;}
query(A) ::= sql_schema_statement(B). {A=B;}


/* Basic Definitions of Characters Used, Tokens, Symbols, Etc. */

regular_identifier(A) ::= NAME(B). {A=B;}

unsigned_numeric_literal(A) ::= INTNUM(B). {A = new filter_StaticIntnum(B->value);}
unsigned_numeric_literal(A) ::= APPROXNUM(B). {A=B;}

/* Literal Numbers, Strings, Dates and Times */

schema_name ::= unqualified_schema_name.

identifier(A) ::= actual_identifier(B). {A=B;}

actual_identifier(A) ::= regular_identifier(B). {A=B;}

/*unqualified_schema_name ::= identifier.*/
unqualified_schema_name ::= DEADTOKEN.

/**************/
/* SQL Module */
/**************/

column_name(A) ::= identifier(B). {A=B;}
qualified_identifier(A) ::= identifier(B). {A=B;}

table_element_list(A) ::= table_element(B). {
		A = Array();
		A[] = B;
	}
table_element_list(A) ::= table_element_list(B) COMMA table_element(C).{
		B[] = C;
		A=B;
	}

table_element(A) ::= column_definition(B). {A=B;}
table_element ::= table_constraint_definition.

table_constraint_definition ::= DEADTOKEN.

column_definition(A) ::= column_name(B) data_type(C) default_clause column_constraints(E). {
		C->name = B->value;
		if(isset(E)){
			foreach(E as $constraint){
				if(strtoupper($constraint)=="AUTO_INCREMENT")
					C->autoinc=1;
					C->is_unique=1;
			}
		}
		A=C;
	}

column_constraints ::= .
column_constraints(A) ::= column_constraint_definition(B). {A = Array(); A[] = B;}
column_constraints(A) ::= column_constraints(B) column_constraint_definition(C). {B[]=C; A=B;}

/*
<column definition> ::=
		<column name> { <data type> | <domain name> } [ <default clause> ] [ <column constraint definition>... ] [ <collate clause> ]
*/

/**************/
/* Data Types */
/**************/

character_set_specification ::= DEADTOKEN.

data_type(A) ::= character_string_type(B). {A=B;}
data_type(A) ::= character_string_type CHARACTER SET character_set_specification(B). {A=B;}
data_type(A) ::= national_character_string_type(B). {A=B;}
data_type(A) ::= binary_large_object_type(B). {A=B;}
data_type(A) ::= bit_string_type(B). {A=B;}
data_type(A) ::= numeric_type(B). {A=B;}
data_type(A) ::= datetime_type(B). {A=B;}
data_type(A) ::= interval_type(B). {A=B;}

character_string_type ::= CHARACTER.
character_string_type ::= CHAR.
character_string_type(A) ::= CHARACTER LPAR length(B) RPAR. {A = new PHPFDB_char(NULL, B->value);}
character_string_type ::= CHARACTER VARYING.
character_string_type ::= CHARACTER VARYING LPAR length RPAR.
character_string_type ::= CHAR VARYING.
character_string_type ::= CHAR VARYING LPAR length RPAR.
character_string_type(A) ::= VARCHAR. {A = new PHPFDB_varchar(NULL, 666);}
character_string_type(A) ::= VARCHAR LPAR length(B) RPAR. {A = new PHPFDB_varchar(NULL, B->value);}

length(A) ::= INTNUM(B).{A=B;}

national_character_string_type ::= NATIONAL CHARACTER.
national_character_string_type ::= NATIONAL CHARACTER LPAR length RPAR.
national_character_string_type ::= NATIONAL CHAR.
national_character_string_type ::= NATIONAL CHAR LPAR length RPAR.
national_character_string_type ::= NCHAR.
national_character_string_type ::= NCHAR LPAR length RPAR.
national_character_string_type ::= NATIONAL CHARACTER VARYING.
national_character_string_type ::= NATIONAL CHARACTER VARYING LPAR length RPAR.
national_character_string_type ::= NATIONAL CHAR VARYING.
national_character_string_type ::= NATIONAL CHAR VARYING LPAR length RPAR.
national_character_string_type ::= NCHAR VARYING.
national_character_string_type ::= NCHAR VARYING LPAR length RPAR.

binary_large_object_type(A) ::= BLOB. {A = new PHPFDB_blob(NULL);}

bit_string_type ::= BIT.
bit_string_type ::= BIT LPAR length RPAR.
bit_string_type ::= BIT VARYING.
bit_string_type ::= BIT VARYING LPAR length RPAR.

numeric_type(A) ::= exact_numeric_type(B). {A=B;}
numeric_type(A) ::= approximate_numeric_type(B). {A=B;}

exact_numeric_type ::= NUMERIC.
exact_numeric_type ::= NUMERIC LPAR precision RPAR.
exact_numeric_type ::= NUMERIC LPAR precision COMMA scale RPAR.
exact_numeric_type ::= DECIMAL.
exact_numeric_type ::= DECIMAL LPAR precision RPAR.
exact_numeric_type ::= DECIMAL LPAR precision COMMA scale RPAR.
exact_numeric_type ::= DEC.
exact_numeric_type ::= DEC LPAR precision RPAR.
exact_numeric_type ::= DEC LPAR precision COMMA scale RPAR.
exact_numeric_type(A) ::= INTEGER. {A = new PHPFDB_int();}
exact_numeric_type ::= SMALLINT.

precision ::= INTNUM.

scale ::= INTNUM.

approximate_numeric_type(A) ::= FLOAT. {A = new PHPFDB_float();}
approximate_numeric_type ::= FLOAT LPAR precision RPAR.
approximate_numeric_type ::= REAL.
approximate_numeric_type ::= DOUBLE PRECISION.

datetime_type(A) ::= DATE. {A = new PHPFDB_date();}
datetime_type(A) ::= DATETIME. {A = new PHPFDB_datetime();}
datetime_type ::= TIME with_time_zone.
datetime_type ::= TIME LPAR time_precision RPAR with_time_zone.
datetime_type ::= TIMESTAMP with_time_zone.
datetime_type ::= TIMESTAMP LPAR timestamp_precision RPAR with_time_zone.

with_time_zone ::= .
with_time_zone ::= WITH TIME ZONE.

time_precision ::= time_fractional_seconds_precision.

time_fractional_seconds_precision ::= INTNUM.

timestamp_precision ::= time_fractional_seconds_precision.

interval_type ::= INTERVAL interval_qualifier.

interval_qualifier ::= start_field TO end_field.
interval_qualifier ::= single_datetime_field.

start_field ::= non_second_datetime_field.
start_field ::= non_second_datetime_field LPAR interval_leading_field_precision RPAR.

non_second_datetime_field ::= YEAR.
non_second_datetime_field ::= MONTH.
non_second_datetime_field ::= DAY.
non_second_datetime_field ::= HOUR.
non_second_datetime_field ::= MINUTE.

interval_leading_field_precision ::= INTNUM.

end_field ::= non_second_datetime_field.
end_field ::= SECOND.
end_field ::= SECOND LPAR interval_fractional_seconds_precision RPAR.

interval_fractional_seconds_precision ::= INTNUM.

single_datetime_field ::= non_second_datetime_field.
single_datetime_field ::= non_second_datetime_field LPAR interval_leading_field_precision RPAR.
single_datetime_field ::= SECOND.
single_datetime_field ::= SECOND LPAR interval_leading_field_precision RPAR.
single_datetime_field ::= SECOND LPAR interval_leading_field_precision COMMA LPAR interval_fractional_seconds_precision RPAR RPAR.

/*domain_name ::= qualified_name.*/

qualified_name(A) ::= qualified_identifier(B). {A=B;}
qualified_name ::= schema_name PERIOD qualified_identifier.

default_clause ::= .
default_clause ::= DEFAULT default_option.

default_option ::= literal.
/*default_option ::= <datetime value function>*/
default_option ::= USER.
default_option ::= CURRENT_USER.
default_option ::= SESSION_USER.
default_option ::= SYSTEM_USER.
default_option ::= NULL.



/* Literals */

literal ::= signed_numeric_literal.
literal ::= general_literal.

signed_numeric_literal ::= unsigned_numeric_literal.
signed_numeric_literal ::= sign unsigned_numeric_literal.

sign(A) ::= PLUS_SIGN. {A="plus_sign";}
sign(A) ::= MINUS_SIGN. {A="minus_sign";}

/***************/
/* Constraints */
/***************/

column_constraint_definition(A) ::= constraint_name_definition column_constraint(C) constraint_attributes. {A=C;}

constraint_name_definition ::= .
constraint_name_definition ::= CONSTRAINT constraint_name.

constraint_name ::= qualified_name.

column_constraint ::= NOT NULL.
column_constraint(A) ::= unique_specification(B). {A=B;}
column_constraint ::= references_specification.
column_constraint ::= check_constraint_definition.

unique_specification ::= UNIQUE.
unique_specification(A) ::= AUTO_INCREMENT(B). {A=B->value;}
unique_specification ::= PRIMARY KEY.

references_specification ::= REFERENCES referenced_table_and_columns reference_match_type referential_triggered_action.

referenced_table_and_columns ::= table_name.
referenced_table_and_columns ::= table_name LPAR reference_column_list RPAR.

table_name(A) ::= qualified_name(B). {A=B;}

reference_column_list ::= column_name_list.

column_name_list(A) ::= column_name(B). {
		A = Array();
		A[] = B->value;
	}
column_name_list(A) ::= column_name_list(B) COMMA column_name(C). {
		B[] = C->value;
		A=B;
	}

reference_match_type ::= .
reference_match_type ::= MATCH match_type.

match_type ::= FULL.
match_type ::= PARTIAL.

referential_triggered_action ::= .
referential_triggered_action ::= update_rule.
referential_triggered_action ::= update_rule delete_rule.
referential_triggered_action ::= delete_rule.
referential_triggered_action ::= delete_rule update_rule.

update_rule ::= ON UPDATE referential_action.
delete_rule ::= ON DELETE referential_action.

referential_action ::= CASCADE.
referential_action ::= SET NULL.
referential_action ::= SET DEFAULT.
referential_action ::= NO ACTION.

/* Questa va lasciata vuota, MySql parsa correttamente CHECK ma poi lo ignora */
check_constraint_definition ::= CHECK LPAR search_condition RPAR.


/********************/
/* Search Condition */
/********************/

search_condition(A) ::= boolean_term(B). {A=B;}
search_condition(A) ::= search_condition(B) OR boolean_term(C). {
		A= new filter_OR(B, C);
	}

boolean_term(A) ::= boolean_factor(B). {A=B;}
boolean_term(A) ::= boolean_term(B) AND boolean_factor(C). {
		A= new filter_AND(B, C);
	}

boolean_factor(A) ::= boolean_test(B). {A=B;}
boolean_factor ::= NOT boolean_test.

boolean_test(A) ::= boolean_primary(B). {A=B;}
boolean_test ::= boolean_primary IS truth_value.
boolean_test ::= boolean_primary IS NOT truth_value.

boolean_primary(A) ::= predicate(B). {A=B;}
boolean_primary ::= LPAR search_condition RPAR.

predicate(A) ::= comparison_predicate(B). {A=B;}	
predicate(A) ::= null_predicate(B). {A=B;}

comparison_predicate(A) ::= row_value_constructor(B) comp_op(C) row_value_constructor(D). {
		A= new filter_COMP(C->value, B, D);
	}

row_value_constructor(A) ::= row_value_constructor_element(B). {A=B;}

row_value_constructor(A) ::= LPAR row_value_constructor_list(B) RPAR. {A=B;}
row_value_constructor ::= row_subquery.

row_value_constructor_element(A) ::= value_expression(B). {A=B;}
/*
row_value_constructor_element ::= null_specification.
row_value_constructor_element ::= default_specification.
*/
value_expression(A) ::= numeric_value_expression(B). {A=B;}
/*value_expression ::= string_value_expression.*/

numeric_value_expression(A) ::= term(B). {A=B;}
numeric_value_expression(A) ::= math_numeric_value_expression(B). {A=B;} /* DEFINED IN MATH OPERATIONS */
numeric_value_expression(A) ::= date_value_expression(B). {A=B;} /* DEFINED IN DATE OPERATIONS */
numeric_value_expression(B) ::= numeric_value_expression(B) sign(C) term(D). { ECHO "TODOTODOTODOTODO";}

term(A) ::= factor(B). {A=B;}
term ::= term ASTERISK factor.
term ::= term SOLIDUS factor.

factor(A) ::= numeric_primary(B). {A=B;}
factor(A) ::= sign(B) numeric_primary(C).{A= new filter_UnaryMathFunction(B); A->expression=C;}

numeric_primary(A) ::= value_expression_primary(B). {A=B;}
/*numeric_primary ::= numeric_value_function.*/

value_expression_primary(A) ::= unsigned_value_specification(B). {A=B;}
value_expression_primary(A) ::= column_reference(B). {A=B;}
value_expression_primary ::= scalar_subquery.
value_expression_primary(A) ::= set_function_specification(B). {A = B;}

/*
	|   <case expression>
	|   <left paren> <value expression> <right paren>
	|   <cast specification>
*/

unsigned_value_specification(A) ::= unsigned_literal(B). {A=B;}
/*unsigned_value_specification> ::= general_value_specification.*/

unsigned_literal(A) ::= unsigned_numeric_literal(B). {A=B;}
unsigned_literal(A) ::= general_literal(B). {A=B;}


general_literal(A) ::= STRING(B). {
	A = new filter_StaticString(substr(B->value, 1, strlen(B->value)-2));
}




/*
unsigned_literal ::= general_literal.
*/
/*
<general value specification> ::=
		<parameter specification>
	|   <dynamic parameter specification>
	|   <variable specification>
	|   USER
	|   CURRENT_USER
	|   SESSION_USER
	|   SYSTEM_USER
	|   VALUE
*/

/*
<parameter specification> ::= <parameter name> [ <indicator parameter> ]

<parameter name> ::= <colon> <identifier>

<indicator parameter> ::= [ INDICATOR ] <parameter name>

<dynamic parameter specification> ::= <question mark>
*/

column_reference(A) ::= column_name(B). {A = new filter_ColumnReference(B->value);}
column_reference(A) ::= qualifier(B) PERIOD column_name(C). {A = new filter_ColumnReference(C->value, B->value);}

set_function_specification(A) ::= COUNT LPAR ASTERISK RPAR. {A = new filter_SetFunction("count_asterisk"); A->expression = new filter_EmptyExpression();}
set_function_specification(A) ::= general_set_function(B). {A=B;}

general_set_function(A) ::= set_function_type(B) LPAR set_quantifier value_expression(D) RPAR. {B->expression=D;A=B;}

set_function_type(A) ::= MAX. {A = new filter_SetFunction("max");}
set_function_type(A) ::= MIN. {A = new filter_SetFunction("min");}
/*
<set function type> ::= AVG | MAX | MIN | SUM | COUNT
*/
qualifier(A) ::= table_name(B). {A=B;}
qualifier ::= correlation_name.
correlation_name(A) ::= identifier(B). {A=B;}
set_quantifier(A) ::= . {A=NULL;}
set_quantifier(A) ::= DISTINCT. {A="DISTINCT";}
set_quantifier(A) ::= ALL. {A="ALL";}



/* Queries */

scalar_subquery ::= subquery.

subquery ::= LPAR query_expression RPAR.

query_expression(A) ::= non_join_query_expression(B). {A=B;}

query_expression ::= joined_table.

non_join_query_expression(A) ::= non_join_query_term(B). {A=B;}
non_join_query_expression ::= query_expression UNION query_term.
non_join_query_expression ::= query_expression UNION ALL query_term.
non_join_query_expression ::= query_expression UNION corresponding_spec query_term.
non_join_query_expression ::= query_expression UNION ALL corresponding_spec query_term.
non_join_query_expression ::= query_expression EXCEPT query_term.
non_join_query_expression ::= query_expression EXCEPT ALL query_term.
non_join_query_expression ::= query_expression EXCEPT corresponding_spec query_term.
non_join_query_expression ::= query_expression EXCEPT ALL corresponding_spec query_term.

non_join_query_term(A) ::= non_join_query_primary(B). {A=B;}
non_join_query_term ::= query_term INTERSECT  query_primary.
non_join_query_term ::= query_term INTERSECT ALL query_primary.
non_join_query_term ::= query_term INTERSECT corresponding_spec query_primary.
non_join_query_term ::= query_term INTERSECT ALL corresponding_spec query_primary.

non_join_query_primary(A) ::= simple_table(B). {A=B;}
non_join_query_primary ::= LPAR non_join_query_expression RPAR.

simple_table ::= query_specification.
simple_table(A) ::= table_value_constructor(B). {A=B;}
simple_table ::= explicit_table.

query_specification(A) ::= SELECT set_quantifier(B) select_list(C) table_expression(D) limit_clause(E). {
		D->last_relation_id = D->actions[count(D->actions)-1]->relation_id;
		
		if(isset(D->filter)){
			D->actions[] = new qpAction_filterResults(D->last_relation_id, D->filter);
		}
		$total_group = false;
		$expression_columns = Array();	
		if(isset(C->columns_projection))
			foreach(C->columns_projection as $p){
				if($p->is_set_function) {
					$total_group=true;
					$expression_columns = qpAction_projectColumns::mergeColumnsNoDuplicates($expression_columns, $p->getFilterColumnReferences());
				} elseif($p->is_math_function) {
					$expression_columns = qpAction_projectColumns::mergeColumnsNoDuplicates($expression_columns, $p->getFilterColumnReferences());
				}
			}
		if(isset(D->grouping_columns)){
			$expression_columns = qpAction_projectColumns::mergeColumnsNoDuplicates($expression_columns, D->grouping_columns);
		}
		if(isset(C->columns_projection)){
			D->actions[] = new qpAction_projectColumns(D->last_relation_id, 
				qpAction_projectColumns::mergeColumnsNoDuplicates(C->columns_projection, $expression_columns));
		} elseif(!(empty($expression_columns))) {
			D->actions[] = new qpAction_projectColumns(D->last_relation_id, $expression_columns);
		}
		if(isset(D->grouping_columns)){
			D->actions[] = new qpAction_groupTable(D->last_relation_id, D->grouping_columns, 
				qpAction_projectColumns::mergeColumnsNoDuplicates(C->columns_projection, $expression_columns));
			D->last_relation_id = D->actions[count(D->actions)-1]->relation_id;
		} else {
			if($total_group){
				D->actions[] = new qpAction_groupTable(D->last_relation_id, array(), C->columns_projection);
				D->last_relation_id = D->actions[count(D->actions)-1]->relation_id;
			}
		}
		if(isset(D->ordering)){
			D->actions[] = new qpAction_orderRelation(D->last_relation_id, D->ordering);
		}			
		if(B=="DISTINCT")
			D->actions[] = new qpAction_distinctValues(D->last_relation_id);
		elseif(B=="ALL"){
		}	
		if(isset(C->columns_projection)){
			$indexes = Array();
			for($i=0;$i<count(C->columns_projection);$i++)
				$indexes[] = $i;
			D->actions[] = new qpAction_selectColumnsByIndexes(D->last_relation_id, $indexes);
		}		
		if(isset(E))
			D->actions[] = new qpAction_limitRows(D->last_relation_id, E->rows, E->offset);
		A=D;
	}

select_list(A) ::= ASTERISK. {A = new stdClass();A->columns_projection = NULL;A->set_functions=NULL;}
select_list(A) ::= select_sublist(B). {
		A = new stdClass();
		A->columns_projection = Array();
		A->columns_projection[] = B;

	}
select_list(A) ::= select_list(B) COMMA select_sublist(C).{
		B->columns_projection[] = C;
		A=B;
	}

select_sublist(A) ::= derived_column(B). {A=B;}
select_sublist ::= qualifier PERIOD ASTERISK.

derived_column(A) ::= value_expression(B). {A=B;}
derived_column(A) ::= value_expression(B) as_clause(C). {A=B; A->alias=C;}

as_clause(A) ::= column_name(B). {A=B->value;}
as_clause(A) ::= AS column_name(B). {A=B->value;}

table_expression(A) ::= from_clause(B) where_clause(C) group_by_clause(D) having_clause orderby_clause(E). {
		A = new stdClass();
		A->actions = B->actions;
		if(isset(D)&&isset(D->grouping_columns))
			A->grouping_columns = D->grouping_columns;
		if(isset(C)&&isset(C->filter))
			A->filter = C->filter;
		if(isset(E)&&isset(E->ordering))
			A->ordering = E->ordering;
	}

limit_clause ::= .
limit_clause(A) ::= LIMIT INTNUM(B). {A = new stdClass();A->offset=0;A->rows=B->value;}
limit_clause(A) ::= LIMIT INTNUM(B) OFFSET INTNUM(C). {A = new stdClass();A->offset=C->value;A->rows=B->value;}
limit_clause(A) ::= LIMIT INTNUM(B) COMMA INTNUM(C). {A = new stdClass();A->offset=B->value;A->rows=C->value;}

orderby_clause ::= .
orderby_clause(A) ::= ORDER BY orderby_expression_list(B). {A = new stdClass();A->ordering=B;}
orderby_expression_list(A) ::= orderby_expression(B).{A=Array();A[]=B;}
orderby_expression_list(A) ::= orderby_expression_list(B) COMMA orderby_expression(C).{B[]=C;A=B;}
orderby_expression(A) ::= value_expression(B) ordering(C).{A = new stdClass();A->expression=B;A->order=C;}
ordering(A) ::= .{A='asc';}
ordering(A) ::= ASC.{A='asc';}
ordering(A) ::= DESC.{A='desc';}


from_clause(A) ::= FROM table_reference(B). {
		A = new stdClass();
		A->actions=B->actions;
	}
from_clause ::= table_reference COMMA table_reference.

table_reference(A) ::= table_name(B). {A = new stdClass();A->actions[]=new qpAction_loadTable(B->value);}
table_reference(A) ::= table_name(B) correlation_specification(C). {A = new stdClass();A->actions[]=new qpAction_loadTable(B->value, C->value);}
table_reference ::= derived_table correlation_specification.
table_reference(A) ::= joined_table(B).{A=B;}


correlation_specification(A) ::= correlation_name(B). {A=B;}
correlation_specification(A) ::= AS correlation_name(B). {A=B;}
correlation_specification ::= correlation_name LPAR derived_column_list RPAR.
correlation_specification ::= AS correlation_name LPAR derived_column_list RPAR.

derived_column_list ::= column_name_list.

derived_table ::= table_subquery.

table_subquery ::= subquery.

joined_table ::= cross_join.
joined_table(A) ::= qualified_join(B). {A=B;}
joined_table ::= LPAR joined_table RPAR.

cross_join ::= table_reference CROSS JOIN table_reference.

qualified_join(A) ::= table_reference(B) natural_join join_type(D) JOIN table_reference(E) join_specification(F). {
	A = new stdClass();
	A->join_type = D;
	$b_last_relation = B->actions[count(B->actions)-1];
	$e_last_relation = E->actions[count(E->actions)-1];
	A->actions = array_merge(B->actions, E->actions);
	A->actions[] = new qpAction_joinRelationsLeft($b_last_relation->relation_id, $e_last_relation->relation_id, F->filter);
}

natural_join ::= .
natural_join ::= NATURAL.

join_type ::= .
join_type ::= INNER.
join_type(A) ::= outer_join_type(B). {A=B;}
join_type ::= outer_join_type OUTER.
join_type ::= UNION.

outer_join_type(A) ::= LEFT(B). {A=B->value;}
outer_join_type(A) ::= RIGHT(B). {A=B->value;}
outer_join_type(A) ::= FULL(B). {A=B->value;}

join_specification ::= .
join_specification(A) ::= join_condition(B). {A = new stdClass();A->filter=B;}
join_specification ::= named_columns_join.

join_condition(A) ::= ON search_condition(B). {A=B;}

named_columns_join ::= USING LPAR join_column_list RPAR.

join_column_list ::= column_name_list.

where_clause ::= .
where_clause(A) ::= WHERE search_condition(B). {
	A = new stdClass();
	A->filter=B;
}

group_by_clause(A) ::= . {A = new stdClass();A->grouping_columns = NULL;}
group_by_clause(A) ::= GROUP BY grouping_column_reference_list(B). {A = new stdClass();A->grouping_columns = B;}

grouping_column_reference_list(A) ::= grouping_column_reference(B). {A = Array(); A[]=B;}
grouping_column_reference_list(A) ::= grouping_column_reference_list(B) COMMA grouping_column_reference(C). {B[]=C;A=B;}

grouping_column_reference(A) ::= column_reference(B). {A=B;}

/* TEMPORANEAMENTE DISABILITATO
grouping_column_reference ::= column_reference collate_clause.

collate_clause ::= COLLATE collation_name.

collation_name ::= qualified_name.
*/

having_clause ::= .
having_clause ::= HAVING search_condition.

table_value_constructor(A) ::= VALUES table_value_constructor_list(B). {A=B;}

table_value_constructor_list(A) ::= row_value_constructor(B). {A=B;}

/*
table_value_constructor_list ::= table_value_constructor_list COMMA row_value_constructor. {echo "LOADING TABLE VALUES...\n";}
*/

explicit_table ::= TABLE table_name.

query_term ::= non_join_query_term.
query_term ::= joined_table.

corresponding_spec ::= CORRESPONDING.
corresponding_spec ::= CORRESPONDING BY LPAR corresponding_column_list RPAR.

corresponding_column_list ::= column_name_list.

query_primary ::= non_join_query_primary.
query_primary ::= joined_table.


/*******************************/
/* Query expression components */
/*******************************/

row_value_constructor_list(A) ::= row_value_constructor_element(B). {
		A = Array();
		A[] = B;
	}
row_value_constructor_list(A) ::= row_value_constructor_list(B) COMMA row_value_constructor_element(C). {
		B[] = C;
		A = B;
	}

/*row_subquery ::= subquery.*/


row_subquery ::= NULLX.

truth_value ::= TRUE.
truth_value ::= FALSE.
truth_value ::= UNKNOWN.

comp_op(A) ::= OP_EQ(B). {A=B;}
comp_op(A) ::= OP_GTLT(B). {A=B;}
comp_op(A) ::= OP_LT(B). {A=B;}
comp_op(A) ::= OP_GT(B). {A=B;}
comp_op(A) ::= OP_LTEQ(B). {A=B;}
comp_op(A) ::= OP_GTEQ(B). {A=B;}

null_predicate(A) ::= column_reference(B) IS NULLX. {
		A = new filter_IsNullColumn(B);
	}
null_predicate(A) ::= column_reference(B) IS NOT NULLX. {
		A = new filter_IsNotNullColumn(B);
	}


/**************************/
/* More about constraints */
/**************************/

constraint_attributes ::= .
constraint_attributes ::= constraint_check_time.
constraint_attributes ::= constraint_check_time DEFERRABLE.
constraint_attributes ::= constraint_check_time NOT DEFERRABLE.
constraint_attributes ::= DEFERRABLE.
constraint_attributes ::= DEFERRABLE constraint_check_time.
constraint_attributes ::= NOT DEFERRABLE.
constraint_attributes ::= NOT DEFERRABLE constraint_check_time.

constraint_check_time ::= INITIALLY DEFERRED.
constraint_check_time ::= INITIALLY IMMEDIATE.

/*
<table constraint definition> ::= [ <constraint name definition> ] <table constraint> [ <constraint check time> ]

<table constraint> ::=
		<unique constraint definition>
	|	<referential constraint definition>
	|	<check constraint definition>

<unique constraint definition> ::= <unique specification> <left paren> <unique column list> <right paren>

<unique column list> ::= <column name list>

<referential constraint definition> ::=
		FOREIGN KEY <left paren> <referencing columns> <right paren> <references specification>

<referencing columns> ::= <reference column list>
*/
/************************************/
/* SQL Schema Definition Statements */
/************************************/


sql_schema_statement(A) ::= sql_schema_definition_statement(B). {A=B;}
sql_schema_statement(A) ::= sql_schema_manipulation_statement(B). {A=B;}

sql_schema_definition_statement(A) ::= table_definition(B). {
		A = new stdClass();
		A->actions=B;
	}
/*
		<schema definition>
	|	<table definition>
	|	<view definition>
	|	<grant statement>
	|	<domain definition>
	|	<character set definition>
	|	<collation definition>
	|	<translation definition>
	|	<assertion definition>

<schema definition> ::=
		CREATE SCHEMA <schema name clause>
			[ <schema character set specification> ]
			[ <schema element>... ]

<schema name clause> ::=
		<schema name>
	|	AUTHORIZATION <schema authorization identifier>
	|	<schema name> AUTHORIZATION <schema authorization identifier>

<schema authorization identifier> ::= <authorization identifier>

<schema character set specification> ::= DEFAULT CHARACTER SET <character set specification>

<schema element> ::=
		<domain definition>
	|	<table definition>
	|	<view definition>
	|	<grant statement>
	|	<assertion definition>
	|	<character set definition>
	|	<collation definition>
	|	<translation definition>

<domain definition> ::=
		CREATE DOMAIN <domain name> [ AS ] <data type>
			[ <default clause> ] [ <domain constraint> ] [ <collate clause> ]

<domain constraint> ::=
		[ <constraint name definition> ] <check constraint definition> [ <constraint attributes> ]
*/


table_definition(A) ::= CREATE TABLE table_name(B) LPAR table_element_list(C) RPAR. {
		A = new stdClass();
		A->action = new qpAction_createTable(B->value, C);
	}

/*
<table definition> ::=
		CREATE [ { GLOBAL | LOCAL } TEMPORARY ] TABLE <table name> <table element list> [ ON COMMIT { DELETE | PRESERVE } ROWS ]

<view definition> ::=
		CREATE VIEW <table name> [ <left paren> <view column list> <right paren> ]
			AS <query expression> [ WITH [ <levels clause> ] CHECK OPTION ]

<view column list> ::= <column name list>

<levels clause> ::= CASCADED | LOCAL

<grant statement> ::=
		GRANT <privileges> ON <object name> TO <grantee> [ { <comma> <grantee> }... ] [ WITH GRANT OPTION ]

<privileges> ::= ALL PRIVILEGES | <action list>

<action list> ::= <action> [ { <comma> action> }... ]

<action> ::=
		SELECT
	|	DELETE
	|	INSERT [ <left paren> <privilege column list> <right paren> ]
	|	UPDATE [ <left paren> <privilege column list> <right paren> ]
	|	REFERENCES [ <left paren> <privilege column list> <right paren> ]
	|	USAGE

<privilege column list> ::= <column name list>

<object name> ::=
		[ TABLE ] <table name>
	|	DOMAIN <domain name>
	|	COLLATION <collation name>
	|	CHARACTER SET <character set name>
	|	TRANSLATION <translation name>

<grantee> := PUBLIC | <authorization identifier>

<assertion definition> ::=
		CREATE ASSERTION <constraint name> <assertion check> [ <constraint attributes> ]

<assertion check> ::= CHECK <left paren> <search condition> <right paren>

<character set definition> ::=
		CREATE CHARACTER SET <character set name> [ AS ] <character set source>
		[ <collate clause> | <limited collation definition> ]

<character set source> ::= GET <existing character set name>

<existing character set name> ::=
		<standard character repertoire name>
	|	<implementation-defined character repertoire name>
	|	<schema character set name>

<schema character set name> ::= <character set name>

<limited collation definition> ::=
		COLLATION FROM <collation source>

<collation source> ::= <collating sequence definition> | <translation collation>

<collating sequence definition> ::=
		<external collation>
	|	<schema collation name>
	|	DESC <left paren> <collation name> <right paren>
	|	DEFAULT

<external collation> ::=
	EXTERNAL <left paren> <quote> <external collation name> <quote> <right paren>

<external collation name> ::= <standard collation name> | <implementation-defined collation name>

<standard collation name> ::= <collation name>

<implementation-defined collation name> ::= <collation name>

<schema collation name> ::= <collation name>

<translation collation> ::= TRANSLATION <translation name> [ THEN COLLATION <collation name> ]

<collation definition> ::=
		CREATE COLLATION <collation name> FOR <character set specification>
			FROM <collation source> [ <pad attribute> ]

<pad attribute> ::= NO PAD | PAD SPACE

<translation definition> ::=
		CREATE TRANSLATION <translation name>
			FOR <source character set specification>
			TO <target character set specification>
			FROM <translation source>

<source character set specification> ::= <character set specification>

<target character set specification> ::= <character set specification>

<translation source> ::= <translation specification>

<translation specification> ::=
		<external translation>
	|	IDENTITY
	|	<schema translation name>

<external translation> ::=
		EXTERNAL <left paren> <quote> <external translation name> <quote> <right paren>

<external translation name> ::=
		<standard translation name>
	|	<implementation-defined translation name>

<standard translation name> ::= <translation name>

<implementation-defined translation name> ::= <translation name>

<schema translation name> ::= <translation name>
*/

sql_schema_manipulation_statement(A) ::= drop_table_statement(B). {A=B;}
/*
		<drop schema statement>
	|	<alter table statement>
	|	
	|	<drop view statement>
	|	<revoke statement>
	|	<alter domain statement>
	|	<drop domain statement>
	|	<drop character set statement>
	|	<drop collation statement>
	|	<drop translation statement>
	|	<drop assertion statement>

<drop schema statement> ::= DROP SCHEMA <schema name> <drop behaviour>
*/

drop_behaviour ::= .
drop_behaviour ::= CASCADE.
drop_behaviour ::= RESTRICT.

/*
<alter table statement> ::= ALTER TABLE <table name> <alter table action>

<alter table action> ::=
		<add column definition>
	|	<alter column definition>
	|	<drop column definition>
	|	<add table constraint definition>
	|	<drop table constraint definition>

<add column definition> ::= ADD [ COLUMN ] <column definition>

<alter column definition> ::= ALTER [ COLUMN ] <column name> <alter column action>

<alter column action> ::= <set column default clause> | <drop column default clause>

<set column default clause> ::= SET <default clause>

<drop column default clause> ::= DROP DEFAULT

<drop column definition> ::= DROP [ COLUMN ] <column name> <drop behaviour>

<add table constraint definition> ::= ADD <table constraint definition>

<drop table constraint definition> ::= DROP CONSTRAINT <constraint name> <drop behaviour>
*/

drop_table_statement(A) ::= DROP TABLE drop_if_exists tables_list(C) drop_behaviour. {
		A = new stdClass();
		A->actions = Array();
		foreach(C as $c){
			A->actions[] = new qpAction_dropTable($c->value);
		}
	}

drop_if_exists ::= .
drop_if_exists ::= IF EXISTS.

tables_list(A) ::= table_name(B). {A = Array(); A[] = B;}
tables_list(A) ::= tables_list(B) COMMA table_name(C). {B[] = C; A=B;}

/*
<drop view statement> ::= DROP VIEW <table name> <drop behaviour>

<revoke statement> ::=
		REVOKE [ GRANT OPTION FOR ] <privileges> ON <object name>
			FROM <grantee> [ { <comma> <grantee> }... ] <drop behaviour>

<alter domain statement> ::= ALTER DOMAIN <domain name> <alter domain action>

<alter domain action> ::=
		<set domain default clause>
	|	<drop domain default clause>
	|	<add domain constraint definition>
	|	<drop domain constraint definition>

<set domain default clause> ::= SET <default clause>

<drop domain default clause> ::= DROP DEFAULT

<add domain constraint definition> ::= ADD <domain constraint>

<drop domain constraint definition> ::= DROP CONSTRAINT <constraint name>

<drop domain statement> ::= DROP DOMAIN <domain name> <drop behaviour>

<drop character set statement> ::= DROP CHARACTER SET <character set name>

<drop collation statement> ::= DROP COLLATION <collation name>

<drop translation statement> ::= DROP TRANSLATION <translation name>

<drop assertion statement> ::= DROP ASSERTION <constraint name>
*/

/************************************/
/* SQL Data Manipulation Statements */
/************************************/

sql_data_statement(A) ::= sql_data_change_statement(B). {A=B;}
/*
<SQL data statement> ::=
		<open statement>
	|	<fetch statement>
	|	<close statement>
	|	<select statement: single row>
	|	<SQL data change statement>

<open statement> ::= OPEN <cursor name>

<fetch statement> ::=
		FETCH [ [ <fetch orientation> ] FROM ] <cursor name> INTO <fetch target list>

<fetch orientation> ::=
		NEXT
	|	PRIOR
	|	FIRST
	|	LAST
	|	{ ABSOLUTE | RELATIVE } <simple value specification>

<simple value specification> ::= <parameter name> | <embedded variable name> | <literal>

<fetch target list> ::= <target specification> [ { <comma> <target specification> }... ]

<target specification> ::=
		<parameter specification>
	|	<variable specification>

<close statement> ::= CLOSE <cursor name>

<select statement: single row> ::=
	SELECT [ <set quantifier> ] <select list> INTO <select target list> <table expression>

<select target list> ::= <target specification> [ { <comma> <target specification> }... ]
*/

/*sql_data_change_statement ::= delete_statement_positioned.*/
sql_data_change_statement(A) ::= delete_statement_searched(B). {A=B;}
sql_data_change_statement(A) ::= insert_statement(B). {A=B;}
/*
sql_data_change_statement ::= update_statement_positioned.
*/
sql_data_change_statement(A) ::= update_statement_searched(B). {A=B;}
/*
delete_statement_positioned ::= DELETE FROM table_name WHERE CURRENT OF cursor_name.
*/

delete_statement_searched(A) ::= DELETE FROM table_name(B) delete_statement_where_search(C). {
		A = new stdClass();
		A->actions = Array();
		if(is_null(C)){
			A->actions[] = new qpAction_bulkDelete(B->value);
		} else {
			A->actions[]=new qpAction_loadTable(B->value);
			$last_relation_id = A->actions[count(A->actions)-1]->relation_id;
			A->actions[] = new qpAction_filterResults($last_relation_id, C);
			A->actions[] = new qpAction_addressedDelete($last_relation_id);
		}
	}

delete_statement_where_search(A) ::= . {
		A = NULL;
	}

delete_statement_where_search(A) ::= WHERE search_condition(B). {A=B;}


insert_statement(A) ::= INSERT INTO table_name(B) insert_columns_and_source(C). {
		A = new stdClass();
		A->actions[] = new qpAction_insertRow(B->value, C);
	}

insert_columns_and_source(A) ::= LPAR insert_column_list(B) RPAR query_expression(C). {
		A = Array();
		foreach(B as $key => $column_name){
			A[$column_name] = C[$key];
		}
	}
insert_columns_and_source ::= query_expression(B). {print_r(B);}
insert_columns_and_source ::= DEFAULT VALUES.

insert_column_list(A) ::= column_name_list(B). {A=B;}


/*
<update statement: positioned> ::=
		UPDATE <table name> SET <set clause list> WHERE CURRENT OF <cursor name>

*/

set_clause_list(A) ::= set_clause_list(B) COMMA set_clause(C). {
		B[] = C;
		A = B;
	}
set_clause_list(A) ::= set_clause(B). {
		A = Array();
		A[] = B;
	}

set_clause(A) ::= object_column(B) OP_EQ update_source(C). {A = new stdClass();A->column=B;A->new_value=C;}

object_column(A) ::= column_name(B). {A = new filter_ColumnReference(B->value);}

update_source(A) ::= value_expression(B). {A=B;}
update_source ::= NULL.
update_source ::= DEFAULT.

update_statement_searched(A) ::= UPDATE table_name(B) SET set_clause_list(C) update_statement_where_search(D). {
		A = new stdClass();
		A->actions = Array();
		A->actions[]=new qpAction_loadTable(B->value);
		$last_relation_id = A->actions[count(A->actions)-1]->relation_id;

		if(!is_null(D)){
			A->actions[] = new qpAction_filterResults($last_relation_id, D);
		}
		A->actions[]=new qpAction_updateValues($last_relation_id, C);
	}

update_statement_where_search(A) ::= . {A=NULL;}
update_statement_where_search(A) ::= WHERE search_condition(B). {A=B;}


/*
<SQL transaction statement> ::=
		<set transaction statement>
	|	<set constraints mode statement>
	|	<commit statement>
	|	<rollback statement>

<set transaction statement> ::=
		SET TRANSACTION <transaction mode> [ { <comma> <transaction mode> }... ]

<transaction mode> ::=
		<isolation level>
	|	<transaction access mode>
	|	<diagnostics size>

<isolation level> ::= ISOLATION LEVEL <level of isolation>

<level of isolation> ::=
		READ UNCOMMITTED
	|	READ COMMITTED
	|	REPEATABLE READ
	|	SERIALIZABLE

<transaction access mode> ::= READ ONLY | READ WRITE

<diagnostics size> ::= DIAGNOSTICS SIZE <number of conditions>

<number of conditions> ::= <simple value specification>

<set constraints mode statement> ::=
		SET CONSTRAINTS <constraint name list> { DEFERRED | IMMEDIATE }

<constraint name list> ::= ALL | <constraint name> [ { <comma> <constraint name> }... ]

<commit statement> ::= COMMIT [ WORK ]

<rollback statement> ::= ROLLBACK [ WORK ]
*/




/************************************/
/* MATH OPERATIONS                  */
/************************************/

math_numeric_value_expression(A) ::= ABS LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("abs");A->expression=B;}
math_numeric_value_expression(A) ::= ACOS LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("acos");A->expression=B;}
math_numeric_value_expression(A) ::= ASIN LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("asin");A->expression=B;}
math_numeric_value_expression(A) ::= ATAN LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("atan");A->expression=B;}
math_numeric_value_expression(A) ::= ATAN LPAR numeric_value_expression(B) COMMA numeric_value_expression(C) RPAR. {A = new filter_BinaryMathFunction("atan2");A->expression1=B;A->expression2=C;}
math_numeric_value_expression(A) ::= ATAN2 LPAR numeric_value_expression(B) COMMA numeric_value_expression(C) RPAR. {A = new filter_BinaryMathFunction("atan2");A->expression1=B;A->expression2=C;}
math_numeric_value_expression(A) ::= CEIL LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("ceil");A->expression=B;}
math_numeric_value_expression(A) ::= COS LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("cos");A->expression=B;}
math_numeric_value_expression(A) ::= COT LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("cot");A->expression=B;}
math_numeric_value_expression(A) ::= CRC32 LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("crc32");A->expression=B;}
math_numeric_value_expression(A) ::= DEGREES LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("degrees");A->expression=B;}
math_numeric_value_expression(A) ::= EXP LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("exp");A->expression=B;}
math_numeric_value_expression(A) ::= FLOOR LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("floor");A->expression=B;}
math_numeric_value_expression(A) ::= FORMAT LPAR numeric_value_expression(B) COMMA numeric_value_expression(C) RPAR. {A = new filter_BinaryMathFunction("format");A->expression1=B;A->expression2=C;}
math_numeric_value_expression(A) ::= LN LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("ln");A->expression=B;}
math_numeric_value_expression(A) ::= LOG LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("ln");A->expression=B;}
math_numeric_value_expression(A) ::= LOG LPAR numeric_value_expression(B) COMMA numeric_value_expression(C) RPAR. {A = new filter_BinaryMathFunction("log");A->expression1=B;A->expression2=C;}
math_numeric_value_expression(A) ::= LOG2 LPAR numeric_value_expression(B) RPAR. {$val2 = new filter_StaticIntnum(2);A = new filter_BinaryMathFunction("log");A->expression1=$val2;A->expression2=B;}
math_numeric_value_expression(A) ::= LOG10 LPAR numeric_value_expression(B) RPAR. {$val10 = new filter_StaticIntnum(10);A = new filter_BinaryMathFunction("log");A->expression1=$val10;A->expression2=B;}
math_numeric_value_expression(A) ::= MOD LPAR numeric_value_expression(B) COMMA numeric_value_expression(C) RPAR. {A = new filter_BinaryMathFunction("mod");A->expression1=B;A->expression2=C;}
math_numeric_value_expression(A) ::= PI LPAR RPAR. {A = new filter_UnaryMathFunction("pi");A->expression=new filter_EmptyExpression();}
math_numeric_value_expression(A) ::= POW LPAR numeric_value_expression(B) COMMA numeric_value_expression(C) RPAR. {A = new filter_BinaryMathFunction("pow");A->expression1=B;A->expression2=C;}
math_numeric_value_expression(A) ::= RADIANS LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("radians");A->expression=B;}
math_numeric_value_expression(A) ::= ROUND LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("round");A->expression=B;}
math_numeric_value_expression(A) ::= ROUND LPAR numeric_value_expression(B) COMMA numeric_value_expression(C) RPAR. {A = new filter_BinaryMathFunction("round");A->expression1=B;A->expression2=C;}
math_numeric_value_expression(A) ::= SIGN LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("sign");A->expression=B;}
math_numeric_value_expression(A) ::= SIN LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("sin");A->expression=B;}
math_numeric_value_expression(A) ::= SQRT LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("sqrt");A->expression=B;}
math_numeric_value_expression(A) ::= TAN LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryMathFunction("tan");A->expression=B;}
math_numeric_value_expression(A) ::= TRUNCATE LPAR numeric_value_expression(B) COMMA numeric_value_expression(C) RPAR. {A = new filter_BinaryMathFunction("truncate");A->expression1=B;A->expression2=C;}


/************************************/
/* DATE OPERATIONS                  */
/************************************/

date_value_expression(A) ::= DAY LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryDateFunction("day");A->expression=B;}
date_value_expression(A) ::= HOUR LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryDateFunction("hour");A->expression=B;}
date_value_expression(A) ::= MINUTE LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryDateFunction("minute");A->expression=B;}
date_value_expression(A) ::= MONTH LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryDateFunction("month");A->expression=B;}
date_value_expression(A) ::= SECOND LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryDateFunction("second");A->expression=B;}
date_value_expression(A) ::= WEEKDAY LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryDateFunction("weekday");A->expression=B;}
date_value_expression(A) ::= WEEKOFYEAR LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryDateFunction("weekofyear");A->expression=B;}
date_value_expression(A) ::= YEAR LPAR numeric_value_expression(B) RPAR. {A = new filter_UnaryDateFunction("year");A->expression=B;}
