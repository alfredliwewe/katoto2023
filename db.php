<?php
//unlink("database.db");

$db = new sqlite3("database.db");

if (isset($_GET['r'])) {
	$text = file_get_contents("w3css/ini.sql");

	$chars = explode(";", $text);

	foreach ($chars as $sql) {
		if ($db->query($sql)) {
			echo "done<hr>";
		}
	}
}

if (isset($_POST['query'])) {
	if ($db->query($_POST['query'])) {
		echo "Query executed successfully";
	}
	else{
		echo $db->lastErrorMsg();
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Run queries</title>
</head>
<body>
<form method="POST">
	<p>
		<textarea name="query" rows="6" style="width: 600px;font-family: consolas;color:blue;"></textarea>
	</p>
	<p>
		<input type="submit" name="">
	</p>
</form>
</body>
</html>