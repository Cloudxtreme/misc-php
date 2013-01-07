<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<!--
This script dumps content of given MySQL database to a local file.
-->

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
	<head>
		<title>mysql dump db</title>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
		<meta content="pl" http-equiv="content-language"/>
	</head>
	<body>
		<font face="Courier New">
<?php
$srcDB = new PDO("mysql:host=Your-MySQL-Host;dbname=Your-Database", "your-login", "your-passw0rd");
$tableNames = array();

$result = $srcDB->query("show tables");
while ($row = $result->fetch(PDO::FETCH_NUM)) {
	$tableNames[] = $row[0];
}

$file = fopen("backup". date("Ymd").".sql","w");

foreach ($tableNames as $tableName) {
	echo "<b>$tableName</b>: ";
	$srcStatement = $srcDB->query('SELECT * FROM '.$tableName);
	fwrite($file, 'INSERT INTO '.$tableName.' VALUES' . "\n");
	$first = 1;
	$rowcount = 0;
	while ($row = $srcStatement->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		if ($first == 0)
		{
			fwrite($file, ",\n");
		}
		fwrite($file, '(' . implode(',', array_map(array($srcDB, 'quote'), $row)) . ')');
		$first = 0;
		if (($rowcount % 100) == 0)
		{
			echo " $rowcount ";
		}
		elseif (($rowcount % 10) == 0) {
			echo ".";
		}
		$rowcount++;
	}
	$srcStatement = null;
	echo " done<br />\n";
	fwrite($file, ";\n");
}
echo "DONE!";
?>
		</font>
	</body>
</html>
