<?php
session_start();

$db = new sqlite3('../database.db');
require '../teacher/functions.php';

function print_line_data_bottom($number, $name, $regnumber, $points, $scores)
{
	$text = "$number,$name,$regnumber,$points";
	global $subjects;
	$subjects_count = count($subjects);

	
	$cell_width = 180 / $subjects_count;
	$i = 1;
	foreach ($subjects as $subId => $subject_name) {
		if (isset($scores[$subId])) {
			
			$score_mark = $scores[$subId];
		}
		else{
			$score_mark = "-";
		}
		
		$text .= ",$score_mark";
	}
	return $text;
}

if (isset($_SESSION['form'], $_SESSION['term'])) {
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	if ($form < 3) {
		header("location: download_class_junior_all_x.php");
	}

	$sql = $db->query("SELECT * FROM subject");
	$subjects_count = 0;
	$subjects = [];
	$init = "Position,Name,Reg,Aggregate";
	while ($r = $sql->fetchArray()) {
		$subjects_count++;
		$subjects[$r['id']] = $r['name'];
		$init .= ",".substr($r['name'], 0,4);
	}

	$students_name = [];
	$students_reg = [];
	$sql = $db->query("SELECT DISTINCT student, student.fullname, student.regnumber FROM registered JOIN student ON registered.student = student.id WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` != 'OPEN'");
	$enrollment = 0;
	while ($r = $sql->fetchArray()) {
		$students_reg[$r['student']] = $r['regnumber'];
		$students_name[$r['student']] = trim($r['fullname']);
		$enrollment += 1;
	}

	$text = [];
	array_push($text, $init);

	if ($enrollment > 0) {
		$mega = [];
		$aggregate_store = [];
		foreach ($students_name as $studentId => $student_name) {
			$calculated = aggregate_points1($studentId);
			$points = $calculated[0];
			$mega[$studentId] = $points;
			$aggregate_store[$studentId] = $calculated;
		}

		asort($mega);


		$i = 1;
		$all_students = count($mega);
		$zatsala = [];
		foreach ($mega as $key => $val) {
    		$studentId = $key;
			$student_name = $students_name[$key];
			$regnumber = $students_reg[$key];

			$calculated = $aggregate_store[$studentId];
			$total_count = count($calculated[1]);
			
			
			if ($total_count < 6) {
				$zatsala[$studentId] = $calculated[0];
			}
			else{
				if ($calculated[3] == true) {
					$inner_text = "$i,$student_name,$regnumber,{$calculated[0]},";
					array_push($text, print_line_data_bottom($i, $student_name, $regnumber, $calculated[0], $calculated[1]));
					$i += 1;
				}
				else{
					$zatsala[$studentId] = $calculated[0];
				}	
			}
		}
		
		$osamaliza_mayeso = [];
		$zeros = [];

		foreach ($zatsala as $key => $val) {
    		$studentId = $key;
			$student_name = $students_name[$key];
			$regnumber = $students_reg[$key];

			$calculated = $aggregate_store[$studentId];
			$total_count = count($calculated[1]);

			if ($total_count < 6) {
				$osamaliza_mayeso[$key] = $val;
			}
			else{
				if ($val == 0) {
					$zeros[$key] = $val;
				}
				else{
					
					array_push($text, print_line_data_bottom($i, $student_name, $regnumber, $calculated[0], $calculated[1]));
					$i += 1;
				}
			}
		}

		foreach ($osamaliza_mayeso as $key => $val) {
    		$studentId = $key;
			$student_name = $students_name[$key];
			$regnumber = $students_reg[$key];

			$calculated = $aggregate_store[$studentId];
			$total_count = count($calculated[1]);

			array_push($text, print_line_data_bottom($i, $student_name, $regnumber, $calculated[0], $calculated[1]));
			$i += 1;			
		}

		foreach ($zeros as $key => $val) {
    		$studentId = $key;

			$student_name = $students_name[$key];
			$regnumber = $students_reg[$key];

			$calculated = $aggregate_store[$studentId];
			$total_count = count($calculated[1]);

			array_push($text, print_line_data_bottom($i, $student_name, $regnumber, $calculated[0], $calculated[1]));
			$i += 1;
			
		}	
	}
	$file = "form $form.csv";
	file_put_contents($file, implode("\n", $text));

	header("location: $file");
}