<?php
	header('Content-Type: text/html; charset=utf-8');
	
	session_start();
	include ("phpfdb/phpfdb.php");
	include ("phpfdb/phpfdb_exceptions.php");
	if(isset($_SESSION['cur_DB'])){
		$db = new PHPFDB("./databases/".$_SESSION['cur_DB']."/");
	} else {
		$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
		$string = ""; 
		$length = 10;   
		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters)-1)];
		}
		$_SESSION['cur_DB'] = $string;
		$db = new PHPFDB("./databases/".$_SESSION['cur_DB']."/");
		$sql = "create table impiegati(id int auto_increment, name varchar(20), lastname varchar(20), dept_id int);";
		$sql .= "create table dipartimenti(id_dept int auto_increment, deptname varchar(20));";
		$sql .= "create table pagamenti(id int auto_increment, id_impiegato int, importo float, data_inserimento date, data_aggiornamento datetime);";
		// INSERTING DEPARTMENTS
		$sql .= "INSERT INTO dipartimenti (id_dept, deptname) VALUES (1, 'Ricerca');".
				"INSERT INTO dipartimenti (id_dept, deptname) VALUES (2, 'Sviluppo');".
				"INSERT INTO dipartimenti (id_dept, deptname) VALUES (3, 'Contabilità');";
		// INSERTING EMPLOYEES
		$sql .= "INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Paolo', 'Moretti', 1);".
				"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Paolo', 'Prova ABCDE', 2);".
				"INSERT INTO impiegati (name, dept_id) VALUES ('Paolo', 1);".
				"INSERT INTO impiegati (lastname, dept_id) VALUES ('name = NULL', 2);".
				"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Test', 'id=NULL!!', 1);".
				"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Altro TEST', 'XXXXXXX', 2);".
				"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Mario', 'Rossi', 1);".
				"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('John', 'Doe', 1);".
				"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Giuseppe', 'Bianchi', 1);".
				"INSERT INTO impiegati (name, lastname, dept_id) VALUES ('Franco', 'Verdi', 2);";
		// INSERTING PAYMENTS
		$sql .= "INSERT INTO pagamenti (id_impiegato, importo, data_inserimento, data_aggiornamento) values (1, 500.20, '2014-01-23', '2014/01/24 06:12:58');".
				"INSERT INTO pagamenti (id_impiegato, importo, data_inserimento) values (2, 997.80, '2012-10-28');".
				"INSERT INTO pagamenti (id_impiegato, importo, data_inserimento) values (3, 1516.37945, '2015-04-03');".
				"INSERT INTO pagamenti (id_impiegato, importo, data_inserimento) values (1, 1916.5, '2012-09-04');";
		$result = $db->query($sql);
	}
	switch (@$_POST['action']){
		case "query":
			if(isset($_POST['query'])){
				$sql = $_POST['query'];
				$result = $db->query($sql);
				if($result->error){
					$html = $result->error_message;
				} else {
					$html = "<table cellpadding='4' cellspacing='0' style='border-collapse:collapse;margin-bottom:20px;'>";
					if(isset($result->data->cols))
						$colspan=count($result->data->cols);
					else
						$colspan=2;
					if(isset($result->data->cols)){
						$html .= "<tr>";
						foreach($result->data->cols as $cur_column){
							$html .= "<th style='border:1px solid black;'>".$cur_column->name."</th>";
						}
						$html .= "</tr>";
					}
					if(isset($result->data->cols)){
						foreach($result->data->rows as $cur_row){
							$html .= "<tr>";
							for($i=0;$i<count($result->data->cols);$i++)
								$html .= "<td style='border:1px solid black;'>".$result->data->cols[$i]->toString($cur_row->values[$i])."</td>";
							$html .= "</tr>";
						}
						$col_1_span = floor((count($result->data->cols))/2);
						$col_2_span = (count($result->data->cols)) - $col_1_span;
					} else {
						$col_1_span = 1;
						$col_2_span = 1;	
					}
					$html .= "<tr>";
					$html .= "<td colspan='".$col_1_span."' style='background-color:#FFBBFF;border-right:1px solid black;font-weight:bold;'>plan: ".(round($result->planning_duration,3)*1000)." msec";
					if($result->from_cache) $html .= " <sub>(cache)</sub>";
					$html .= "</td>";
					$html .= "<td colspan='".$col_2_span."' style='background-color:#FFBBFF;text-align:right;font-weight:bold;'>exec: ".(round($result->execution_duration,3)*1000)." msec</td>";
					$html .= "</tr>";
					$html .= "</table>";
				}
				echo $html;
			}
			break;
		default: /* GENERATE PAGE'S HTML */ ?>
<!doctype html>
<html>
	<head>
		<title>PHPFileDB Demo page</title>
		<meta charset="utf-8"/>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.22.0/codemirror.min.js" charset="UTF-8"></script>
		<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.22.0/codemirror.css" charset="UTF-8"></link>
		<script src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.22.0/mode/sql/sql.js" charset="UTF-8"></script>
		<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
		<style type="text/css">
			.CodeMirror {
				height:150px;
				border-top: 1px solid black;
				border-bottom: 1px solid black;
			}
			li{margin-left:0px;margin-bottom:4px;padding:2px;cursor:pointer;}
			li:hover{background-color:#99ccff;}
		</style>
		<script type="text/javascript">
			var doc;
			window.onload = function() {
				doc = CodeMirror.fromTextArea(document.getElementById('query'), {
					mode: "text/x-mysql",
					indentWithTabs: true,
					smartIndent: true,
					lineNumbers: true,
					matchBrackets : true,
					autofocus: true
				});
				$("#execute_all").click(function(){
					executeQuery(doc.getValue());
				});
				$("#execute_selected").click(function(){
					executeQuery(doc.getValue());
				});
			};
			function executeQuery(query){
				$.ajax({
					url:"demo.php",
					method:"POST",
					data: "action=query&query="+query,
					success: function(response){$("#result").html(response);},
					failure: function(response){$("#result").html("ERROR FROM SERVER");}
				});
			}
			function generateShowQuery(tableName){
				doc.setValue("select * from "+tableName);
			}
		</script>
	</head>
	<body style="font-family:Arial, helvetica, sans-serif;">
		<div>
			<h1>PHPFileDB TEST Page <a href="https://github.com/morepaolo/PHPFileDB">https://github.com/morepaolo/PHPFileDB</a></h1>
		</div>
		<div style="width:200px;float:left;font-size:14px;font-weight:bold;color:#ffffff;">
			<div style="background-color:#1e90ff;padding:4px;">
				TABLES àèìòù
			</div>
			<ul style="list-style:none;padding:4px;color:#000000;">
				<?php 
					$result = $db->query("select * from tables");
					while ($array = $result->FetchRow()) {
						echo "<li onclick=\"generateShowQuery('".$array['name']."');\">".$array['name']."</li>";
					}
				?>
			</ul>
		</div>
		<div style="margin-left:210px;">
			<input type="button" name="execute_selected" id="execute_selected" value="EXECUTE SELECTED" />
			<input type="button" name="execute_all" id="execute_all" value="EXECUTE ALL" />
            <textarea id="query" name="query">-- INSERT HERE YOUR QUERY
select * from tables;</textarea>
			<div style="background-color:#1e90ff;padding:4px;">
				RESULT
			</div>
			<div id="result">
			</div>
		</div>
	</body>
</html>
			<?php break;
		}
?>
