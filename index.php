<?php
	include ("phpfdb/phpfdb.php");
	include ("phpfdb/phpfdb_exceptions.php");
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
	$sql = "create table pagamenti(id int auto_increment, id_impiegato int, importo float, data_inserimento date, data_aggiornamento datetime)";
	$result = $db->query($sql);
	
	echo "TABLES CREATED<br />";
	
	// INSERISCO I DIPARTIMENTI
	$sql = "INSERT INTO dipartimenti (id_dept, deptname) VALUES (1, 'Ricerca');".
			"INSERT INTO dipartimenti (id_dept, deptname) VALUES (2, 'Sviluppo');".
			"INSERT INTO dipartimenti (id_dept, deptname) VALUES (3, 'Contabilità')";
	$result = $db->query($sql);
	// FINE - INSERISCO I DIPARTIMENTI
	
	// INSERISCO GLI IMPIEGATI
	$sql = "INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Paolo', 'Moretti', 1);".
			"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Paolo', 'Prova ABCDE', 2);".
			"INSERT INTO impiegati (name, dept_id) VALUES ('Paolo', 1);".
			"INSERT INTO impiegati (lastname, dept_id) VALUES ('name = NULL', 2);".
			"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Test', 'id=NULL!!', 1);".
			"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Altro TEST', 'XXXXXXX', 2);".
			"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Mario', 'Rossi', 1);".
			"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('John', 'Doe', 1);".
			"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Giuseppe', 'Bianchi', 1);".
			"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Franco', 'Verdi', 2);";
	$result = $db->query($sql);
	// FINE - INSERISCO GLI IMPIEGATI
	
	// INSERISCO I PAGAMENTI
	$sql = "INSERT INTO pagamenti (id_impiegato, importo, data_inserimento, data_aggiornamento) values (1, 500.20, '2014-01-23', '2014/01/24 06:12:58');".
			"INSERT INTO pagamenti (id_impiegato, importo, data_inserimento) values (2, 997.80, '2012-10-28');".
			"INSERT INTO pagamenti (id_impiegato, importo, data_inserimento) values (3, 1516.37945, '2015-04-03');".
			"INSERT INTO pagamenti (id_impiegato, importo, data_inserimento) values (1, 1916.5, '2012-09-04');";
	$result = $db->query($sql);
	// FINE - INSERISCO I PAGAMENTI
	
	$sql = "SELECT * FROM tables";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "SELECT * FROM pagamenti where data_inserimento>'2012-09-04' order by data_inserimento asc";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "SELECT * FROM impiegati AS i";
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
	
	$sql = "SELECT * FROM pagamenti order by data_inserimento asc";
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
	
	$sql = "select * from impiegati order by name desc, lastname asc";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
		
	$sql = "select * from impiegati";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "select * from cache";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	
	echo "TODO: Mancano le operazioni matematiche ternarie:<br />";
	echo "CONV(N,from_base,to_base)<br />";
	echo "Manca operazione unaria HEX(N_or_S), perchè non c'è supporto ai numeri hex<br />";
	echo "Manca l'operazione unaria RAND, capire come gestire i valori random float 0:1 e il seed<br />";
	echo "Finite a parte queste 3 indicate<br />";
	
	$sql = "select importo, data_inserimento, year(data_inserimento) as anno, day(data_inserimento) as giorno, month(data_inserimento) as mese, year('2014-02-24') as test, weekday('2007-11-06'), weekofyear('2008-02-20') as WY from pagamenti";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	echo "ALTRO TODO: IN PROGRESS, TIPO \"DATE\"<br />";
	echo "FUNZIONI SUL TIPO DATE IMPLEMENTATE: day, month, year<br />";
	echo "IL TIPO \"DATE\" OCCUPA 4 byte, ma dovrebbe occuparne solo 3<br />";
	echo "IL CONVERTITORE string2Datetime GESTISCE SOLO IL - COME SEPARATORE DI CAMPO, MENTRE MYSQL<br />";
	echo "SUPPORTA QUALUNQUE CARATTERE DI PUNTEGGIATURA (COME ./@ ...)<br />";
	echo "IN PROGRESS: QUERY PLAN CACHING, FUNZIONA IL SISTEMA GENERICO MA SOLO LE ACTION LOAD_TABLE E RETURN_RELATION SONO IMPLEMENTATE<br />";
	
	echo "DONE 24-02-2014<br />";
	echo "ADDED SUPPORT TO date_function('2014-02-24')<br />";
	echo "ADDED SUPPORT TO WEEKDAY, WEEKOFYEAR <br />";
	echo "ADDED SUPPORT TO DATETIME, HOUR MINUTE SECOND FUNCTION"
	
	$sql = "select importo, data_aggiornamento, year(data_aggiornamento) as anno, day(data_aggiornamento) as giorno, month(data_aggiornamento) as mese, weekday(data_aggiornamento), weekofyear(data_aggiornamento) as WY, hour(data_aggiornamento) as H, minute(data_aggiornamento) as MINUT, second(data_aggiornamento) as sec from pagamenti";
	$result = $db->query($sql);	
	$dump = $result->HTMLDump();
	echo $dump;
	
	$sql = "delete from cache";
	$result = $db->query($sql);
	/*
	echo "<pre>";
	print_r($result->plan);
	echo "</pre>";
	*/
?>    
