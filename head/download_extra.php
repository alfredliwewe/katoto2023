<?php
session_start();

$db = new sqlite3("../database.db");
require '../teacher/functions.php';
require 'libs/fpdf.php';
require '../objects.php';
require '../config.php';

$system = new System($db);

class PDF extends FPDF{
	// Load data
	function putPaCenter($text)
	{
	    $pageWidth = $this->GetPageWidth();
	    $this->SetFillColor(102, 255, 102);
	    $this->Cell($pageWidth-20,8,$text, 0, 0, 'C', false);
	}

	function sub_heading($text)
	{
		$pageWidth = $this->GetPageWidth();
	    //$this->SetFillColor(102, 255, 102);
	    $this->Cell($pageWidth-20,8,$text, 'B', 0, 'C', false);
	}


	function print_half_half($text1, $text2)
	{
		$pageWidth = $this->GetPageWidth()-20;
		$forty = ceil((50/100)*$pageWidth);

		//print the rows
		$this->Cell($forty,6,$text1);
		$this->Cell($forty,6,$text2);
	}

	function put_image($filename)
	{
		//shrink the image to 40% of the page
		$pageWidth = $this->GetPageWidth();
		$forty = ceil((10/100)*$pageWidth);
		$dif = $pageWidth - $forty;
		$x_cord = ceil($dif/2);
		$this->Image($filename,$x_cord,null,($forty));
	}

	function print_table_row($array_values)
	{
		$pageWidth = $this->GetPageWidth()-20;
		
		for ($i=0; $i < count($array_values); $i++) { 
			if ($i == 0) {
				$this->Cell(70,7,$array_values[$i],1);
			}
			elseif ($i == (count($array_values) - 1)) {
				$this->Cell(35,7,$array_values[$i],1);
			}
			else{
				$this->Cell(35,7,$array_values[$i],1);
			}
		}
		$this->Ln();
	}

	function print_table_row_border_bottom($array_values)
	{
		$pageWidth = $this->GetPageWidth()-20;
		
		for ($i=0; $i < count($array_values); $i++) { 
			if ($i == 0) {
				$this->Cell(70,7,$array_values[$i],1);
			}
			elseif ($i == (count($array_values) - 1)) {
				$this->Cell(35,7,$array_values[$i],1);
			}
			else{
				$this->Cell(35,7,$array_values[$i],1);
			}
		}
		$this->Ln();
	}

	function print_third($text1, $text2, $text3)
	{
		$pageWidth = $this->GetPageWidth()-20;

		$third = $pageWidth / 3;

		$this->Cell($third,9,$text1);
		$this->Cell($third,9,$text2);
		$this->Cell($third,9,$text3);
		$this->Ln();
	}
}

$years = [];
$r = $db->query("SELECT * FROM year");
while ($row = $r->fetchArray()) {
	$years[$row['id']] = $row['name'];
}


$subjects = [];
$subject_scores = [];
$r = $db->query("SELECT * FROM subject");
while ($row = $r->fetchArray()) {
	$subjects[$row['id']] = $row['name'];
	$subject_scores[$row['id']] = [];
}

$term = $_SESSION['term'];
$form = $_SESSION['form'];
$group = $_SESSION['group'];
$year = $_SESSION['acayear'];
$mode = $_SESSION['mode'];

$exam_name = $db->query("SELECT * FROM exams WHERE id = '$mode' ")->fetchArray()['name'];


$sql_year = $db->query("SELECT * FROM year WHERE id = '$year' ");
$year_data = $sql_year->fetchArray();
$fees = $year_data['fees'];
$uniform = $year_data['uniform'];
$year_name = $year_data['name'];

//get stamp
$sql = $db->query("SELECT * FROM stamps WHERE term = '$term' AND year = '$year' ");
$stamp_data = $sql->fetchArray();
if ($stamp_data) {
	$stamp = "../img/".$stamp_data['file'];
}

$pdf = new PDF();

$mega = [];

$student_names = [];
$regnumbers = [];
$scores_store = [];
$aggregate_store = [];



