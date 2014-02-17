%token_prefix TK_

/* QUESTO L'HO AGGIUNTO IO, TESTARE... */

query ::= query_specification.

query_specification ::= SELECT select_list table_expression.
query_specification ::= SELECT set_quantifier select_list table_expression.

set_quantifier ::= DISTINCT | ALL.

select_list ::= NULLX.
table_expression ::= ALL.


truth_value ::= TRUE | FALSE | UNKNOWN.
comp_op ::= OP_EQ | OP_GTLT | OP_LT | OP_GT | OP_LTEQ | OP_GTEQ.
