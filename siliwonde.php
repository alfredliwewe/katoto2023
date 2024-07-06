<?php

$db = new sqlite3("database.db");


$sql = $db->query("SELECT * FROM student");
/*
$text = '';
while ($row = $sql->fetchArray()) {
	$text .= $row['regnumber'].",".rand(1,60).",".rand(0,20).",".rand(0,20)."\n";
}
file_put_contents('try.csv', $text);

echo "done";

*/

while ($row = $sql->fetchArray()) {
	$name = str_replace("\\r", "", $row['fullname']);
	$id = $row['id'];

	$upd = $db->query("UPDATE student SET fullname = '$name' WHERE id = '$id' ");
}
echo "done";