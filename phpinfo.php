<?php
	phpinfo();
	$sq = sqlite_open("miodb.db", 0666, $sqlite_error);
if(!$sq)
{
    die("Errore Sqlite: ".$sqlite_error);
}

sqlite_query($sq, "CREATE TABLE prova_tbl (campo varchar(10) UNIQUE)");
for($i = 0; $i < 10; ++$i)

{
    sqlite_query($sq, "INSERT INTO prova_tbl VALUES ('Prova $i')");
}

$result = sqlite_query($sq, "SELECT a.* FROM prova_tbl AS a");
while($data = sqlite_fetch_array($result))

{
    echo $data['campo']."<br />";
}
sqlite_close($sq);
?>