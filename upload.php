<?php

$db = new sqlite3("robert.db");

$text = file_get_contents("data.sql");

$chars = explode(";", $text);

foreach ($chars as $sql) {
	if ($db->query($sql)) {
		echo "done<hr>";
	}
}