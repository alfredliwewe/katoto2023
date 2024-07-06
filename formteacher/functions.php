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

function getPapers($studentId)
{
	global $db;
	global $form;
	global $term;
	global $acayear;
	global $group;

	$them = [];

	$sql = $db->query("SELECT * FROM scores WHERE form = '$form' AND term = '$term' AND `group` = '$group' AND year = '$acayear' AND student = '$studentId' ");
	//echo $db->lastErrorMsg();

	while ($row = $sql->fetchArray()) {
		array_push($them, $row['id']);
	}

	return $them;
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

function button($studentId, $sql, $all_count)
{
	global $db;
	global $form;
	global $term;
	global $acayear;
	global $group;
	$bool = true;

	$i = 0;

	while ($row = $sql->fetchArray()) {
		$subId = $row['subject'];
		//check if not already available
		$sql_check = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND subject = '$subId' AND `group` = '$group'");
		$in = $sql_check->fetchArray();
		if ($in) {
			//do nothing
			$bool = false;
			$i++;
		}
		else{
			
		}
	} 

	$remaining = $all_count - $i;
	if ($remaining == 0) {
		$text = "<i class='fa fa-check text-success'></i> done";
	}
	else{
		$text = "<font class='text-secondary'>$remaining remaining</font>";
	}

	if ($bool != true) {
		return "$text <button class=\"btn btn-info btn-sm\" onclick=\"edit_grades('$studentId')\"><i class=\"fa fa-pen-alt\"></i> Edit</button>";
	}
	else{
		return "<button class='btn btn-sm btn-info' onclick=\"upload_results('$studentId');\"><i class='fa fa-upload'></i> Upload</button>";
	}
}

function ma_points($score)
{
	$score = (int)$score;
	if ($score < 1) {
		return 9;
	}
	elseif ($score < 40) {
		return 9;
	}
	elseif ($score < 45) {
		return 8;
	}
	elseif ($score < 50) {
		return 7;
	}
	elseif ($score < 55) {
		return 6;
	}
	elseif ($score < 60) {
		return 5;
	}
	elseif ($score < 65) {
		return 4;
	}
	elseif ($score < 70) {
		return 3;
	}
	elseif ($score < 80) {
		return 2;
	}
	elseif ($score <= 100) {
		return 1;
	}
	else{
		return 9;
	}
}

function ma_pointsShow($score, $form)
{
	$form = (int)$form;
	$score = (int)$score;
	if ($form > 2) {
		if ($score < 1) {
			return 9;
		}
		elseif ($score >= 0 && $score <= 39) {
			return "9";
		}
		elseif ($score >= 40 && $score <= 44) {
			return "8";
		}
		elseif ($score >= 45 && $score <= 52) {
			return "7";
		}
		elseif ($score >= 53 && $score <= 60) {
			return "6";
		}
		elseif ($score >= 61 && $score <= 65) {
			return "5";
		}
		elseif ($score >= 66 && $score <= 69) {
			return "4";
		}
		elseif ($score >= 70 && $score <= 74) {
			return "3";
		}
		elseif ($score >= 75 && $score <= 79) {
			return "2";
		}
		elseif ($score >= 80 && $score <= 100) {
			return "1";
		}
		else{
			return 9;
		}
	}
	else{
		if ($score >= 0 && $score <= 39) {
			return "F";
		}
		elseif ($score >= 40 && $score <= 49) {
			return "D";
		}
		elseif ($score >= 50 && $score <= 64) {
			return "C";
		}
		elseif ($score >= 65 && $score <= 75) {
			return "B";
		}
		elseif ($score >= 76 && $score <= 100) {
			return "A";
		}
		else{
			return "F";
		}
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

function getUniform($stream)
{
	$stream = strtolower(trim($stream));
	if ($stream == "open" || $stream == "evening") {
		return "Black trousers and white shirt";
	}
	else{
		return "Grey pair of trousers/skirt & sky blue shirt/";
	}
}

function has_passed($studentId)
{
	global $db;
	global $form;
	global $term;
	global $acayear;
	global $group;

	$sql_check = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	
	$status = true;
	$count_passed = 0;
		
	$grades = [];
	while ($row = $sql_check->fetchArray()) {
		$grades[$row['subject']] = $row['score'];
		if ($form > 2) {
			if ((int)$row['score'] >= 40) {
				$count_passed += 1;
			}
		}
		else{
			if ((int)$row['score'] >= 40) {
				$count_passed += 1;
			}
		}
	}

	if ($count_passed < 6) {
		$status = false;
		$coz = "the student did not pass a minimum of 6 subjects";
	}

	if (count($grades) > 5) {
		if ($form > 2) {
			if (isset($grades[4])) { ////////////////////// this 4 is subject id for English
				if ($grades[4] < 40) {
					$status = false;
					$coz = "the student has failed English which is a key subject";
				}
			}
			else{
				$status = false;
				$coz = "the student did not sit for English exam";
			}
		}
		else{
			if (isset($grades[4])) { ////////////////////// this 4 is subject id for English
				if ($grades[4] < 40) {
					$status = false;
					$coz = "the student has failed English which is a key subject";
				}
			}
			else{
				$status = false;
				$coz = "the student did not sit for English exam";
			}
		}
	}
	else{
		$status = false;
		$coz = "the student did not write a minimum of six subjects";
	}

	if ($status == true) {
		return "Has Passed";
	}
	else{
		return "Failed because ".$coz;
	}
}

function has_passed_bool($studentId)
{
	global $db;
	global $form;
	global $term;
	global $acayear;
	global $group;

	$sql_check = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	
	$status = true;
	$count_passed = 0;
			
	$grades = [];
	while ($row = $sql_check->fetchArray()) {
		$grades[$row['subject']] = $row['score'];
		if ($form > 2) {
			if ((int)$row['score'] >= 40) {
				$count_passed += 1;
			}
		}
		else{
			if ((int)$row['score'] >= 40) {
				$count_passed += 1;
			}
		}
	}

	if ($count_passed < 6) {
		$status = false;
	}

	if (count($grades) > 5) {
		if ($form > 2) {
			if (isset($grades[4])) { ////////////////////// this 4 is subject id for English
				if ($grades[4] < 40) {
					$status = false;
				}
			}
			else{
				$status = false;
			}
		}
		else{
			if (isset($grades[4])) { ////////////////////// this 4 is subject id for English
				if ($grades[4] < 40) {
					$status = false;
				}
			}
			else{
				$status = false;
			}
		}
	}
	else{
		$status = false;
	}

	return $status;
}

function has_passed_bool1($studentId)
{
	global $db;
	global $form;
	global $term;
	global $acayear;
	global $group;

	$sql_check = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear'");
	$status = true;
	$count_passed = 0;
		
	$grades = [];
	while ($row = $sql_check->fetchArray()) {
		$grades[$row['subject']] = $row['score'];
		if ($form > 2) {
			if ((int)$row['score'] >= 40) {
				$count_passed += 1;
			}
		}
		else{
			if ((int)$row['score'] >= 40) {
				$count_passed += 1;
			}
		}
	}

	if ($count_passed < 6) {
		$status = false;
	}

	if (count($grades) > 5) {
		if ($form > 2) {
			if (isset($grades[4])) { ////////////////////// this 4 is subject id for English
				if ($grades[4] < 40) {
					$status = false;
				}
			}
			else{
				$status = false;
			}
		}
		else{
			if (isset($grades[4])) { ////////////////////// this 4 is subject id for English
				if ($grades[4] < 40) {
					$status = false;
				}
			}
			else{
				$status = false;
			}
		}
	}
	else{
		$status = false;
	}

	return $status;
}


function aggregate_points($studentId)
{
	global $db;
	global $form;
	global $term;
	global $acayear;
	global $group;

	//get english score
	$total = 0;
	$correction = [];
	$totalBestSix = 0;

	$eng_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND subject = '4' AND `group` = '$group'");
	$eng_data = $eng_sql->fetchArray();
	if ($eng_data) {
		$eng_score = $eng_data['score'];
		$totalBestSix += $eng_score;

		$correction[4] = $eng_score;
		$point = ma_points($eng_score);
		$total += $point;
	}
		
	$sql_check = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND subject != '4' AND `group` = '$group' ORDER BY score DESC");
	$i = 0;
	while ($row = $sql_check->fetchArray()) {
		if ($i < 5) {
			$totalBestSix += $row['score'];
			$point = ma_points($row['score']);
			$total += $point;
		}
		$i += 1;
		$correction[$row['subject']] = $row['score'];
	}

	//the has passed code
	$status = true;
	$coz = "Has passed";
	$count_passed = 0;

	foreach ($correction as $key => $value) {
		if ($form > 2) {
			if ((int)$value >= 40) {
				$count_passed += 1;
			}
		}
		else{
			if ((int)$value >= 40) {
				$count_passed += 1;
			}
		}
	}

	if ($count_passed <6) {
		$status = false;
		$coz = "the student did not pass a minimum of 6 subjects";
	}

	if (count($correction) > 5) {
		if ($form > 2) {
			if (isset($correction[4])) { ////////////////////// this 4 is subject id for English
				if ($correction[4] < 40) {
					$status = false;
					$coz = "the student has failed English which is a key subject";
				}
			}
			else{
				$status = false;
				$coz = "the student did not sit for English exam";
			}
		}
		else{
			if (isset($correction[4])) { ////////////////////// this 4 is subject id for English
				if ($correction[4] <40) {
					$status = false;
					$coz = "the student has failed English which is a key subject";
				}
			}
			else{
				$status = false;
				$coz = "the student did not sit for English exam";
			}
		}
	}
	else{
		$status = false;
		$coz = "the student did not write a minimum of six subjects";
	}
	
	return [$total, $correction, $totalBestSix, $status, $coz];
}

function aggregate_points1($studentId)
{
	global $db;
	global $form;
	global $term;
	global $acayear;
	global $group;

	//get english score
	$total = 0;
	$correction = [];
	$totalBestSix = 0;

	$eng_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND subject = '4'");
	$eng_data = $eng_sql->fetchArray();
	if ($eng_data) {
		$eng_score = $eng_data['score'];
		$totalBestSix += $eng_score;

		$correction[4] = $eng_score;
		$point = ma_points($eng_score);
		$total += $point;
	}
		
	$sql_check = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND subject != '4' ORDER BY score DESC");
	$i = 0;
	while ($row = $sql_check->fetchArray()) {
		if ($i < 5) {
			$totalBestSix += $row['score'];
			$point = ma_points($row['score']);
			$total += $point;
		}
		$i += 1;
		$correction[$row['subject']] = $row['score'];
	}

	//the has passed code
	$status = true;
	$coz = "Has passed";

	$count_passed = 0;

	foreach ($correction as $key => $value) {
		if ($form > 2) {
			if ((int)$value >= 40) {
				$count_passed += 1;
			}
		}
		else{
			if ((int)$value >= 40) {
				$count_passed += 1;
			}
		}
	}

	if ($count_passed < 6) {
		$status = false;
		$coz = "the student did not pass a minimum of 6 subjects";
	}

	if (count($correction) > 5) {
		if ($form > 2) {
			if (isset($correction[4])) { ////////////////////// this 4 is subject id for English
				if ($correction[4] < 40) {
					$status = false;
					$coz = "the student has failed English which is a key subject";
				}
			}
			else{
				$status = false;
				$coz = "the student did not sit for English exam";
			}
		}
		else{
			if (isset($correction[4])) { ////////////////////// this 4 is subject id for English
				if ($correction[4] <40) {
					$status = false;
					$coz = "the student has failed English which is a key subject";
				}
			}
			else{
				$status = false;
				$coz = "the student did not sit for English exam";
			}
		}
	}
	else{
		$status = false;
		$coz = "the student did not write a minimum of six subjects";
	}
	
	return [$total, $correction, $totalBestSix, $status, $coz];
}

function getAverage($array)
{
	$grades = [];
	$i = 0;
	foreach ($array as $key => $value) {
		if ($i < 6) {
			array_push($grades, $value);
		}
		$i += 1;
	}
	return average($grades);
}


function position_in_class($studentId, $subId)
{
	global $db;
	global $form;
	global $term;
	global $acayear;
	global $group;

	$all = 0;
	$sql = $db->query("SELECT * FROM scores WHERE subject = '$subId' AND term = '$term' AND form = '$form' AND year = '$acayear' AND `group` = '$group' ORDER BY score DESC");
	$pos = 0;
	$i = 1;
	while ($row = $sql->fetchArray()) {
		if ($row['student'] == $studentId) {
			$pos = $i;
		}
		$i += 1;
	}

	return $pos."/".($i-1);
}

function score($index, $subId, $studentId)
{
	global $db;
	global $form;
	global $term;
	global $acayear;
	global $group;

	$sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND subject = '$subId' AND term = '$term' AND form = '$form' AND year = '$acayear' AND `group` = '$group'");
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

/**
 * 
 */
class subject
{
	
	function __construct($db, $id)
	{
		$this->db = $db;
		$this->id = $id;

		$sql = $db->query("SELECT * FROM subject WHERE id = '$id' ");
		$this->data = $sql->fetchArray();
	}

	function getName()
	{
		return $this->data['name'];
	}

	function setName($name)
	{
		return $this->db->query("UPDATE subject SET name = '$name' WHERE id = '$id' ");
	}
}

class student
{
	public $name = "";
	public $reg = "";
           
	
	function __construct($db, $id)
	{
		$this->db = $db;
		$this->id = $id;

		$sql = $db->query("SELECT * FROM student WHERE id = '$id' ");
		$this->data = $sql->fetchArray();
		$this->name = $this->data['fullname'];
		$this->reg = $this->data['regnumber'];

	}

	

	function getName()
	{
		return $this->data['fullname'];
	}

	function getReg()
	{
		return $this->data['regnumber'];
	}

function getVillage()
	{
		return $this->data['village'];
	}


function getChurch()
	{
		return $this->data['church'];
	}

function getGuardian()
	{
		return $this->data['guardian'];
	}

function getLamwa()
	{
		return $this->data['lamwa'];
	}





	function setName($name)
	{
		return $this->db->query("UPDATE student SET fullname = '$name' WHERE id = '$id' ");
	}
}

function getAggregate($scores)
{
	$total = 0;
	$correction = [];
	$totalBestSix = 0;

	$status = true;
	$coz = [];

	//check english
	if (isset($scores[4])) {
		if ($scores[4] >= 40) {
			$total += ma_points($scores[4]);
			$totalBestSix += $scores[4];
		}
		else{
			$status = false;
			$total += ma_points($scores[4]);
			$totalBestSix += $scores[4];
			array_push($coz, "did not pass English");
		}
	}
	else{
		$status = false;
		array_push($coz, "did not write English");
		$total += ma_points(0);
		$totalBestSix += 0;
	}

	//get best 6 subjects
	$new_scores = $scores;
	if (isset($new_scores[4])) {
		unset($new_scores[4]);
	}
	//sort the scores
	arsort($new_scores);
	$i = 1;
	foreach ($new_scores as $key => $value) {
		if ($i < 6) {
			$total += ma_points($value);
			$totalBestSix += $value;
		}
		$i += 1;
	}

	//check if has passed 6 subjects
	global $form;

	if ($form > 2) {

		$index = 0;
		foreach ($scores as $key => $value) {
			if ($value >= 40) {
				$index += 1;
			}
		}
	}
	else{
		$index = 0;
		foreach ($scores as $key => $value) {
			if ($value >= 40) {
				$index += 1;
			}
		}
	}

	if ($index < 6) {
		$status = false;
		array_push($coz, "did not pass a minimum of 6 subjects");
	}

	if ($status) {
		$report = "Has passed";
	}
	else{
		$report = "Failed: ".implode(", ", $coz);
	}

	return [$total, $scores, $totalBestSix, $status, $report];
}

function getPosition($scores, $score)
{
	rsort($scores);
	$position = 1;
	$all = count($scores);

	foreach ($scores as $c) {
		if ($c == $score) {
			return $position."/".$all;
		}

		$position += 1;
	}
}

function addExtra($scores, $studentId)
{
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$year = $_SESSION['acayear'];
	$mode = $_SESSION['mode'];
	global $db;

	$read = $db->query("SELECT * FROM registered WHERE student = '$studentId' AND term = '$term' AND form = '$form' AND year = '$year'");
	while ($row = $read->fetchArray()) {
		if (!isset($scores[$row['subject']])) {
			$scores[$row['subject']] = 0;
		}
	}

	return $scores;
}
?>