if ($form > 2) {
	
               
	$read = $db->query("SELECT DISTINCT student, student.regnumber,student.guardian,student.lamwa,student.church,student.village, student.fullname FROM extra_scores INNER JOIN student ON extra_scores.student = student.id WHERE form = '$form' AND term = '$term' AND year = '$year' AND exam = '$mode' ");
	while ($row = $read->fetchArray()) {
		$studentId = $row['student'];
		$student_names[$row['student']] = $row['fullname'];
		$regnumbers[$row['student']] = $row['regnumber'];
        $student_village[$row['student']] = $row['village'];                            
        $student_church[$row['student']] = $row['church'];
        $student_guardian[$row['student']] = $row['guardian'];   
        $student_lamwa[$row['student']] = $row['lamwa'];  

		if ($group != "OPEN" AND $group != "EVENING") {
			if(strpos(strtolower($row['regnumber']), "op")){
			}
			else{
				$scores = [];
				$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$studentId' AND exam = '$mode' ");
				while ($d = $r->fetchArray()) {
					$scores[$d['subject']] = $d['score'];
					array_push($subject_scores[$d['subject']], $d['score']);
				}
				//count the scores
				if (count($scores) < 6) {
					$scores = addExtra($scores, $studentId);
				}
				$scores_store[$studentId] = $scores;
				$calculated = getAggregate($scores);
				$mega[$studentId] = $calculated[0];
				$aggregate_store[$studentId] = $calculated;
			}
		}
		else{
			if(strpos(strtolower($row['regnumber']), "op")){
				$scores = [];
				$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$studentId' AND exam = '$mode' ");
				while ($d = $r->fetchArray()) {
					$scores[$d['subject']] = $d['score'];
				}
				if (count($scores) < 6) {
					$scores = addExtra($scores, $studentId);
				}
				$scores_store[$studentId] = $scores;
				$calculated = getAggregate($scores);
				$mega[$studentId] = $calculated[0];
				$aggregate_store[$studentId] = $calculated;
			}
		}
	}
	asort($mega);
}
else{
	$read = $db->query("SELECT DISTINCT student,student.regnumber,student.guardian,student.lamwa,student.church,student.village,student.fullname FROM extra_scores INNER JOIN student ON extra_scores.student = student.id WHERE form = '$form' AND term = '$term' AND year = '$year' AND exam = '$mode' ");
	while ($row = $read->fetchArray()) {
		$studentId = $row['student'];
		$student_names[$row['student']] = $row['fullname'];   
		$regnumbers[$row['student']] = $row['regnumber'];
        $student_village[$row['student']] = $row['village'];
        $student_church[$row['student']] = $row['church'];
        $student_guardian[$row['student']] = $row['guardian'];   
        $student_lamwa[$row['student']] = $row['lamwa'];                                  
                          
		if ($group != "OPEN" AND $group != "EVENING") {
			if(strpos(strtolower($row['regnumber']), "op")){
			}
			else{
				$scores = [];
				$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$studentId' AND exam = '$mode' ");
				while ($d = $r->fetchArray()) {
					$scores[$d['subject']] = $d['score'];
					array_push($subject_scores[$d['subject']], $d['score']);
				}
				if (count($scores) < 6) {
					$scores = addExtra($scores, $studentId);
				}
				$scores_store[$studentId] = $scores;
				$calculated = getAggregate($scores);
				$mega[$studentId] = $calculated[2];
				$aggregate_store[$studentId] = $calculated;
			}
		}
		else{
			if(strpos(strtolower($row['regnumber']), "op")){
				$scores = [];
				$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$studentId' AND exam = '$mode' ");
				while ($d = $r->fetchArray()) {
					$scores[$d['subject']] = $d['score'];
				}
				if (count($scores) < 6) {
					$scores = addExtra($scores, $studentId);
				}
				$scores_store[$studentId] = $scores;
				$calculated = getAggregate($scores);
				$mega[$studentId] = $calculated[2];
				$aggregate_store[$studentId] = $calculated;
			}
		}
	}
	arsort($mega);
}


$all_students = count($mega);

$i = 1;
$failed = [];
$previous = 0;
$pre_pos = 1;
foreach ($mega as $key => $value) {
	$calculated = $aggregate_store[$key];
	if ($calculated[3]) {
		$status = "PASS";
		if($value == $previous){
				
		}
		else{
			$pre_pos = $i;
		}
		require 'page_extra.php';
		//echo "<tr><td>$i</td><td>".$student_names[$key]."</td><td>$value</td><td>".$regnumbers[$key]."</td><td>$status</td></tr>";
		$i += 1;
		$previous = $value;
	}
	else{
		$failed[$key] = $value;
		$status = "FAIL";
	}

}

$osamaliza_mayeso = [];
foreach ($failed as $key => $value) {
	$calculated = $aggregate_store[$key];
	if (count($calculated[1]) > 5) {
		if($value == $previous){
				
		}
		else{
			$pre_pos = $i;
		}
		require 'page_extra.php';
		//echo "<tr><td>$i</td><td>".$student_names[$key]."</td><td>$value</td><td>".$regnumbers[$key]."</td><td>FAIL</td></tr>";
		$i += 1;
		$previous = $value;
	}
	else{
		$osamaliza_mayeso[$key] = $value;
	}
}

foreach ($osamaliza_mayeso as $key => $value) {
	$calculated = $aggregate_store[$key];
	if($value == $previous){
				
	}
	else{
		$pre_pos = $i;
	}
	require 'page_extra.php';
	//echo "<tr><td>$i</td><td>".$student_names[$key]."</td><td>$value</td><td>".$regnumbers[$key]."</td><td>FAIL</td></tr>";
	$i += 1;
	$previous = $value;
}

$pdf->Output();
?>