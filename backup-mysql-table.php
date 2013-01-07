<!--
Very simple script to create backup of MySQL database table.
-->

<?php
$mydb = new PDO("mysql:host=your_mysql_host;dbname=your_database", "your_login", "your_p@ssw0rD");
$tableNames = array('your_table1', 'your_table2');
foreach ($tableNames as $tableName) {
	$myquery = $mydb->query('SELECT * FROM ' . $tableName);
	echo('INSERT INTO ' . $tableName . ' VALUES' . "\n");
	$first = 1;
	while ($row = $myquery->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		if ($first == 0)
		{
			echo(",\n");
		}
		echo('(' . implode(',', array_map(array($mydb, 'quote'), $row)) . ')' . "\n");
		$first = 0;
	}
	$myquery = null;
}
?>
