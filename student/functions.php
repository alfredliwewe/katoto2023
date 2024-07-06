<?php
function student($studentId)
{
	global $db;

	$sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
	$data = $sql->fetchArray();
	if ($data) {
		return $data['fullname'];
                                    
	}
	else{
		return "unknown";
	}
}

function acayear($id)
{
	global $db;

	$sql = $db->query("SELECT * FROM year WHERE id = '$id' ");
	$data = $sql->fetchArray();
	if ($data) {
		return $data['name'];
	}
	else{
		return "unknown";
	}
}

function subject($id)
{
	global $db;

	$sql = $db->query("SELECT * FROM subject WHERE id = '$id' ");
	$data = $sql->fetchArray();
	if ($data) {
		
		return $data['name'];
	}
	else{
		return "unknown";
	}
}

function button($studentId, $sql)
{
	global $db;
	global $form;
	global $term;
	global $acayear;
	$bool = true;
	while ($row = $sql->fetchArray()) {
		$subId = $row['subject'];
		//check if not already available
		$sql_check = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND subject = '$subId' ");
		$in = $sql_check->fetchArray();
		if ($in) {
			//do nothing
		}
		else{
			$bool =false;
		}
	}

	if ($bool == true) {
		return "<i class='fa fa-check text-success'></i> done";
	}
	else{
		return "<button class='btn btn-sm btn-info' onclick=\"upload_results('$studentId');\"><i class='fa fa-upload'></i> Upload</button>";
	}
}

function ma_points($score)
{
	$score = (int)$score;

	if ($score < 40) {
		return 9;
	}
	elseif ($score < 50) {
		return 8;
	}
	elseif ($score < 55) {
		return 7;
	}
	elseif ($score < 60) {
		return 6;
	}
	elseif ($score < 65) {
		return 5;
	}
	elseif ($score < 70) {
		return 4;
	}
	elseif ($score < 75) {
		return 3;
	}
	elseif ($score < 80) {
		return 2;
	}
	elseif ($score < 100) {
		return 1;
	}
	else{
		return 0;
	}
}

function average($array)
{
	$total = 0;
	if (count($array) == 0) {
		return 0;
	}
	else{
		for ($i=0; $i < count($array); $i++) { 
			$total += $array[$i];
		}
		$average = $total / count($array);
		return round($average, 0);
	}
}

function aggregate_points($studentId)
{
	global $db;
	global $form;
	global $term;
	global $acayear;

	//get english score
	$total = 0;
	$correction = [];

	$eng_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND subject = '4'");
	$eng_data = $eng_sql->fetchArray();
	$eng_score = $eng_data['score'];

	array_push($correction, $eng_score);
	$point = ma_points($eng_score);
	$total += $point;
	
	$sql_check = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND subject != '4' ORDER BY score DESC LIMIT 5");
	
	while ($row = $sql_check->fetchArray()) {
		$point = ma_points($row['score']);
		$total += $point;
		array_push($correction, $row['score']);
	}
	
	return [$total, average($correction)];
}

function position_in_class($studentId, $subId)
{
	global $db;
	global $form;
	global $term;
	global $acayear;

	//select all from the same class
	$sql = $db->query("SELECT * FROM scores WHERE subject = '$subId' AND term = '$term' AND form = '$form' AND year = '$acayear' ");
	$all = $sql->num_rows;

	$pos = 0;
	$i = 1;
	while ($row = $sql->fetchArray()) {
		if ($row['student'] == $studentId) {
			$pos = $i;
		}
		$i += 1;
	}

	return $pos."/".$all;
}

function score($index, $subId, $studentId)
{
	global $db;
	global $form;
	global $term;
	global $acayear;

	$sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND subject = '$subId' AND term = '$term' AND form = '$form' AND year = '$acayear' ");
	$data = $sql->fetchArray();

	switch ($index) {
		case '0':
			return $data['ca1'];
			break;

		case '1':
			return $data['ca2'];
			break;
		
		case '2':
			return $data['end_term'];
			break;
		default:
			return 0;
			break;
	}
}
?>