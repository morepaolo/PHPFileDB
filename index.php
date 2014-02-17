<?php
	include ("phpfdb/PHPFDB.php");
	include ("phpfdb/PHPFDB_Exceptions.php");
	$db = new PHPFDB("./data/");
	
	// CREATE TABLE impiegati
	$sql = "drop table impiegati";
	$result = $db->query($sql);
	$sql = "create table impiegati(id int auto_increment, name varchar(20), lastname varchar(20), dept_id int)";
	$result = $db->query($sql);
	
	// CREATE TABLE dipartimenti
	$sql = "drop table dipartimenti";
	$result = $db->query($sql);
	$sql = "create table dipartimenti(id_dept int auto_increment, deptname varchar(20))";
	$result = $db->query($sql);
	
	// CREATE TABLE pagamenti
	$sql = "drop table pagamenti";
	$result = $db->query($sql);
	$sql = "create table pagamenti(id int auto_increment, id_impiegato int, importo float)";
	$result = $db->query($sql);
	
	// INSERISCO I DIPARTIMENTI
	$sql = "INSERT INTO dipartimenti (id_dept, deptname) VALUES (1, 'Ricerca')";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO dipartimenti (id_dept, deptname) VALUES (2, 'Sviluppo')";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO dipartimenti (id_dept, deptname) VALUES (3, 'Contabilità')";
	$result = $db->query($sql);
	// FINE - INSERISCO I DIPARTIMENTI
	
	// INSERISCO GLI IMPIEGATI
	$sql = "INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Paolo', 'Moretti', 1)";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Paolo', 'Prova ABCDE', 2)";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO impiegati (name, dept_id) VALUES ('Paolo', 1)";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO impiegati (lastname, dept_id) VALUES ('name = NULL', 2)";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Test', 'id=NULL!!', 1)";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Altro TEST', 'XXXXXXX', 2)";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Mario', 'Rossi', 1)";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO impiegati (name, lastname, dept_id) VALUES ('John', 'Doe', 1)";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Giuseppe', 'Bianchi', 1)";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Franco', 'Verdi', 2)";
	$result = $db->query($sql);
	// FINE - INSERISCO GLI IMPIEGATI
	
	// INSERISCO I PAGAMENTI
	$sql = "INSERT INTO pagamenti (id_impiegato, importo) values (1, 500.20)";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO pagamenti (id_impiegato, importo) values (2, 997.80)";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO pagamenti (id_impiegato, importo) values (3, 1516.37945)";
	$result = $db->query($sql);
	
	$sql = "INSERT INTO pagamenti (id_impiegato, importo) values (1, 1916.5)";
	$result = $db->query($sql);
	
	// FINE - INSERISCO I PAGAMENTI
	
	$sql = "SELECT * FROM tables";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "SELECT * FROM impiegati AS i";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "SELECT * FROM dipartimenti";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "SELECT id, name as firstname, lastname FROM impiegati WHERE name ='Paolo'";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "SELECT * FROM impiegati";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "select id, name, lastname, id_dept, deptname from impiegati left outer join dipartimenti on id_dept=dept_id where id_dept is not null or id_dept is null";
	$result = $db->query($sql);
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "SELECT name, count(*) as quanti,deptname FROM dipartimenti left join impiegati on id_dept=dept_id group by deptname, name";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql="SELECT name, count(*) as quanti FROM dipartimenti left join impiegati on id_dept=dept_id group by deptname, name";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "SELECT deptname, name FROM dipartimenti left join impiegati on id_dept=dept_id";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql="update impiegati set name='UGOooo' where id=1";
	$result = $db->query($sql);	
	//$dump = $result->HTMLDump();
	//echo $dump;
	
	$sql = "SELECT * FROM impiegati AS i";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql="update impiegati set name='Pippo' where id=1";
	$result = $db->query($sql);	
	//$dump = $result->HTMLDump();
	//echo $dump;
	
	$sql = "SELECT * FROM impiegati AS i";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
			
	$sql = "SELECT id, max(importo) as importo_massimo, min(importo) as importo_minimo FROM pagamenti AS p";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "SELECT name, lastname, max(importo) as pagamento_massimo FROM impiegati left join pagamenti on impiegati.id=pagamenti.id_impiegato group by name, lastname";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
		
	$sql = "select id_impiegato, round(importo) as importo_arrotondato, pi() as Pgreco from pagamenti";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "select round(pi()) as Pgreco from pagamenti";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "select importo, round(importo, 2) as test, truncate(importo,2) as test2, abs(-5) as valore_ass_1, abs(5) as valore_ass_2, acos(0) as acos1, asin(-1) as asin1, atan(2) as atan_1,  atan(-2) as atan_22  from pagamenti where id_impiegato=3";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "select atan(-2,2) test1, atan2(-2,2) as test2, ceil(3.1) as ceil1, cos(pi()) as cos1, cot(12) as cot_1, crc32('MySQL') as crcmysql, degrees(pi()) as deg_pi, exp(2) as e2, floor(5.98) as fl  from pagamenti where id_impiegato=3";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "select format(54132412.19, 1) as fmt, ln(exp(1)) as ln1, ln(-5) as ln2, log(exp(1)) as log_1, log(10, 100) as log_2, log2(65536) as log65536, log10(1000) as log1000, mod(11.5,2) as test_mod, power(2,8) as test_pow from pagamenti where id_impiegato=3";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "select radians(180) as test_rad, sign(5) as sign_1, sign(-5) as sign_2, sign(0) as sign_3, sin(0) as sin_90, sqrt(sqrt(16)) as test_sqrt, tan(radians(45)) as test_tan from pagamenti where id_impiegato=3";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "select * from impiegati";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "select * from impiegati order by name limit 4 OFFSET 2";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	echo "TODO: Mancano le operazioni ternarie:<br />";
	echo "CONV(N,from_base,to_base)<br />";
	echo "Manca operazione unaria HEX(N_or_S), perchè non c'è supporto ai numeri hex<br />";
	echo "Manca l'operazione unaria RAND, capire come gestire i valori random float 0:1 e il seed<br />";
	echo "Finite a parte queste 3 indicate<br />";
	echo "IN PROGRESS: Order by, Ho finito la parte di grammatica, mi manca la parte di query planner<br />";
	
	$sql = "select * from impiegati";
	echo "TESTING QUERY PLANNER CACHING: $sql<br />";
	$result = $db->query($sql);	
	$result->storePlan();
	
	$sql = "select * from cache";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	/*
	echo "<pre>";
	print_r($result->plan);
	echo "</pre>";
	*/
?>    