start ::= query_specification.

query_specification ::= SELECT. {echo "OK";}
query_specification ::= SELECT set_quantifier.

set_quantifier ::= DISTINCT|ALL.
